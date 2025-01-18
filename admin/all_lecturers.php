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

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>APEX Institute  | Admins</title>
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
    <div id="main-wrapper">
        <!-- header header -->
        <?php require('header.php'); ?>
        <!-- Left Sidebar -->
        <?php require('left_sidebar.php'); ?>

        <!-- Page wrapper -->
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">All Lecturers</h4>
                                <h6 class="card-subtitle">List of all Lecturers</h6>
                                <div class="table-responsive m-t-40">
                                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Lecturer ID</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Department</th>
                                                <th>Address</th>
                                                <th>Password</th> <!-- Added Password Column -->
                                                <th>Created Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch and display lecturers
                                            $sql = "SELECT lecturers.*, departments.department_name 
                                                    FROM lecturers 
                                                    LEFT JOIN departments ON lecturers.department = departments.department_id
                                                    WHERE lecturers.is_active = 1";
                                            $query = mysqli_query($db, $sql);

                                            if (!mysqli_num_rows($query) > 0) {
                                                echo '<td colspan="10"><center>No Lecturers Data Found </center></td>';
                                            } else {
                                                while ($rows = mysqli_fetch_array($query)) {
                                                    $status = $rows['is_active'] == 1 ? 'Active' : 'Inactive'; // Determine status text
                                                    
                                                    // If the password is stored in a reversible encryption, decode it here.
                                                    // Example: If it is base64 encoded (Replace with your decoding logic):
                                                    $decoded_password = base64_decode($rows['password']); // Replace with actual decoding logic
                                                    
                                                    echo '
                                                    <tr>
                                                        <td>' . $rows['lecturer_id'] . '</td>
                                                        <td>' . $rows['first_name'] . ' ' . $rows['last_name'] . '</td>
                                                        <td>' . $rows['email'] . '</td>
                                                        <td>' . $rows['phone_number'] . '</td>
                                                        <td>' . $rows['department_name'] . '</td>
                                                        <td>' . $rows['address'] . '</td>
                                                        <td>' . htmlspecialchars($decoded_password) . '</td> <!-- Display Password here -->
                                                        <td>' . $rows['created_date'] . '</td>
                                                        <td>' . $status . '</td>
                                                        <td>
                                                            <a href="edit_lecturer.php?id=' . $rows['lecturer_id'] . '" class="btn btn-primary btn-sm">Edit</a>
                                                            <button class="btn btn-danger btn-sm change-status-btn" data-id="' . $rows['lecturer_id'] . '" data-status="' . $rows['is_active'] . '">Change Status</button>
                                                        </td>
                                                    </tr>';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Lecturer ID</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Department</th>
                                                <th>Address</th>
                                                <th>Password</th>
                                                <th>Created Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Page Content -->
            </div>
        </div>
    </div>
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
    <script src="js/lib/datatables/datatables-init.js"></script>

    <script>
$(document).ready(function() {
    $("#showInactiveLecturersBtn").click(function() {
        $(".inactive-table").toggle();
    });

    // Change status button click event
    $(".change-status-btn").click(function() {
        var lecturerId = $(this).data("id");
        var currentStatus = $(this).data("status");
        var newStatus = currentStatus == 1 ? 0 : 1; // Toggle status

        $.ajax({
            url: "change_lecturer_status.php",
            type: "POST",
            data: {
                lecturer_id: lecturerId,
                new_status: newStatus
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    location.reload(); // Reload the page to reflect changes
                } else {
                    console.error(result.message);
                }
            }
        });
    });
});
</script>


    
</body>

</html>
