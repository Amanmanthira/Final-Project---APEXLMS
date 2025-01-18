<!DOCTYPE html>
<html lang="en">
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

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

// Fetch departments list from the database
$departments_query = "SELECT * FROM departments";
$departments_result = mysqli_query($db, $departments_query);
$departments = mysqli_fetch_all($departments_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_first_name = mysqli_real_escape_string($db, $_POST['lecturer_first_name']);
    $lecturer_last_name = mysqli_real_escape_string($db, $_POST['lecturer_last_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone_number = mysqli_real_escape_string($db, $_POST['phone_number']);
    $department_id = $_POST['department_id'];
    $address = mysqli_real_escape_string($db, $_POST['address']);

    if (isset($_POST['generate_password']) && $_POST['generate_password'] === 'on') {
        $password = bin2hex(random_bytes(4)); // Generate a random password
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_query = "SELECT * FROM lecturers WHERE email = '$email'";
    $check_result = mysqli_query($db, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Lecturer with this email already exists.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    } else {
        if (empty($lecturer_first_name) || empty($lecturer_last_name) || empty($email) || empty($hashed_password) || empty($phone_number) || empty($department_id) || empty($address)) {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        All fields are required!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Invalid email format.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif (!preg_match('/^\d{10}$/', $phone_number)) {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Phone number must be 10 digits.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif (strlen($address) < 5) {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Address must be at least 5 characters long.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {
            $insert_query = "INSERT INTO lecturers (first_name, last_name, email, password, phone_number, department, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $insert_query);
            mysqli_stmt_bind_param($stmt, "sssssss", $lecturer_first_name, $lecturer_last_name, $email, $hashed_password, $phone_number, $department_id, $address);

            if (mysqli_stmt_execute($stmt)) {
                $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                Lecturer added successfully.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                
                // Send email using PHPMailer
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 465;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->SMTPAuth = true;
                $mail->Username = 'amanmanthira32326@gmail.com'; 
                $mail->Password = 'cboa qnsj iuln mrqy'; 
                $mail->setFrom('amanmanthira32326@gmail.com', 'APEX INSTITUTE'); 
                $mail->addReplyTo('amanmanthira32326@gmail.com', 'APEX INSTITUTE'); 
                $mail->addAddress($email);
                $mail->Subject = 'Lecturer Account Details';
                $mail->Body = "Dear $lecturer_first_name,<br><br>Your lecturer account has been created successfully.<br>Email: $email<br>Password: $password<br><br>Regards,<br>APEX Institute";
                $mail->isHTML(true);

                if (!$mail->send()) {
                    $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error sending email: ' . $mail->ErrorInfo . '
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                }
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error adding lecturer: ' . mysqli_error($db) . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
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
    <title>APEX Institute | Add Lecturer </title>
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
                                <h4 class="m-b-0 text-white">Add Lecturer</h4>
                            </div>
                            <br>
                            <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                <div class="form-group">
                                    <label for="lecturer_first_name">First Name</label>
                                    <input type="text" class="form-control" id="lecturer_first_name" name="lecturer_first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="lecturer_last_name">Last Name</label>
                                    <input type="text" class="form-control" id="lecturer_last_name" name="lecturer_last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="department_id">Department</label>
                                    <select class="form-control" id="department_id" name="department_id" required>
                                        <option value="" disabled selected>Select Department</option>
                                        <?php foreach ($departments as $department): ?>
                                            <option value="<?php echo $department['department_id']; ?>"><?php echo $department['department_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Phone</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" pattern="[0-9]{10}" required>
                                    <small class="form-text text-muted">Please enter a 10-digit phone number.</small>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" id="generate_password" name="generate_password" required>
                                    <label for="generate_password">Generate Password</label>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Lecturer</button>
                                <?php if (!empty($error)): ?>
                                    <?php echo $error; ?>
                                <?php endif; ?>
                                <?php if (!empty($success)): ?>
                                    <?php
                                    echo $success; 
                                    echo '<script>';
                                    echo 'setTimeout(function() {';
                                    echo '    window.location.href = "all_lecturers.php";';
                                    echo '}, 2000);';
                                    echo '</script>';
                                    ?>
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
