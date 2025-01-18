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

// Get topic_id and content_id from the URL parameters (GET request)
$topic_id = isset($_GET['topic_id']) ? $_GET['topic_id'] : null;
$content_id = isset($_GET['content_id']) ? $_GET['content_id'] : null;
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>APEX Institute | Admin Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="css/lib/datatables/datatables.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <div id="main-wrapper">
        <?php
        // Include the header and left sidebar
        require('header.php');
        require('left_sidebar.php');
        ?>

        <!-- Page wrapper -->
        <div class="page-wrapper" style="height:1200px;">
            <!-- Container fluid -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">

                    <div class="col-md-12">
                        <div class="card border-white">
                            <div class="card-body">
                                <h2 class="text-center">Attendance Details</h2>
                                <table id="attendanceTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Attendance ID</th>
                                            <th>Student Name</th>
                                            <th>Course Name</th>
                                            <th>Topic Name</th>
                                            <th>Content Name</th>
                                            <th>Attendance Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Start building the query
                                        $sql = "SELECT a.attendance_id, a.student_id, a.course_id, a.topic_id, a.content_id, a.attendance_date, a.status, 
                            s.full_name AS student_name, c.course_name, t.topic_name, con.content_name 
                            FROM attendance a
                            JOIN students s ON a.student_id = s.id
                            JOIN courses c ON a.course_id = c.course_id
                            JOIN topics t ON a.topic_id = t.topic_id
                            JOIN content con ON a.content_id = con.content_id";

                                        // If topic_id or content_id is provided, add conditions to the query
                                        if ($topic_id) {
                                            $sql .= " WHERE a.topic_id = '$topic_id'";
                                        }
                                        if ($content_id) {
                                            // If topic_id is also set, use AND, otherwise just WHERE
                                            $sql .= ($topic_id ? " AND " : " WHERE ") . "a.content_id = '$content_id'";
                                        }

                                        // Execute the query
                                        $result = mysqli_query($db, $sql);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['attendance_id'] . "</td>";
                                                echo "<td>" . $row['student_name'] . "</td>";
                                                echo "<td>" . $row['course_name'] . "</td>";
                                                echo "<td>" . $row['topic_name'] . "</td>";
                                                echo "<td>" . $row['content_name'] . "</td>";
                                                echo "<td>" . $row['attendance_date'] . "</td>";
                                                echo "<td>" . $row['status'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>No attendance records found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Page Content -->
            </div>
            <!-- End Container fluid -->
        </div>
        <!-- End Page wrapper -->
    </div>

    <!-- All Jquery -->
    <script src="js/lib/jquery/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="js/lib/datatables/datatables.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="js/jquery.slimscroll.js"></script>
    <!--Menu sidebar -->
    <script src="js/sidebarmenu.js"></script>
    <!-- stickey kit -->
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="js/custom.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#attendanceTable').DataTable();
        });
    </script>

</body>

</html>