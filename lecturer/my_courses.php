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
    <title>APEX Institute  | My Courses</title>
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
              <!-- Page content -->
        <div class="container-fluid">
               <!-- Start Page Content -->
               <div class="row">
                <?php
                // Assuming session is already started and $db is the database connection
                $lecturer_id = $_SESSION["apex_lecturer_id"];

                // SQL query to get courses only for the lecturer in the session
                $sql = "SELECT c.*, d.department_name, GROUP_CONCAT(CONCAT(l.first_name, ' ', l.last_name) SEPARATOR ', ') AS lecturers 
                        FROM courses c 
                        LEFT JOIN course_lecturers cl ON c.course_id = cl.course_id 
                        LEFT JOIN lecturers l ON cl.lecturer_id = l.lecturer_id 
                        LEFT JOIN departments d ON c.department_id = d.department_id 
                        WHERE c.is_active = 1 AND c.course_id IN (
                            SELECT course_id FROM course_lecturers WHERE lecturer_id = $lecturer_id
                        )
                        GROUP BY c.course_id";
                $query = mysqli_query($db, $sql);

                if (mysqli_num_rows($query) > 0) {
                    while ($row = mysqli_fetch_assoc($query)) {
                        echo '<div class="col-md-4">';
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<img src="' . $row['course_image'] . '" class="card-img-top" alt="Course Image">';
                        echo '<h4 class="card-title">' . $row['course_name'] . '</h4>';
                        echo '<h6 class="card-subtitle">Course ID: ' . $row['course_id'] . '</h6>';
                        echo '<p class="card-text">' . substr($row['course_description'], 0, 20) . '...</p>'; // Shortened description
                        echo '<p class="card-text">Department: ' . $row['department_name'] . '</p>';
                        echo '<p class="card-text">Start Date: ' . $row['start_date'] . '</p>';
                        echo '<p class="card-text">End Date: ' . $row['end_date'] . '</p>';
                        echo '<p class="card-text">Lecturers: ' . $row['lecturers'] . '</p>';
                        echo '<p class="card-text">Course Fee (LKR):' . $row['course_fee'] . '</p>';
                        echo '<p class="card-text">Created At: ' . $row['created_at'] . '</p>';
                        echo '<p class="card-text">Updated At: ' . $row['updated_at'] . '</p>';
                        echo '<a href="view_course.php?course_id=' . $row['course_id'] . '" class="btn btn-sm btn-info ml-2 mt-2">View Course</a>';
                        echo '<a href="enrollments.php?course_id=' . $row['course_id'] . '" class="btn btn-sm btn-dark ml-2 mt-2">Enrollments</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="col-12"><p>No Courses Found</p></div>';
                }
                ?>
            </div>
            <!-- End Page Content -->
            <div id="alert-container"></div>

             <!-- Button to show inactive courses -->
             <button id="showInactiveBtn" class="btn btn-info">Show Inactive Courses</button>

<!-- Container to display inactive courses -->
<div id="inactiveCoursesContainer" style="display: none;">
<br>
<h1>Inactive Courses</h1>
    <?php
    // Fetch and display inactive courses
    $inactiveSql = "SELECT c.*, d.department_name, GROUP_CONCAT(CONCAT(l.first_name, ' ', l.last_name) SEPARATOR ', ') AS lecturers 
    FROM courses c 
    LEFT JOIN course_lecturers cl ON c.course_id = cl.course_id 
    LEFT JOIN lecturers l ON cl.lecturer_id = l.lecturer_id 
    LEFT JOIN departments d ON c.department_id = d.department_id 
    WHERE c.is_active = 0 AND c.course_id IN (
        SELECT course_id FROM course_lecturers WHERE lecturer_id = $lecturer_id
    )
    GROUP BY c.course_id";
    $inactiveQuery = mysqli_query($db, $inactiveSql);

    if (mysqli_num_rows($inactiveQuery) > 0) {
        while ($inactiveRow = mysqli_fetch_assoc($inactiveQuery)) {
            echo '<div class="col-md-4">';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<img src="' . $inactiveRow ['course_image'] . '" class="card-img-top" alt="Course Image">';
            echo '<h4 class="card-title">' . $inactiveRow ['course_name'] . '</h4>';
            echo '<h6 class="card-subtitle">Course ID: ' . $inactiveRow ['course_id'] . '</h6>';
            echo '<p class="card-text">' . substr($inactiveRow ['course_description'], 0, 20) . '...</p>'; // Shortened description
            echo '<p class="card-text">Department: ' . $inactiveRow ['department_name'] . '</p>';
            echo '<p class="card-text">Start Date: ' . $inactiveRow ['start_date'] . '</p>';
            echo '<p class="card-text">End Date: ' . $inactiveRow ['end_date'] . '</p>';
            echo '<p class="card-text">Lecturers: ' . $inactiveRow['lecturers'] . '</p>';
            echo '<p class="card-text">Course Fee (LKR): ' . $inactiveRow['course_fee'] . '</p>';
            echo '<p class="card-text">Created At: ' . $inactiveRow['created_at'] . '</p>';
            echo '<p class="card-text">Updated At: ' . $inactiveRow['updated_at'] . '</p>';
            echo '<a href="view_course.php?course_id=' . $inactiveRow['course_id'] . '" class="btn btn-sm btn-info ml-2 mt-2">View Course</a>';
            echo '<a href="enrollments.php?course_id=' . $inactiveRow['course_id'] . '" class="btn btn-sm btn-dark ml-2 mt-2">Enrollments</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="col-12"><p>No Inactive Courses Found</p></div>';
    }
    ?>
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
        document.getElementById("showInactiveBtn").addEventListener("click", function() {
            var inactiveCoursesContainer = document.getElementById("inactiveCoursesContainer");
            if (inactiveCoursesContainer.style.display === "none") {
                inactiveCoursesContainer.style.display = "block";
            } else {
                inactiveCoursesContainer.style.display = "none";
            }
        });
        // Add this script to handle AJAX delete requests
        $(document).ready(function () {
            $('.delete-course').on('click', function () {
                var courseId = $(this).data('course-id');
                var card = $(this).closest('.col-md-4');
                $.ajax({
                    url: 'delete_course.php',
                    type: 'POST',
                    data: { course_id: courseId },
                    success: function (response) {
                        if (response == 'success') {
                            card.remove();
                            showAlert('Course deleted successfully.', 'success');
                        } else {
                            showAlert('Failed to delete course. Please try again.', 'danger');
                        }
                    },
                    error: function () {
                        showAlert('An error occurred. Please try again.', 'danger');
                    }
                });
            });
        });
       // Function to show Bootstrap alerts
            function showAlert(message, type) {
                var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                                message +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                                '</button>' +
                                '</div>';
                $('#alert-container').html(alertHtml);

                // Timeout to reload the page after 3 seconds
                setTimeout(function() {
                    location.reload();
                }, 3000);
            }

    </script>

    
</body>

</html>