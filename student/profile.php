<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
session_start();
error_reporting(0);

// Check if student_id is set in session
if(isset($_SESSION["student_id"])) {
    // Retrieve student_id from session
    $student_id = $_SESSION["student_id"];
    $username = $_SESSION['username'];

    // Check if student account is active
    $sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);
    $student = mysqli_fetch_assoc($result);

    // Redirect to error.php if student account is not active
    if($row_count != 1) {
        header('location:../frontend/error.php');
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if student_id is not set in session
    header('location:../frontend/login.php');
    exit(); // Ensure script execution stops after redirection
}

// Check if form is submitted to update student details
if(isset($_POST['update'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $mobile_number = $_POST['mobile_number'];

    // Server-side validation for full name (letters and spaces only)
    if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $errorMsg = "Full name can only contain letters and spaces.";
    } 
    // Server-side validation for email
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } 
    // Server-side validation for address (alphanumeric, spaces, and basic punctuation)
    else if (!preg_match("/^[a-zA-Z0-9\s,.'-]{3,}$/", $address)) {
        $errorMsg = "Invalid address format.";
    } 
    // Server-side validation for mobile number (only 10 digits)
    else if (!preg_match('/^\d{10}$/', $mobile_number)) {
        $errorMsg = "Mobile number must be exactly 10 digits.";
    } 
    // Server-side validation for gender
    else if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errorMsg = "Invalid gender value.";
    } 
    else {
        // Check if email already exists for another student
        $emailQuery = "SELECT * FROM students WHERE email = '$email' AND id != '$student_id'";
        $emailResult = mysqli_query($db, $emailQuery);
        if(mysqli_num_rows($emailResult) > 0) {
            $errorMsg = "The email address is already in use by another student.";
        } else {
            // Check if mobile number already exists for another student
            $mobileQuery = "SELECT * FROM students WHERE mobile_number = '$mobile_number' AND id != '$student_id'";
            $mobileResult = mysqli_query($db, $mobileQuery);
            if(mysqli_num_rows($mobileResult) > 0) {
                $errorMsg = "The mobile number is already in use by another student.";
            } else {
                // Update student details in the database
                $updateQuery = "UPDATE students SET full_name = '$full_name', email = '$email', address = '$address', gender = '$gender', mobile_number = '$mobile_number' WHERE id = '$student_id'";
                $updateResult = mysqli_query($db, $updateQuery);

                if ($updateResult) {
                    // Fetch updated details
                    $sql = "SELECT * FROM students WHERE id = '$student_id'";
                    $result = mysqli_query($db, $sql);
                    $student = mysqli_fetch_assoc($result);

                    $successMsg = "Profile updated successfully.";
                } else {
                    $errorMsg = "Error occurred while updating profile. Please try again.";
                }
            }
        }
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
        <svg class="circular" viewBox="25 25 50 50">
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
                                <h4 class="card-title">Profile</h4>
                                <h6 class="card-subtitle">Student ID: <b><?php echo $student_id; ?></b></h6>
                                <h6 class="card-subtitle">Username: <b><?php echo $username; ?></b></h6>

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
                                                <label for="full_name">Full Name</label>
                                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $student['full_name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $student['address']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select class="form-control" id="gender" name="gender">
                                                    <option value="Male" <?php echo $student['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                                    <option value="Female" <?php echo $student['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                                    <option value="Other" <?php echo $student['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="mobile_number">Mobile Number</label>
                                                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $student['mobile_number']; ?>" pattern="\d{10}" title="Please enter a valid 10-digit mobile number" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary" name="update">Update Profile</button>
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
