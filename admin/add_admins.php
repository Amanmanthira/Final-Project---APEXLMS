<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['apex_admin_id'])) {
    header("Location: index.php");
    exit();
} else {
    $admin_id = $_SESSION['apex_admin_id'];
    $query = "SELECT apex_admin_name FROM admin WHERE apex_admin_id = '$admin_id'";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $admin_name = $row['apex_admin_name'];
    } else {
        session_destroy();
        header("Location: index.php");
        exit();
    }
}

// Handle form submission for adding new admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = mysqli_real_escape_string($db, $_POST['admin_name']);
    $password = mysqli_real_escape_string($db, $_POST['admin_password']); // Password input from form
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Check if admin name already exists
    $check_query = "SELECT * FROM admin WHERE apex_admin_name = '$admin_name'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Admin name already exists. Please choose a different name.";
    } else {
        // Check if the same hashed password already exists
        $check_password_query = "SELECT * FROM admin WHERE apex_admin_password = '$hashed_password'";
        $check_password_result = mysqli_query($db, $check_password_query);

        if (mysqli_num_rows($check_password_result) > 0) {
            $error_message = "This password is already in use. Please choose a different password.";
        } else {
            // Insert the new admin
            $query = "INSERT INTO admin (apex_admin_name, apex_admin_password) VALUES ('$admin_name', '$hashed_password')";
            if (mysqli_query($db, $query)) {
                header("Location: all_admins.php"); // Redirect to all_admins.php after successful addition
                exit(); // Stop further execution
            } else {
                $error_message = "Error: Could not add admin. " . mysqli_error($db);
            }
        }
    }
}

?>
<head>
    <meta charset="utf-8">
    <title>APEX Institute | Add Admin</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Ensure sidebar is clickable */
        #sidebar {
            position: fixed;
            z-index: 1000; /* Higher than other elements */
            pointer-events: auto;
        }

        /* Adjust main content so it doesn't overlap with the sidebar */
        .page-wrapper {
            margin-left: 250px; /* Adjust based on sidebar width */
        }

        /* Ensure that form/card doesn't block sidebar */
        .card {
            z-index: 1;
            position: relative;
        }
    </style>
</head>
<body>
<div id="main-wrapper">
    <?php require('header.php'); ?>
    <?php require('left_sidebar.php'); ?>

    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Add New Admin Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add New Admin</h4>
                            <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="admin_name">Admin Name</label>
                                    <input type="text" name="admin_name" id="admin_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="admin_password">Password</label>
                                    <input type="password" name="admin_password" id="admin_password" class="form-control" required>
                                </div>
                                <button type="submit" name="add_admin" class="btn btn-primary">Add Admin</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Admins Table -->
           
        </div>
    </div>
</div>

<script src="js/lib/jquery/jquery.min.js"></script>
<script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
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
</body>
</html>
