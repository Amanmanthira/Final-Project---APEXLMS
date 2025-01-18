<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if (empty($_SESSION["student_id"])) {
    header('location:../frontend/login.php');
}
// Check if student account is active
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
$result = mysqli_query($db, $sql);
$row_count = mysqli_num_rows($result);

// Redirect to error.php if student account is not active
if ($row_count != 1) {
    header('location:../frontend/error.php');
    exit(); // Ensure script execution stops after redirection
}
// Retrieve the course_id from the GET parameters
$course_id = $_GET['course_id'];

// Prepare and execute a query to fetch the course_name based on the course_id
$query = "SELECT course_name FROM courses WHERE course_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->bind_result($course_name);
$stmt->fetch();
$stmt->close();
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>APEX Institute | <?php echo $course_name; ?></title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header fix-sidebar">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <div id="main-wrapper">
        <?php require 'header.php'; ?>
        <?php require 'left_sidebar.php'; ?>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <?php
                    date_default_timezone_set('Asia/Colombo');

                    if (isset($_GET['course_id'])) {
                        $course_id = $_GET['course_id'];

                        $enrollment_sql = "SELECT COUNT(*) as count FROM course_enrollments WHERE student_id = $student_id AND course_id = $course_id AND is_active = 1";
                        $enrollment_query = mysqli_query($db, $enrollment_sql);
                        $enrollment_result = mysqli_fetch_assoc($enrollment_query);
                        $is_enrolled = $enrollment_result['count'] > 0;

                        if ($is_enrolled) {

                            $sql = "SELECT c.*, d.department_name, GROUP_CONCAT(CONCAT(l.first_name, ' ', l.last_name) SEPARATOR ', ') AS lecturers 
                                    FROM courses c 
                                    LEFT JOIN course_lecturers cl ON c.course_id = cl.course_id 
                                    LEFT JOIN lecturers l ON cl.lecturer_id = l.lecturer_id 
                                    LEFT JOIN departments d ON c.department_id = d.department_id 
                                    WHERE c.course_id = $course_id";

                            $query = mysqli_query($db, $sql);

                            if (mysqli_num_rows($query) > 0) {
                                $row = mysqli_fetch_assoc($query);
                                echo '<div class="col-md-12">';
                                echo '<div class="card">';
                                echo '<div class="card-body">';
                                echo '<h6 class="card-subtitle">Course ID: ' . $row['course_id'] . '</h6>';
                                echo '<img src="' . $row['course_image'] . '" class="card-img-top" alt="Course Image"><br><br>';
                                echo '<div class="text-center"><h4 class="card-title"><strong style="font-size: 24px;">' . $row['course_name'] . '</strong></h4></div>';
                                echo '<div class="text-center"><p class="card-text">' . $row['course_description'] . '</p></div>';
                                echo '<p class="card-text">Department: ' . $row['department_name'] . '</p>';
                                echo '<p class="card-text">Start Date: ' . $row['start_date'] . '</p>';
                                echo '<p class="card-text">End Date: ' . $row['end_date'] . '</p>';
                                echo '<p class="card-text">Lecturers: ' . $row['lecturers'] . '</p>';
                                echo '<p class="card-text">Course Fee (LKR): ' . $row['course_fee'] . '</p>';
                                echo '<p class="card-text">Created At: ' . $row['created_at'] . '</p>';
                                echo '<p class="card-text">Updated At: ' . $row['updated_at'] . '</p>';
                                echo ' <a href="course_materials.php?course_id=' . $row['course_id'] . '" class="btn btn-info">Manage Course Materials</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';



                                // Existing logic for fetching topics
                                $sql_topics = "SELECT * FROM topics WHERE course_id = $course_id AND is_active = 1 ORDER BY creation_date ASC";
                                $result_topics = mysqli_query($db, $sql_topics);

                                if (mysqli_num_rows($result_topics) > 0) {
                                    echo '<div class="col-md-12">';
                                    echo '<div class="card mt-3">';
                                    echo '<div class="card-body">';
                                    echo '<h2 class="card-title">Topics</h2>';
                                    echo '<ul class="list-group list-group-flush">';
                                    // Initialize count variable
                                    $count = 1;
                                    while ($row_topic = mysqli_fetch_assoc($result_topics)) {
                                        echo '<li class="list-group-item pl-4"><h4 class="list-group-item-heading">' . $count . '. ' . $row_topic['topic_name'] . '</h4>';
                                        echo '<ul class="pl-3">';

                                        // Fetch content for the current topic
                                        $topic_id = $row_topic['topic_id'];
                                        $sql_content = "SELECT * FROM content WHERE topic_id = $topic_id AND is_active = 1 ORDER BY is_active DESC";
                                        $result_content = mysqli_query($db, $sql_content);

                                        if (mysqli_num_rows($result_content) > 0) {
                                            while ($row_content = mysqli_fetch_assoc($result_content)) {
                                                echo '<li class="mb-3">';

                                                // Determine the appropriate icon for the content type
                                                $icon = '';
                                                switch ($row_content['content_type']) {
                                                    case 'PDF':
                                                        $icon = '<i class="fa fa-file-pdf-o"></i>';
                                                        break;
                                                    case 'Audio':
                                                        $icon = '<i class="fa fa-volume-up"></i>';
                                                        break;
                                                    case 'Video':
                                                        $icon = '<i class="fa fa-file-video-o"></i>';
                                                        break;
                                                    case 'Image':
                                                        $icon = '<i class="fa fa-file-image-o"></i>';
                                                        break;
                                                    default:
                                                        $icon = '<i class="fa fa-file"></i>';
                                                        break;
                                                }

                                                // Display content with clickable link
                                                $content_url = 'show_content.php?content_id=' . $row_content['content_id'];
                                                echo $icon . ' <a href="' . $content_url . '">' . $row_content['content_name'] . '</a>';

                                                $sql_check_attendance = "SELECT status FROM attendance WHERE student_id = ? AND topic_id = ?";
                                                $stmt = $db->prepare($sql_check_attendance);
                                                $stmt->bind_param("ii", $student_id, $topic_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();

                                                if ($row && $row['status'] == 'Present') {
                                                    // Attendance is already marked as "Present"
                                                    echo '<button class="btn btn-success btn-sm float-right ml-2" disabled>Attendance Already Marked</button>';
                                                } else {
                                                    // Proceed with your existing logic to enable or disable the button based on time
                                                    $class_time = $row_content['class_time']; // Assuming 'class_time' is in 'YYYY-MM-DD HH:MM:SS' format
                                                    if (!empty($class_time)) {
                                                        // Format the class time
                                                        $formatted_class_time = date('d M Y, H:i', strtotime($class_time));
                                                        echo ' <span class="text-muted" style="font-size: 0.9em;">(Class Time: ' . $formatted_class_time . ')</span>';
                                                    }

                                                    $current_time = date('Y-m-d H:i:s');
                                                    // Log the raw time strings
                                                    error_log("Debug: Raw Class Time = $class_time, Current Time = $current_time");

                                                    // Convert to timestamps
                                                    $class_time_ts = strtotime($class_time);
                                                    $current_time_ts = strtotime($current_time);

                                                    // Log the timestamps
                                                    error_log("Debug: Class Time Timestamp = $class_time_ts, Current Time Timestamp = $current_time_ts");

                                                    // Calculate the time difference in seconds
                                                    $time_difference = abs($current_time_ts - $class_time_ts);

                                                    // Log the time difference
                                                    error_log("Debug: Time Difference = $time_difference seconds");

                                                    // Check if the current time is within the 60-minute range of the class time
                                                    if ($time_difference <= 3600) {
                                                        // Log the button enabled status
                                                        error_log("Info: Button enabled. Current time is within 60 minutes of class time.");
                                                        // Pass both content_id and topic_id to markAttendance
                                                        echo ' <button onclick="markAttendance(' . $row_content['content_id'] . ', ' . $row_topic['topic_id'] . ')" class="btn btn-success btn-sm float-right ml-2">Mark Attendance</button>';
                                                    } else {
                                                        // Log the button disabled status
                                                        error_log("Info: Button disabled. Current time is outside the 60-minute range of class time.");
                                                        echo ' <button class="btn btn-success btn-sm float-right ml-2" disabled data-toggle="tooltip" data-placement="top" title="Attendance can only be marked within 60 minutes of the class time.">Mark Attendance</button>';
                                                    }
                                                }

                                                echo '</li>';
                                            }
                                        } else {
                                            echo '<li>No content found for this topic.</li>';
                                        }

                                        // Fetch assignments for the current topic
                                        $sql_assignments = "SELECT a.*, ar.resource_type, ar.resource_path 
                            FROM assignments AS a 
                            LEFT JOIN assignment_resources AS ar 
                            ON a.assignment_id = ar.assignment_id 
                            WHERE a.topic_id = $topic_id 
                            AND a.is_active = 1";

                                        $result_assignments = mysqli_query($db, $sql_assignments);

                                        // Check if there are assignments available for the topic
                                        if (mysqli_num_rows($result_assignments) > 0) {
                                            echo '<h4>Assignments</h4>';
                                            echo '<ul class="fa-ul mb-3">';
                                            while ($row_assignment = mysqli_fetch_assoc($result_assignments)) {
                                                echo '<li><span class="fa-li"><i class="fa fa-clipboard"></i></span>';
                                                echo '<a href="show_assignment.php?id=' . $row_assignment['assignment_id'] . '">' . $row_assignment['assignment_name'] . '</a>';
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                        } else {
                                            echo '<p>No assignments found for this topic.</p>';
                                        }

                                        echo '</ul>'; // End nested list for content
                                        echo '</li>'; // End list item for topic
                                        $count++; // Increment count
                                    }

                                    echo '</ul>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="col-md-12">';
                                    echo '<div class="card mt-3">';
                                    echo '<div class="card-body">';
                                    echo '<p>No topics found for this course.</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }

                                // Existing Mark Attendance Button
                                echo '<div class="col-md-12">';
                                echo '</div>';
                            } else {
                                echo '<div class="card">
                                <div class="card-body">
                                    <div class="col-12">
                                        <p>No Course Found</p>
                                    </div>
                                </div>
                            </div>';
                            }
                        } else {
                            echo '<div class="card">
                            <div class="card-body">
                                <div class="col-12">
                                    <p>You are not enrolled in this course</p>
                                </div>
                            </div>
                        </div>';
                        }
                    } else {
                        echo '<div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <p>Course ID not provided</p>
                            </div>
                        </div>
                    </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });

        function markAttendance(contentId, topicId) {
            $.ajax({
                url: 'mark_attendance.php',
                type: 'POST',
                data: {
                    content_id: contentId, // Pass content_id
                    topic_id: topicId // Pass topic_id
                },
                dataType: 'json', // Ensure the response is parsed as JSON
                success: function(response) {
                    console.log(response); // Log the entire response to check for any issues
                    if (response.success) {
                        // Display success message using SweetAlert
                        Swal.fire({
                            title: 'Success!',
                            text: 'Attendance marked successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reload the page after success
                                location.reload();
                            }
                        });
                    } else {
                        // Display error message using SweetAlert
                        const errorMessage = response.message ? response.message : 'An unknown error occurred.';
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error marking attendance: ' + errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log the error response for debugging
                    // Display error message if AJAX fails
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>



</body>

</html>