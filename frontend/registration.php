<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$username = $email = $password = $confirm_password = $address = $gender = $mobile = "";
$usernameErr = $emailErr = $passwordErr = $confirm_passwordErr = $addressErr = $genderErr = $mobileErr = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    // Validate full name

    if (empty($_POST["full_name"])) {
        $fullnameErr = "Full name is required";
    } else {
        $full_name = test_input($_POST["full_name"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        if (!preg_match("/^(?=.*\d)(?=.*[A-Z])(?=.*\W).{8,}$/", $password)) {
            $passwordErr = "Password must contain at least 8 characters, including one uppercase letter, one digit, and one special character.";
        }
    }

    // Validate confirm password
    if (empty($_POST["confirm_password"])) {
        $confirm_passwordErr = "Please confirm password";
    } else {
        $confirm_password = test_input($_POST["confirm_password"]);
        if ($confirm_password !== $password) {
            $confirm_passwordErr = "Password did not match";
        }
    }

    // Validate address
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    // Validate gender
    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = test_input($_POST["gender"]);
        if (!in_array($gender, ["Male", "Female", "Other"])) {
            $genderErr = "Invalid gender selected";
        }
    }

    // Validate mobile number
    if (empty($_POST["mobile"])) {
        $mobileErr = "Mobile number is required";
    } else {
        $mobile = test_input($_POST["mobile"]);
        if (!preg_match("/^\d{10}$/", $mobile)) {
            $mobileErr = "Invalid mobile number format";
        }
    }

    // Add an error message if confirm password error exists
    if (!empty($confirm_passwordErr)) {
        $errorMsg = $confirm_passwordErr;
    }

    // Check if username or email already exists
    $query = "SELECT * FROM students WHERE username=? OR email=?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errorMsg = "Username or email already exists";
    } else {
        // Generate OTP
        $otp = generateOTP();
        if (empty($usernameErr) && empty($emailErr) && empty($fullnameErr) && empty($passwordErr) && empty($confirm_passwordErr) && empty($addressErr) && empty($genderErr) && empty($mobileErr)) {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into database
            $insertQuery = "INSERT INTO students (full_name, username, email, password, address, gender, mobile_number) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($insertQuery);
            $stmt->bind_param("sssssss", $full_name, $username, $email, $hashedPassword, $address, $gender, $mobile);

            if ($stmt->execute()) {
                $studentId = $stmt->insert_id;
                $_SESSION['student_id'] = $studentId;
                // Store the email in the session
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $username;
                // Insert OTP into database
                $insertOtpQuery = "INSERT INTO otps (student_id, otp) VALUES (?, ?)";
                $stmt = $db->prepare($insertOtpQuery);
                $stmt->bind_param("is", $studentId, $otp);
                if ($stmt->execute()) {
                    // Send OTP via email
                    $subject = "Account Confirmation OTP";
                    $message = "
                        <!DOCTYPE php>
                        <php lang='en'>
                        <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <head>
                        <title>APEX Institute Account Confirmation</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f2f2f2;
                                color: #333;
                            }
                            .container {
                                width: 80%;
                                margin: 0 auto;
                                padding: 20px;
                                background-color: #fff;
                                border-radius: 5px;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            }
                            h1 {
                                color: #007bff;
                            }
                            p {
                                line-height: 1.5;
                            }
                        </style>
                        </head>
                        <body>
                            <div class='container'>
                                <h1>Account Confirmation OTP</h1>
                                <p>Dear $username,</p>
                                <p>Your OTP for APEX Institute account confirmation is: <b>$otp</b>. Please enter this OTP on the APEX Institute website to confirm your account.</p>
                                <p>Best Regards,</p>
                                <p>APEX Institute</p>
                            </div>
                        </body>
                        </php>
                    ";
                    if (sendEmail($email, $subject, $message)) {
                        echo '<script>window.location.href = "verification.php";</script>';
                    } else {
                        $errorMsg = "Failed to send OTP. Please try again later.";
                    }
                } else {
                    $errorMsg = "Failed to insert OTP into database.";
                }
            } else {
                $errorMsg = "" . $stmt->error;
            }
        }
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateOTP()
{
    global $db;

    // Generate OTP
    $otp = mt_rand(100000, 999999);

    // Check if the OTP already exists in the database
    $query = "SELECT COUNT(*) AS count FROM otps WHERE otp=?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    // If the OTP already exists, generate a new one recursively
    if ($count > 0) {
        return generateOTP(); // Recursively call the function to generate a new OTP
    }

    return $otp;
}

