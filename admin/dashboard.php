<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
// Check if user is logged in
if (!isset($_SESSION['apex_admin_id'])) {
    // Redirect to the login page or display an error message
    header("Location: index.php");
    exit();
} else {
    $admin_id = $_SESSION['apex_admin_id'];

    // Fetch the admin name from the database
    $query = "SELECT apex_admin_name FROM admin WHERE apex_admin_id = '$admin_id'";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $admin_name = $row['apex_admin_name'];
    } else {
        // Destroy session and redirect to login if admin is not found
        session_destroy();
        header("Location: index.php");
        exit();
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
        <!-- Page wrapper  -->
        <div class="page-wrapper" style="height:1200px;">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">APEX Institute | Admin Dashboard</h3>
                </div>

            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">

                    <div class="col-md-3">
                        <a href="all_students.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-child f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h2>
                                            <?php
                                            $sql = "select * from students";
                                            $result = mysqli_query($db, $sql);
                                            $rws = mysqli_num_rows($result);
                                            echo $rws;
                                            ?>
                                        </h2>
                                        <p class="m-b-0">Students</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="all_departments.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-sitemap f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h2>
                                            <?php
                                            $sql = "select * from departments";
                                            $result = mysqli_query($db, $sql);
                                            $rws = mysqli_num_rows($result);
                                            echo $rws;
                                            ?>
                                        </h2>
                                        <p class="m-b-0">Departments</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="all_lecturers.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-user-circle-o f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h2>
                                            <?php
                                            $sql = "select * from lecturers";
                                            $result = mysqli_query($db, $sql);
                                            $rws = mysqli_num_rows($result);
                                            echo $rws;
                                            ?>
                                        </h2>
                                        <p class="m-b-0">Lecturers</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="all_courses.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-graduation-cap f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h2>
                                            <?php
                                            $sql = "select * from courses";
                                            $result = mysqli_query($db, $sql);
                                            $rws = mysqli_num_rows($result);
                                            echo $rws;
                                            ?>
                                        </h2>
                                        <p class="m-b-0">Courses</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="messages.php">
                            <div class="card p-30">
                                <div class="media">
                                    <div class="media-left meida media-middle">
                                        <span><i class="fa fa-envelope f-s-40" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="media-body media-text-right">
                                        <h2>
                                            <?php
                                            // SQL query to count the number of messages
                                            $sql = "SELECT COUNT(*) AS message_count FROM contact";
                                            $result = mysqli_query($db, $sql);
                                            $row = mysqli_fetch_assoc($result);
                                            $message_count = $row['message_count'];
                                            echo $message_count;
                                            ?>
                                        </h2>
                                        <p class="m-b-0">Messages</p>
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
                                        <h5>LKR
                                            <?php
                                            $sql = "
                            SELECT 
                                'Full Payment' AS payment_type,
                                SUM(payment_amount) AS total_payments
                            FROM 
                                course_full_payments
                            WHERE 
                                payment_status = 'Confirmed'
                                
                            UNION
                                
                            SELECT 
                                'Installment Payment' AS payment_type,
                                SUM(installment_amount) AS total_payments
                            FROM 
                                course_installment_payments
                            WHERE 
                                installment_payment_status = 'Confirmed';
                        ";

                                            $result = mysqli_query($db, $sql);

                                            // Fetch the result row
                                            $row = mysqli_fetch_assoc($result);

                                            // Output the total payment
                                            echo $row['total_payments'];


                                            ?>
                                        </h5>
                                        <p class="m-b-0">Payments</p>
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