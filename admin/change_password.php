<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
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

$admin_id = $_SESSION['apex_admin_id']; // Fetch the admin ID from the session

// Function to validate password strength
function validatePassword($password) {
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    return preg_match($pattern, $password);
}

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Retrieve form data
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    // Fetch the current password from the database
    $query = "SELECT apex_admin_password FROM admin WHERE apex_admin_id = '$admin_id'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    $hashedPassword = $row['apex_admin_password'];

    // Verify current password
    if(password_verify($currentPassword, $hashedPassword)) {
        // Check if new password is not the same as the current password
        if(password_verify($newPassword, $hashedPassword)) {
            $errorMsg = "New password cannot be the same as the current password.";
        } else {
            // Check if new password matches the confirm password and meets the strength criteria
            if($newPassword === $confirmNewPassword) {
                if(validatePassword($newPassword)) {
                    // Hash the new password
                    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update the new password in the database
                    $updateQuery = "UPDATE admin SET apex_admin_password = '$newHashedPassword' WHERE apex_admin_id = '$admin_id'";
                    $updateResult = mysqli_query($db, $updateQuery);

                    if($updateResult) {
                        // Password successfully updated, display success message
                        $successMsg = "Password updated successfully. Logging out...";
                        // Log out after setting the new password
                        session_destroy();
                        echo '<script>
                                setTimeout(function() {
                                    window.location.href = "index.php";
                                }, 2000);
                            </script>';
                    } else {
                        // Error occurred while updating password, handle as needed (e.g., display error message)
                        $errorMsg = "Error occurred while updating password. Please try again.";
                    }
                } else {
                    // Password does not meet the strength criteria
                    $errorMsg = "New password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
                }
            } else {
                // New password and confirm new password do not match
                $errorMsg = "New password and confirm new password do not match.";
            }
        }
    } else {
        // Current password is incorrect
        $errorMsg = "Current password is incorrect.";
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
    <title>APEX Institute | Change Password</title>
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
        <svg class="circular" view="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php require 'header.php'; ?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php require 'left_sidebar.php'; ?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper -->
        <div class="page-wrapper">
            <!-- Page content -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Change Password</h4>
                                <h6 class="card-subtitle">Admin ID: <b><?php echo $admin_id; ?></b></h6>
                                <h6 class="card-subtitle">Username: <b><?php echo $admin_name; ?></b></h6>

                                <!-- Bootstrap alert for error message -->
                                <?php if(isset($errorMsg) && !empty($errorMsg)): ?>
                                <div class="alert alert-danger mt-4" role="alert">
                                    <?php echo $errorMsg; ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Bootstrap alert for success message -->
                                <?php if(isset($successMsg) && !empty($successMsg)): ?>
                                <div class="alert alert-success mt-4" role="alert">
                                    <?php echo $successMsg;
                                    ?>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <form method="POST">
                                        <div class="form-group">
                                            <label for="current_password">Current Password</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_new_password">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="submit">Change Password</button>
                                    </form>
                                </div>
                            </div>
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