function sendEmail($to, $subject, $message)
{
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';

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
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->isHTML(true);

    if ($mail->send()) {
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>APEX Institute of Management Services | Registration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">

    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">


    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">


</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
        <a class="navbar-brand" href="index.php">    <img src="images/apexlogo.png" alt="APEX Logo" style="height: 100px; width: auto;">      </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="course.php" class="nav-link">Courses</a></li>
                    <li class="nav-item"><a href="teacher.php" class="nav-link">Teachers</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>

                    <?php
                    if (isset($_SESSION["student_id"])) {
                        // If student is logged in
                        echo '<li class="nav-item"><a href="../student/dashboard.php" class="nav-link">LMS</a></li>';
                        echo '<li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>';
                    } else {
                        // If student is not logged in
                        echo '<li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>';
                        echo '<li class="nav-item active cta"><a href="registration.php" class="nav-link"><span>Register Now!</span></a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->

    <div class="container mt-5 mb-5">
        <div class="card">
            <div class="row">
                <!-- Form half -->
                <div class="col-md-6">
                    <div class="card-body">
                        <h2 class="card-title mt-4">Registration</h2>
                        <!-- Display errors using Bootstrap alerts -->
                        <?php if (!empty($errorMsg)) : ?>
                            <div class="alert alert-danger mt-4"><?php echo $errorMsg; ?></div>
                        <?php endif; ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mt-4" id="registrationForm">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="full_name">Full Name:</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select name="gender" class="form-control" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mobile">Mobile Number:</label>
                                <input type="text" name="mobile" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password:</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="password-icon"><i id="password-feedback" class=""></i></span>
                                    </div>
                                </div>
                                <!-- Password requirements -->
                                <small class="form-text text-danger">
                                    Password must contain at least 8 characters, including one uppercase letter, one digit, and one special character.
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password:</label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="confirm-password-icon"><i id="confirm-password-feedback" class=""></i></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <div class="container mt-3">
                                <p>Already have an account? <a href="login.php">Please Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Image half -->
                <div class="col-md-6 image-container"></div>
            </div>
        </div>
    </div>


    <footer class="ftco-footer ftco-bg-dark ftco-section img" style="background-image: url(images/bg_2.jpg); background-attachment:fixed;">
        <div class="overlay"></div>
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="ftco-footer-widget mb-4">
                    <a class="navbar-brand" href="index.php">    <img src="images/apexlogo.png" alt="APEX Logo" style="height: 100px; width: auto;">      </a>
                    <p>Apex Institute Management Services (AIMS) is a renowned training and educational partner established in 2007, serving Sri Lanka and global clients. It caters to various student demographics, including school leavers, graduates, and professionals in managerial positions.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ftco-footer-widget mb-4 ml-md-4">
                        <h2 class="ftco-heading-2">Site Links</h2>
                        <ul class="list-unstyled">
                            <li><a href="index.php" class="py-2 d-block">Home</a></li>
                            <li><a href="about.php" class="py-2 d-block">About</a></li>
                            <li><a href="course.php" class="py-2 d-block">Courses</a></li>
                            <li><a href="teacher.php" class="py-2 d-block">Teachers</a></li>
                            <li><a href="contact.php" class="py-2 d-block">Contact Us</a></li>

                            <?php
                            if (isset($_SESSION["student_id"])) {
                                // If student is logged in
                                echo '<li><a href="../student/dashboard.php" class="py-2 d-block">LMS</a></li>';
                                echo '<li><a href="logout.php" class="py-2 d-block">Logout</a></li>';
                            } else {
                                // If student is not logged in
                                echo '<li><a href="login.php" class="py-2 d-block">Login</a></li>';
                                echo '<li><a href="registration.php" class="py-2 d-block">Register Now!</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Have any Questions?</h2>
                        <div class="block-23 mb-3">
                            <ul>
                                <li><span class="icon icon-map-marker"></span><span class="text">B35 , Wannirasnayakapura, Ja Ela, Gampaha, Sri Lanka</span></li>
                                <li><a href="#"><span class="icon icon-phone"></span><span class="text">033 324 2339</span></a></li>
                                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@apexinstitute.lk</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>
                        Copyright &copy;<script>
                            document.write(new Date().getFullYear());
                        </script> All rights reserved | APEX INSTITUTE MANAGEMENT SERVICES
                    </p>
                </div>
            </div>
        </div>
    </footer>



    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" />
        </svg></div>


    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/jquery.timepicker.min.js"></script>
    <script src="js/scrollax.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.getElementById('password').addEventListener('input', function() {
            let password = this.value;
            let passwordIcon = document.getElementById('password-feedback');

            // Update password strength icon
            if (!password.match(/^(?=.*\d)(?=.*[A-Z])(?=.*\W).{8,}$/)) {
                passwordIcon.className = 'fas fa-times-circle text-danger';
                passwordIcon.setAttribute('title', 'Password must contain at least 8 characters, including one uppercase letter, one digit, and one special character.');
            } else {
                passwordIcon.className = 'fas fa-check-circle text-success';
                passwordIcon.setAttribute('title', 'Password strength: Strong');
            }

            // Check confirm password if it's already entered
            let confirmPassword = document.getElementById('confirm_password').value;
            if (confirmPassword !== '') {
                updateConfirmPasswordFeedback(password, confirmPassword);
            }
        });

        document.getElementById('confirm_password').addEventListener('input', function() {
            let confirmPassword = this.value;
            let password = document.getElementById('password').value;

            // Update confirm password match icon
            if (password !== confirmPassword) {
                document.getElementById('confirm-password-feedback').className = 'fas fa-times-circle text-danger';
                document.getElementById('confirm-password-feedback').setAttribute('title', 'Password did not match');
            } else {
                document.getElementById('confirm-password-feedback').className = 'fas fa-check-circle text-success';
                document.getElementById('confirm-password-feedback').setAttribute('title', 'Password matched');
            }
        });
    </script>


</body>

</html>