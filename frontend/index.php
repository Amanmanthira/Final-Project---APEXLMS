<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if student_id is set in session
if(isset($_SESSION["student_id"])) {
    // Retrieve student_id from session
    $student_id = $_SESSION["student_id"];
} 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>APEX Institute of Management Services</title>
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
    <link rel="icon" type="image/png" sizes="16x16" href="images/apexlogo.png">

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
          <li class="nav-item active"><a href="index.php" class="nav-link">Home</a></li>
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
    
    <div class="hero-wrap" style="background-image: url('images/bg_1.jpg'); background-attachment:fixed;">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center" data-scrollax-parent="true">
          <div class="col-md-8 ftco-animate text-center">
            <h1 class="mb-4">Education is key to national prosperity and progress.</h1>
            <?php
if(isset($_SESSION["student_id"])) {
    // If student is logged in
    echo '<p><a href="../student/dashboard.php" class="btn btn-primary px-4 py-3">LMS</a> <a href="course.php" class="btn btn-secondary px-4 py-3">View Courses</a></p>';
} else {
    // If student is not logged in
    echo '<p><a href="registration.php" class="btn btn-primary px-4 py-3">Register Now</a> <a href="course.php" class="btn btn-secondary px-4 py-3">View Courses</a></p>';
}
?>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-search-course">
    	<div class="container">
    		<div class="row">
    			<div class="col-md-12">
    				<div class="courseSearch-wrap d-md-flex flex-column-reverse">
    					<div class="full-wrap d-flex ftco-animate">
                <div class="one-third order-last p-5">
                <span>Know what you're after?</span>
                <h3>I want to study</h3>
                <form action="#" class="course-search-form">
                    <div class="form-group position-relative">
                        <input type="text" class="form-control" id="course-search-input" placeholder="Type a course you want to study">
                        <ul id="course-search-results" class="dropdown-menu">
                            <!-- Search results will be dynamically added here -->
                        </ul>
                    </div>
                </form>

                <p>Just Browsing? <a href="course.php"> See all courses</a></p>
            </div>

    						<div class="one-forth order-first img" style="background-image: url(images/image_1.jpg);"></div>
    					</div>
    					<div class="full-wrap ftco-animate">
    						<div class="one-half">
    							<div class="featured-blog d-md-flex">
    								<div class="image d-flex order-last">
    									<a href="#" class="img" style="background: url(images/image_2.jpg);"></a>
    								</div>
    							</div>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </section>

    <section class="ftco-section">
    	<div class="container">
    		<div class="row">
          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services p-3 py-4 d-block text-center">
              <div class="icon d-flex justify-content-center align-items-center mb-3"><span class="flaticon-exam"></span></div>
              <div class="media-body px-3">
                <h3 class="heading">Admission</h3>
                <p>Unlock your future with our institution - where knowledge meets opportunity.</p>
              </div>
            </div>      
          </div>
          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services p-3 py-4 d-block text-center">
              <div class="icon d-flex justify-content-center align-items-center mb-3"><span class="flaticon-blackboard"></span></div>
              <div class="media-body px-3">
                <h3 class="heading">Online Lectures</h3>
                <p>Explore dynamic online lectures for comprehensive learning experiences.</p>
              </div>
            </div>      
          </div>
          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services p-3 py-4 d-block text-center">
              <div class="icon d-flex justify-content-center align-items-center mb-3"><span class="flaticon-books"></span></div>
              <div class="media-body px-3">
                <h3 class="heading">Lecture Materials</h3>
                <p>Access comprehensive lecture materials, including videos, PDFs, slides, and  Continuous Assessment Tests.</p>
              </div>
            </div>    
          </div>
        </div>
    	</div>
    </section>


    <section class="ftco-section-3 img" style="background-image: url(images/bg_3.jpg);">
    	<div class="overlay"></div>
    	<div class="container">
    		<div class="row d-md-flex justify-content-center">
    			<div class="col-md-9 about-video text-center">
    				<h2 class="ftco-animate">Apex Institute Management Services is a Leading educational Institute in Sri Lanka.</h2>
    			</div>
    		</div>
    	</div>
    </section>
    <section class="ftco-counter bg-light" id="section-counter">
    	<div class="container">
    		<div class="row justify-content-center">
    			<div class="col-md-10">
		    		<div class="row">
		          <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
		            <div class="block-18 text-center">
		              <div class="text">
		                <strong class="number" data-number="25624">0</strong>
		                <span>Students</span>
		              </div>
		            </div>
		          </div>
		          <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
		            <div class="block-18 text-center">
		              <div class="text">
		                <strong class="number" data-number="60">0</strong>
		                <span>Courses</span>
		              </div>
		            </div>
		          </div>
		          <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
		            <div class="block-18 text-center">
		              <div class="text">
		                <strong class="number" data-number="200">0</strong>
		                <span>Teachers</span>
		              </div>
		            </div>
		          </div>
		          <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
		            <div class="block-18 text-center">
		              <div class="text">
		                <strong class="number" data-number="8">0</strong>
		                <span>Branches</span>
		              </div>
		            </div>
		          </div>
		        </div>
	        </div>
        </div>
    	</div>
    </section>

    <section class="ftco-section testimony-section">
      <div class="container">
      	<div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section ftco-animate text-center">
            <h2 class="mb-4">Student Testimonials </h2>
          </div>
        </div>
        <div class="row">
        	<div class="col-md-12 ftco-animate">
            <div class="carousel-testimony owl-carousel">
              <div class="item">
                <div class="testimony-wrap text-center">
                  <div class="user-img mb-5" style="background-image: url(images/kavindu.jpeg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                      <i class="icon-quote-left"></i>
                    </span>
                  </div>
                  <div class="text">
                    <p class="mb-5">"Exploring the depths of finance has been an enlightening journey. With each lecture, I'm gaining invaluable insights into the world of investments and financial management, shaping me into a more astute professional.</p>
                    <p class="name">Kavindu Silva</p>
                    <span class="position">Finance Student</span>
                </div>
                </div>
              </div>
              <div class="item">
                <div class="testimony-wrap text-center">
                  <div class="user-img mb-5" style="background-image: url(images/nethmi.jpeg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                      <i class="icon-quote-left"></i>
                    </span>
                  </div>
                  <div class="text">
                    <p class="mb-5">Delving into the intricacies of marketing has been an exhilarating experience. Through dynamic lectures and hands-on projects, I'm honing my creativity and strategic thinking, ready to make an impact in the ever-evolving world of marketing.</p>
                    <p class="name">Nethmi Fernando</p>
                    <span class="position">Marketing Student</span>
                </div>
                </div>
              </div>
              <div class="item">
                <div class="testimony-wrap text-center">
                  <div class="user-img mb-5" style="background-image: url(images/dilshan.jpg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                      <i class="icon-quote-left"></i>
                    </span>
                  </div>
                  <div class="text">
                    <p class="mb-5">Embarking on the journey of HR management has been both challenging and fulfilling. Each class equips me with invaluable skills and insights, shaping me into a capable HR professional ready to tackle real-world challenges.</p>
                    <p class="name">Dilshan Perera</p>
                    <span class="position">Human Resource Management Student</span>
                </div>
                </div>
              </div>
              <div class="item">
                <div class="testimony-wrap text-center">
                  <div class="user-img mb-5" style="background-image: url(images/tharindu.jpg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                      <i class="icon-quote-left"></i>
                    </span>
                  </div>
                  <div class="text">
                    <p class="mb-5">Diving into the realm of IT has been truly enlightening. Immersed in cutting-edge technologies and surrounded by innovative ideas, I'm not just acquiring technical skills; I'm preparing myself to thrive in the dynamic and fast-paced IT industry.</p>
                    <p class="name">Tharindu Rajapaksha</p>
                    <span class="position">Information Technology Student</span>
                </div>
                </div>
              </div>
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
  <script>
    
 $(document).ready(function(){
    $('#course-search-input').on('input', function(){
        var query = $(this).val().toLowerCase().trim();
        if(query != ''){
            $.ajax({
                url: 'search.php',
                method: 'POST',
                data: {query: query},
                success: function(data){
                    $('#course-search-results').html(data);
                    if(data.trim() !== '') {
                        $('#course-search-results').show();
                    } else {
                        $('#course-search-results').hide();
                    }
                }
            });
        } else {
            $('#course-search-results').hide(); // Hide results when input is empty
        }
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function (e) {
        if ($(e.target).closest('#course-search-results').length === 0 && $(e.target).closest('#course-search-input').length === 0) {
            $('#course-search-results').hide();
        }
    });

    // Hide results when input box loses focus
    $('#course-search-input').on('blur', function() {
        $('#course-search-results').hide();
    });
});

</script>

  </body>
</html>