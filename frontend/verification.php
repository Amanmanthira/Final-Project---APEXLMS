<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$otp = $otpErr = $successMsg = $errorMsg = "";

// Retrieve student ID from query parameters
if (isset($_GET['student_id'])) {
  $_SESSION['student_id'] = $_GET['student_id'];
}

// Initialize $email and $username variables
$email = "";
$username = "";

// Check if email and username are set in session
if (isset($_SESSION['email'])) {
    // Assign email from session to $email variable
    $email = $_SESSION['email'];
} else {
    $email = "undefined";
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['username'])) {
    // Assign username from session to $username variable
    $username = $_SESSION['username'];
} else {
    $username = "undefined";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["otp"])) {
      $otpErr = "OTP is required";
  } else {
      $otp = test_input($_POST["otp"]);
      if (isset($_SESSION['student_id'])) {
          $studentId = $_SESSION['student_id'];
          // Using parameterized query to prevent SQL injection
          $query = "SELECT * FROM otps WHERE student_id=? AND otp=?";
          $stmt = $db->prepare($query);
          $stmt->bind_param("is", $studentId, $otp);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result->num_rows > 0) {
              // Delete OTP entry from the database
              $deleteQuery = "DELETE FROM otps WHERE student_id=?";
              $stmt = $db->prepare($deleteQuery);
              $stmt->bind_param("i", $studentId);
              if ($stmt->execute()) {
                  // OTP successfully deleted
                  $updateQuery = "UPDATE students SET is_active='1' WHERE id=?";
                  $stmt = $db->prepare($updateQuery);
                  $stmt->bind_param("i", $studentId);
                  if ($stmt->execute()) {
                      $successMsg = "Account verified successfully!";
                      // Send email confirming successful account verification
                      $subject = "APEX Institute Account Verification Successful";
                      // Email 
                      $message = "
                      <!DOCTYPE html>
                      <html lang='en'>
                      <head>
                      <meta charset='UTF-8'>
                      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                      <title>APEX Institute Account Verification Complete</title>
                      <style>
                        /* CSS styles */
                        body {
                          font-family: Arial, sans-serif;
                          background-color: #f2f2f2;
                          color: #333;
                          margin: 0;
                          padding: 0;
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
                          <h1>APEX Institute Account Verification</h1>
                          <p>Dear $username,</p>
                          <p>Your APEX Institute account has been successfully verified. You can now access all the features of our platform.</p>
                          <p>Best regards,<br>APEX Institute</p>
                        </div>
                      </body>
                      </html>
                      "; 
                      sendEmail($email, $subject, $message);
                      // Redirect to login page after 2 seconds
                      echo "<script>setTimeout(function() { window.location.href = 'login.php'; }, 2000);</script>";
                  } else {
                      $errorMsg = "Error: Unable to update account status";
                  }
              } else {
                  $errorMsg = "Error: Unable to delete OTP";
              }
          } else {
              $otpErr = "Invalid OTP";
          }
      } else {
          // If no student ID found in session, redirect back to registration page
          $errorMsg = "No student ID found. Please register again.";
          echo "<script>setTimeout(function() { window.location.href = 'registration.php'; }, 2000);</script>";
          exit;
      }
  }
  // Destroy session data after completing OTP verification
  session_destroy();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function sendEmail($to, $subject, $message) {
  require 'PHPMailer/src/PHPMailer.php';
  require 'PHPMailer/src/SMTP.php';
  require 'PHPMailer/src/Exception.php';

  $mail = new PHPMailer();
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->Port = 465;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->SMTPAuth = true;
  $mail->Username = 'EMAIL HERE'; 
  $mail->Password = 'APP PASSWORD'; 
  $mail->setFrom('EMAIL HERE', 'APEX INSTITUTE');
  $mail->addReplyTo('EMAIL HERE', 'APEX INSTITUTE');
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
    <title>APEX Institute of Management Services | Account Verification </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
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
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">


  </head>
  <body>
    
  <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="flaticon-university"></i>APEX <br><small>INSTITUTE</small></a>
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
          if(isset($_SESSION["student_id"])) {
              // If student is logged in
              echo '<li class="nav-item"><a href="../student/dashboard.php" class="nav-link">LMS</a></li>';
              echo '<li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>';
          } else {
              // If student is not logged in
              echo '<li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>';
              echo '<li class="nav-item cta"><a href="registration.php" class="nav-link"><span>Register Now!</span></a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>
    <!-- END nav -->
    
    <!-- OTP Verification Form -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                    <h2 class="card-title mt-4">OTP Verification</h2>
                    <h6 class="card-subtitle mt-4">OTP has been sent to your email (<?php echo $email; ?>). Please check your email and enter it in the below form.</h2>
                     <!-- Display errors using Bootstrap alerts -->
                     <?php if (!empty($errorMsg)) : ?>
                        <div class="alert alert-danger mt-4"><?php echo $errorMsg; ?></div>
                     <?php endif; ?>
                     <form method="post" action="verification.php" class="mt-4" id="otpForm">
                      <div class="form-group">
                          <label for="otp">Enter OTP:</label>
                          <input type="text" name="otp" class="form-control" required>
                          <?php if (!empty($otpErr)) : ?>
                              <span class="text-danger"><?php echo $otpErr; ?></span>
                          <?php endif; ?>
                      </div>
                      <?php if (!empty($successMsg)) : ?>
                          <p class="text-success mt-2"><?php echo $successMsg; ?></p>
                          <p class="text-info">You will be redirected shortly...</p>
                      <?php endif; ?>
                      <button type="submit" class="btn btn-primary">Verify OTP</button>
                  </form>
                </div>
                </div>
            </div>
        </div>
    </div>


    <footer class="ftco-footer ftco-bg-dark ftco-section img" style="background-image: url(images/bg_2.jpg); background-attachment:fixed;">
    	<div class="overlay"></div>
      <div class="container">
        <div class="row mb-5">
          <div class="col-md-4">
            <div class="ftco-footer-widget mb-4">
              <h2><a class="navbar-brand" href="index.php"><i class="flaticon-university"></i>APEX <br><small>INSTITUTE</small></a></h2>
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
    if(isset($_SESSION["student_id"])) {
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
	                <li><span class="icon icon-map-marker"></span><span class="text">456 Sri Jayawardenepura Mawatha, Colombo 07, Sri Lanka</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">+2 392 3929 210</span></a></li>
	                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@apexinstitute.lk</span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <p>
  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | APEX INSTITUTE MANAGEMENT SERVICES
  </p>
          </div>
        </div>
      </div>
    </footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


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
 
  
  </body>
</html>