<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if lecturer_id is set in session
if(isset($_SESSION["apex_lecturer_id"])) {
    // Retrieve lecturer_id from session
    $lecturer_id = $_SESSION["apex_lecturer_id"];

    // Check if lecturer account is active
    $sql = "SELECT * FROM lecturers WHERE lecturer_id = '$lecturer_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);

    // Redirect to error.php if lecturer account is not active
    if($row_count != 1) {
        header('location: error.php');
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if lecturer_id is not set in session
    header('location: index.php');
    exit(); // Ensure script execution stops after redirection
}

// Retrieve the course_id from the GET parameters
$course_id = $_GET['course_id'];

// Check if the lecturer is assigned to the course
$query = "SELECT * FROM course_lecturers WHERE course_id = ? AND lecturer_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $course_id, $lecturer_id);
$stmt->execute();
$stmt->store_result();
$row_count = $stmt->num_rows;
$stmt->close();

if ($row_count != 1) {
    // Redirect to my_courses.php if the lecturer is not assigned to the course
    header('location: my_courses.php');
    exit(); // Ensure script execution stops after redirection
}
// Function to generate a unique token for a resource path and store it in a session
function generateResourceToken($resource_path) {
    // Generate a unique token for the resource path
    $token = md5(uniqid($resource_path, true));
    
    // Store the token and its corresponding resource path in the session
    $_SESSION['resource_tokens'][$token] = $resource_path;
    
    // Return the generated token
    return $token;
}

// Function to fetch assignment details from the database
function getAssignmentDetailsFromDatabase($assignment_id, $db) {
    $sql = "SELECT * FROM assignments WHERE assignment_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $assignment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Fetch assignment details from the result set
        $row = $result->fetch_assoc();
        return $row;
    } else {
        // No assignment found for the given assignment ID
        return false;
    }
}

// Check if assignment ID is provided in the URL
if (isset($_GET['id'])) {
    // Fetch assignment details from the database based on the assignment ID
    $assignment_id = $_GET['id'];

    // Call the function to get assignment details
    $assignmentDetails = getAssignmentDetailsFromDatabase($assignment_id, $db);

    // Check if assignment details are retrieved successfully
    if ($assignmentDetails) {
        // Extract assignment details
        $assignment_name = $assignmentDetails['assignment_name'];
        $assignment_description = $assignmentDetails['assignment_description'];
        $due_date = $assignmentDetails['due_date'];

        // Create the card HTML with assignment details
        $contentCard = '<div class="col-lg-12">';
        $contentCard .= '<div class="card">';
        $contentCard .= '<div class="card-body">';
        $contentCard .= "<h4 class='card-title'><i class='fa fa-tasks'></i> $assignment_name</h4>";
        $contentCard .= "<p class='card-text'><strong>Description:</strong> $assignment_description</p>";
        $contentCard .= "<p class='card-text'><strong>Due Date:</strong> $due_date</p>";

        // Fetch associated resources for the assignment
        $sql = "SELECT * FROM assignment_resources WHERE assignment_id = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $assignment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Display associated resources
        if ($result->num_rows > 0) {
            $contentCard .= "<h5 class='card-title'><i class='fa fa-file'></i> Resources</h5>";
            $contentCard .= "<ul class='card-text'>";
            while ($row = $result->fetch_assoc()) {
                $resource_type = $row['resource_type'];
                $resource_path = $row['resource_path'];
                $icon_class = getResourceIcon($resource_type);
                $resource_token = generateResourceToken($resource_path);
                $contentCard .= "<li><i class='$icon_class'></i> <a href='resource.php?token=$resource_token&course_id=$course_id'>" . basename($resource_path) . "</a></li>";
            }
            $contentCard .= "</ul>";
        } else {
            $contentCard .= "<p class='card-text'>No resources available for this assignment.</p>";
        }
        
        $contentCard .= '<br>'; // Adding a line break

            // Fetch submissions for the assignment
            $sql = "SELECT s.full_name, a.student_id, a.submission_date, a.submission_path
            FROM assignment_submissions a
            INNER JOIN students s ON a.student_id = s.id
            WHERE a.assignment_id = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "i", $assignment_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if any submissions are found
            if ($result->num_rows > 0) {
            // Display submissions in a table
            $contentCard .= "<h5 class='card-title'><i class='fa fa-tasks'></i> Submissions</h5>";
            $contentCard .= "<div class='table-responsive'>";
            $contentCard .= "<table id='example23' class='display nowrap table table-hover table-striped table-bordered' cellspacing='0' width='100%'>";
            $contentCard .= "<thead>";
            $contentCard .= "<tr>";
            $contentCard .= "<th>Student ID</th>";
            $contentCard .= "<th>Student Name</th>";
            $contentCard .= "<th>Submission Date</th>";
            $contentCard .= "<th>Submission File</th>";
            $contentCard .= "<th>Mark</th>";
            $contentCard .= "<th>Action</th>";
            $contentCard .= "</tr>";
            $contentCard .= "</thead>";
            $contentCard .= "<tbody>";

            // Loop through each submission
            while ($row = $result->fetch_assoc()) {
            $student_id = $row['student_id'];
            $student_name = $row['full_name'];
            $submission_date = $row['submission_date'];
            $submission_path = $row['submission_path'];

            // Fetch mark for the submission
            $mark_query = "SELECT mark FROM marks WHERE student_id = ? AND assignment_id = ?";
            $mark_stmt = mysqli_prepare($db, $mark_query);
            mysqli_stmt_bind_param($mark_stmt, "ii", $student_id, $assignment_id);
            mysqli_stmt_execute($mark_stmt);
            $mark_result = mysqli_stmt_get_result($mark_stmt);
            $mark_row = mysqli_fetch_assoc($mark_result);
            $mark = ($mark_row) ? $mark_row['mark'] : "Not graded yet";

            // Display submission details in a table row
            $contentCard .= "<tr>";
            $contentCard .= "<td>$student_id</td>";
            $contentCard .= "<td>$student_name</td>";
            $contentCard .= "<td>$submission_date</td>";
            $contentCard .= "<td><a href='$submission_path' target='_blank'>View Submission</a></td>";
            $contentCard .= "<td>$mark</td>";
            $contentCard .= "<td><button class='btn btn-danger btn-sm set-mark' data-student-id='$student_id' data-student-name='$student_name' data-assignment-id='$assignment_id' data-toggle='modal' data-target='#setMarkModal'>Set Mark</button></td>";
            $contentCard .= "</tr>";
            }

            $contentCard .= "</tbody>";
            $contentCard .= "</table>";
            $contentCard .= "</div>";
            } else {
            // If no submissions found, display a message
            $contentCard .= "<p class='card-text'>No submissions available for this assignment.</p>";
            }


        // Close card body and card div
        $contentCard .= '</div>';
        $contentCard .= '</div>';
        $contentCard .= '</div>';
    } else {
        // If assignment not found, display error message
        $contentCard = "<div class='col-lg-6'><p>Assignment not found.</p></div>";
    }
}

