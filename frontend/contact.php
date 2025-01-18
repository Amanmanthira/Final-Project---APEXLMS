<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if student_id is set in session
if (isset($_SESSION["student_id"])) {
  // Retrieve student_id from session
  $student_id = $_SESSION["student_id"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>APEX Institute of Management Services | Contact</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

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
          <li class="nav-item active"><a href="contact.php" class="nav-link">Contact</a></li>

          <?php
          if (isset($_SESSION["student_id"])) {
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

  <div class="hero-wrap hero-wrap-2" style="background-image: url('images/bg_2.jpg'); background-attachment:fixed;">
    <div class="overlay"></div>
    <div class="container">
      <div class="row no-gutters slider-text align-items-center justify-content-center" data-scrollax-parent="true">
        <div class="col-md-8 ftco-animate text-center">
          <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Contact</span></p>
          <h1 class="mb-3 bread">Contact Us</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="ftco-section contact-section ftco-degree-bg">
    <div class="container">
      <div class="row d-flex mb-5 contact-info">
        <div class="col-md-12 mb-4">
          <h2 class="h4">Contact Information</h2>
        </div>
        <div class="w-100"></div>
        <div class="col-md-3">
          <p><span>Address:</span>B35 , Wannirasnayakapura, Ja Ela, Gampaha, Sri Lanka</p>
        </div>
        <div class="col-md-3">
          <p><span>Phone:</span> <a href="tel://23923929210">033 324 2339</a></p>
        </div>
        <div class="col-md-3">
          <p><span>Email:</span> <a href="mailto:info@yoursite.com">info@apexinstitute.lk</a></p>
        </div>
        <div class="col-md-3">
          <p><span>Website</span> <a href="apexinstitute.lk">apexinstitute.lk</a></p>
        </div>
      </div>
      <div class="row block-9">
        <div class="col-md-6 pr-md-5">
          <h4 class="mb-4">Do you have any questions?</h4>
          <form action="contact_handler.php" method="post">
            <div class="form-group">
              <input type="text" class="form-control" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
              <input type="email" class="form-control" name="email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="subject" placeholder="Subject" required>
            </div>
            <div class="form-group">
              <textarea name="message" cols="30" rows="7" class="form-control" placeholder="Message" required></textarea>
            </div>
            <div class="form-group">
              <input type="submit" value="Send Message" class="btn btn-primary py-3 px-5">
            </div>
          </form>


        </div>
      </div>
    </div>
    </div>
  </section>



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
$(document).ready(function() {
  // Handle form submission
  $("form").on("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Serialize form data
    var formData = $(this).serialize();
    var $form = $(this);

    // Perform AJAX request
    $.ajax({
      url: 'contact_handler.php',
      type: 'POST',
      data: formData,
      success: function(response) {
        // Display success message and clear the form
        showAlert('success', response);
        $form[0].reset(); // Clear the form fields
      },
      error: function(xhr, status, error) {
        // Display error message
        showAlert('danger', 'An error occurred: ' + error);
      }
    });
  });

  // Function to show Bootstrap alerts
  function showAlert(type, message) {
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                      '<strong>' + (type === 'success' ? 'Success!' : 'Error!') + '</strong> ' + message +
                      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                      '</button>' +
                    '</div>';
    // Append alert inside the form
    $('form').prepend(alertHtml);
  }
});
</script>


</body>

</html>