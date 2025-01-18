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
$error = '';
$success = '';

// Fetch lecturer details from the database
if (isset($_GET['id'])) {
    $lecturer_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM lecturers WHERE lecturer_id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $lecturer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $lecturer = mysqli_fetch_assoc($result);

    if (!$lecturer) {
        // Redirect or display an error message if lecturer not found
        header("Location: all_lecturers.php");
        exit();
    }

    // Assign lecturer details to variables
    $lecturer_first_name = $lecturer['first_name'];
    $lecturer_last_name = $lecturer['last_name'];
    $email = $lecturer['email'];
    $phone_number = $lecturer['phone_number'];
    $department_id = $lecturer['department'];
    $address = $lecturer['address'];

}
 // Fetch departments from the database
 $query = "SELECT * FROM departments";
 $result = mysqli_query($db, $query);

 $departments = [];
 while ($row = mysqli_fetch_assoc($result)) {
     $departments[] = $row;
 }

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update lecturer details
    $lecturer_first_name = mysqli_real_escape_string($db, $_POST['lecturer_first_name']);
    $lecturer_last_name = mysqli_real_escape_string($db, $_POST['lecturer_last_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone_number = mysqli_real_escape_string($db, $_POST['phone_number']);
    $department_id = $_POST['department_id'];
    $address = mysqli_real_escape_string($db, $_POST['address']);
    $lecturer_id = $_POST['lecturer_id'];


    // Check if the generate password checkbox is checked
    if (isset($_POST['generate_password']) && $_POST['generate_password'] === 'on') {
        // Generate a new password
        $password = bin2hex(random_bytes(4));
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Include the password in the update query
        $update_query = "UPDATE lecturers SET first_name = ?, last_name = ?, email = ?, phone_number = ?, department = ?, address = ?, password = ? WHERE lecturer_id = ?";
        $stmt = mysqli_prepare($db, $update_query);
        mysqli_stmt_bind_param($stmt, "sssssssi", $lecturer_first_name, $lecturer_last_name, $email, $phone_number, $department_id, $address, $hashed_password, $lecturer_id);
    } else {
        // Exclude the password from the update query
        $update_query = "UPDATE lecturers SET first_name = ?, last_name = ?, email = ?, phone_number = ?, department = ?, address = ? WHERE lecturer_id = ?";
        $stmt = mysqli_prepare($db, $update_query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $lecturer_first_name, $lecturer_last_name, $email, $phone_number, $department_id, $address, $lecturer_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Lecturer details updated successfully.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error updating lecturer details: ' . mysqli_error($db) . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
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
    <title>APEX Institute | Edit Lecturer </title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header">
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
         <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Lecturers</h3>
                </div>
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-outline-primary">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Edit Lecturer</h4>
                            </div>
                            <br>
                            <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="form-group">
                                <label for="lecturer_first_name">First Name</label>
                                <input type="text" class="form-control" id="lecturer_first_name" name="lecturer_first_name" value="<?php echo $lecturer_first_name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lecturer_last_name">Last Name</label>
                                <input type="text" class="form-control" id="lecturer_last_name" name="lecturer_last_name" value="<?php echo $lecturer_last_name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select class="form-control" id="department_id" name="department_id" required>
                                    <option value="" disabled>Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department['department_id']; ?>" <?php echo ($department['department_id'] == $department_id) ? 'selected' : ''; ?>><?php echo $department['department_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Phone</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" pattern="[0-9]{10}" value="<?php echo $phone_number; ?>" required>
                                <small class="form-text text-muted">Please enter a 10-digit phone number.</small>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" id="generate_password" name="generate_password">
                                <label for="generate_password">Generate Password</label>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" required>
                            </div>
                            <input type="hidden" name="lecturer_id" value="<?php echo $lecturer_id; ?>">
                            <button type="submit" class="btn btn-primary">Update Lecturer</button>
                            <?php if (!empty($error)): ?>
                                <?php echo $error; ?>
                            <?php endif; ?>
                            <?php if (!empty($success)): ?>
                                <?php echo $success; ?>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = "all_lecturers.php";
                                    }, 2000);
                                </script>
                            <?php endif; ?>
                        </form>
                            </div>
                        </div>
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