// Function to get the icon class based on the resource type
function getResourceIcon($resource_type) {
    switch ($resource_type) {
        case 'PDF':
            return 'fa fa-file-pdf-o';
        case 'Video':
            return 'fa fa-file-video-o';
        case 'Image':
            return 'fa fa-file-image-o';
        case 'Audio':
            return 'fa fa-volume-up';
        case 'Zip':
            return 'fa fa-file-archive-o';
        default:
            return 'fa fa-file';
    }
}
?>




<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>APEX Institute  | Show Assignment </title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
    <!--[if lt IE 9]>
    <script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header fix-sidebar">
    <!-- Preloader - style you can find in spinners.css -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php
        // Require the header.php file
        require('header.php');
        ?>
        <!-- End header header -->
       <!-- Left Sidebar  -->
       <?php
        // Require the header.php file
        require('left_sidebar.php');
        ?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper -->
    <div class="page-wrapper">
        <!-- Page content -->
        <div class="container-fluid">
               <!-- Start Page Content -->
               <div class="row">
                    <?php echo $contentCard; ?>
                <!-- Modal for setting marks -->
                <div class="modal fade" id="setMarkModal" tabindex="-1" role="dialog" aria-labelledby="setMarkModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="setMarkModalLabel">Set Mark for <span id="modalStudentId"></span> - <span id="modalStudentName"></span></h5>
                        </div>
                            <div class="modal-body">
                                <!-- Form to set marks -->
                                <form id="setMarkForm">
                                    <div class="form-group">
                                        <label for="mark">Mark:</label>
                                        <input type="number" class="form-control" id="mark" name="mark" required>
                                    </div>
                                    <input type="hidden" id="markStudentId">
                                    <input type="hidden" id="markAssignmentId">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-secondary" onclick="reloadPage()">Close</button>
                                        <!-- Hidden containers for success and error messages -->
                                        <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                                            <span id="successMessageText"></span>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                                            <span id="errorMessageText"></span>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
        </div>
    </div>
</div>


       
        <!-- End Container fluid -->
    </div>
    <!-- End Page wrapper -->
    </div>
    <!-- End Wrapper -->
    <!-- All Jquery -->
    <script src="js/lib/jquery/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="js/jquery.slimscroll.js"></script>
    <!--Menu sidebar -->
    <script src="js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <!--Custom JavaScript -->
    <script src="js/custom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.337/pdf.min.js"></script>

    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="js/lib/datatables/datatables-init.js"></script>
   
    <script>
        // Reload the page function
    function reloadPage() {
            location.reload();
        }
        $(document).ready(function() {
        // Listen for click on set-mark button
        $('.set-mark').click(function() {
            var studentId = $(this).data('student-id');
            var studentName = $(this).data('student-name');
            var assignmentId = $(this).data('assignment-id');
            $('#modalStudentId').text(studentId);
            $('#modalStudentName').text(studentName);
            $('#markStudentId').val(studentId);
            $('#markStudentName').val(studentName);
            $('#markAssignmentId').val(assignmentId);
        });

        // Handle form submission
        $('#setMarkForm').submit(function(event) {
            event.preventDefault(); // Prevent default form submission
            var mark = $('#mark').val();
            var studentId = $('#markStudentId').val();
            var studentName = $('#markStudentName').val();
            var assignmentId = $('#markAssignmentId').val();

            // Retrieve the course_id from the GET parameters
            var urlParams = new URLSearchParams(window.location.search);
            var courseId = urlParams.get('course_id');
            var url = 'set_mark.php?course_id=' + courseId;

            //  AJAX request to set mark 
            
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    mark: mark,
                    studentId: studentId,
                    studentName: studentName,
                    assignmentId: assignmentId
                },
                success: function(response) {
                    if (response.startsWith("success")) {
                        // Show success message
                        $('#successMessageText').text(response.substring(8));
                        $('#successMessage').show();
                        // Close the modal
                        $('#setMarkModal').modal('hide');
                        // Reload the page after 2 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // 2000 milliseconds = 2 seconds
                    } else {
                        // Show error message
                        $('#errorMessageText').text(response.substring(6));
                        $('#errorMessage').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    $('#errorMessageText').text('Error adding content: ' + errorMessage);
                    $('#errorMessage').show();
                }
            });
            
            // Close the modal after form submission
            $('#setMarkModal').modal('hide');
        });
    });
    </script>
</body>

</html>
