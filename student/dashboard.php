<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if student_id is set in session
if (isset($_SESSION["student_id"])) {
    // Retrieve student_id from session
    $student_id = $_SESSION["student_id"];

    // Check if student account is active
    $sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);

    // Redirect to error.php if student account is not active
    if ($row_count != 1) {
        header('location:../frontend/error.php');
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if student_id is not set in session
    header('location:../frontend/login.php');
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
    <title>APEX Institute | Admin Dashboard</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">


</head>

<body class="fix-header">
    <!-- Preloader - style you can find in spinners.css -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php require 'header.php'; ?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php require 'left_sidebar.php'; ?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper  -->
        <div class="page-wrapper" style="height:1200px;">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">APEX Institute | Student Dashboard</h3>
                </div>

            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-md-3">
                        <a href="my_courses.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-graduation-cap f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h4>
                                            <?php
                                            $student_id = $_SESSION['student_id'];
                                            $sql = "SELECT COUNT(*) AS course_count 
                                                                FROM courses 
                                                                WHERE course_id IN 
                                                                    (SELECT course_id 
                                                                    FROM course_enrollments 
                                                                    WHERE student_id = $student_id AND is_active = 1)";
                                            $result = mysqli_query($db, $sql);
                                            $row = mysqli_fetch_assoc($result);
                                            echo $row['course_count'];
                                            ?>
                                        </h4>
                                        <p class="m-b-0">Courses</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="all_payments.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-money f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <p class="m-b-0">Payments</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="enrollments.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-user-plus f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h4>
                                            <?php
                                            $student_id = $_SESSION['student_id'];
                                            $sql = "SELECT COUNT(*) AS enrollment_count 
                                                                FROM course_enrollments 
                                                                WHERE student_id = $student_id AND is_active = 1";
                                            $result = mysqli_query($db, $sql);
                                            $row = mysqli_fetch_assoc($result);
                                            echo $row['enrollment_count'];
                                            ?>
                                        </h4>
                                        <p class="m-b-0">Enrollments</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="chatbot.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-comments  f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">

                                        <p class="m-b-0">Chatbot</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
        </div>
        <!-- End Page wrapper  -->
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

</body>

</html>