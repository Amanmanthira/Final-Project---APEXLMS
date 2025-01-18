<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if student_id is set in session
if(isset($_SESSION["student_id"])) {
    // Retrieve student_id from session
    $student_id = $_SESSION["student_id"];

    // Check if student account is active
    $sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);

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
// Fetch course ID from URL
if(isset($_GET['course_id']) && !empty($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details from the database
    $course_query = mysqli_query($db, "SELECT * FROM courses WHERE course_id = '$course_id'");
    $course_data = mysqli_fetch_assoc($course_query);
} else {
    header("Location: ../frontend/course.php");
    exit();
}


// Fetch student details from session
$student_id = $_SESSION['student_id'];
$username = $_SESSION['username'];

// Check if there is an active enrollment record for the same student and course
$existing_enrollment_query = mysqli_query($db, "SELECT * FROM course_enrollments WHERE student_id = '$student_id' AND course_id = '$course_id'");

if(mysqli_num_rows($existing_enrollment_query) > 0) {
    // Redirect to enrollments.php
    header("Location: enrollments.php");
    exit();
}
$pending_payments_query = mysqli_query($db, "SELECT student_id, course_id, payment_status 
                                             FROM course_full_payments 
                                             WHERE student_id = '$student_id' AND course_id = '$course_id' AND payment_status = 'Pending' 
                                             UNION 
                                             SELECT student_id, course_id, installment_payment_status 
                                             FROM course_installment_payments 
                                             WHERE student_id = '$student_id' AND course_id = '$course_id' AND installment_payment_status = 'Pending'");

if(mysqli_num_rows($pending_payments_query) > 0) {
     // Redirect to enrollments.php
     header("Location: pending_payments_error.php");
     exit();
}  

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Retrieve form data
    $paymentType = $_POST['payment_type'];
    $paymentAmount = $_POST['payment_amount'];
    $paymentDate = $_POST['payment_date'];
    
// Handle Full Payment
if($paymentType === 'Full Payment') {
    // Retrieve payment slip image file
    $paymentSlipExtension = strtolower(pathinfo($_FILES['payment_slip']['name'], PATHINFO_EXTENSION)); // Get the file extension
    $allowedExtensions = array('jpg', 'jpeg', 'png'); // Allowed file extensions
    if (in_array($paymentSlipExtension, $allowedExtensions)) {
        $paymentSlipName = uniqid('payment_slip_') . '.' . $paymentSlipExtension; // Generate a unique name with the correct extension
        $paymentSlipTemp = $_FILES['payment_slip']['tmp_name'];
        $paymentSlipPath = "../assets/private/payment_slips/" . $paymentSlipName;

        // Move uploaded payment slip file to the designated directory
        move_uploaded_file($paymentSlipTemp, $paymentSlipPath);

        // Insert payment details into course_full_payments table
        $insertFullPaymentQuery = "INSERT INTO course_full_payments (student_id, course_id, payment_amount, payment_date, payment_slip_path, payment_status) VALUES ('$student_id', '$course_id', '$paymentAmount', '$paymentDate', '$paymentSlipPath', 'Pending')";
        $result = mysqli_query($db, $insertFullPaymentQuery);

        if($result) {
            // Payment successfully inserted, display success message
            $successMsg = "Full Payment Slip Submitted Successfully. Payment verification is pending.";
        } else {
            // Error occurred while inserting payment, handle as needed (e.g., display error message)
            $errorMsg = "Error occurred while processing payment. Please try again.";
        }
    } else {
        // Invalid file extension, handle as needed (e.g., display error message)
        $errorMsg = "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
    }
}

 // Handle Installment first payment
elseif($paymentType === 'Installment') {
    // Retrieve payment slip image file
    $paymentSlipExtension = strtolower(pathinfo($_FILES['payment_slip']['name'], PATHINFO_EXTENSION)); // Get the file extension
    $allowedExtensions = array('jpg', 'jpeg', 'png'); // Allowed file extensions
    if (in_array($paymentSlipExtension, $allowedExtensions)) {
        $paymentSlipName = uniqid('payment_slip_') . '.' . $paymentSlipExtension; // Generate a unique name with the correct extension
        $paymentSlipTemp = $_FILES['payment_slip']['tmp_name'];
        $paymentSlipPath = "../assets/private/payment_slips/" . $paymentSlipName;

        // Move uploaded payment slip file to the designated directory
        move_uploaded_file($paymentSlipTemp, $paymentSlipPath);

        // Calculate installment due date (one month from payment date)
        $paymentDate = $_POST['payment_date'];

        // Insert installment details into course_installment_payments table
        $insertInstallmentPaymentQuery = "INSERT INTO course_installment_payments (student_id, course_id, installment_number, installment_amount, installment_payment_date, installment_payment_status, installment_payment_slip_path) VALUES ('$student_id', '$course_id', '1', '$paymentAmount', '$paymentDate', 'Pending', '$paymentSlipPath')";
        $result = mysqli_query($db, $insertInstallmentPaymentQuery);

        if($result) {
            // Payment successfully inserted, display success message
            $successMsg = "First installment payment submitted successfully. You will be notified via email once your payment is verified.";
        } else {
            // Error occurred while inserting payment, handle as needed (e.g., display error message)
            $errorMsg = "Error occurred while processing payment. Please try again.";
        }
    } else {
        // Invalid file extension, handle as needed (e.g., display error message)
        $errorMsg = "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
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
    <title>APEX Institute  | Enroll </title>
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
                            <h4 class="card-title">Enroll in Course</h4>
                            <h6 class="card-subtitle">Course: <b> <?php echo $course_data['course_name']; ?></b></h6>
                            <h6 class="card-subtitle">Student ID: <b><?php echo $student_id; ?></h6>
                            <h6 class="card-subtitle">Username:<b> <?php echo $username; ?></h6>
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
                            echo '<script>
                                // Set a timeout to redirect after 2 seconds (2000 milliseconds)
                                setTimeout(function() {
                                    window.location.href = "dashboard.php";
                                }, 2000);
                            </script>'; 
                        ?>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                    <form method="POST" enctype="multipart/form-data">

                      <div class="form-group">
                          <label for="payment_type">Payment Type</label>
                          <select class="form-control" id="payment_type" name="payment_type" required onchange="updatePaymentAmount()">
                              <option value="Full Payment">Full Payment</option>
                              <option value="Installment">Installment (4 x <?php echo $course_data['course_fee'] / 4; ?>)</option>
                          </select>
                      </div>
                      <div class="form-group">
                          <label for="payment_amount">Payment Amount (LKR)</label>
                          <input type="text" class="form-control" id="payment_amount" name="payment_amount" value="<?php echo $course_data['course_fee']; ?>" required>
                      </div>
                      <div class="form-group">
                          <label for="payment_date">Payment Date</label>
                          <input type="text" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                      </div>
                        <div class="form-group">
                            <label for="payment_slip">Payment Slip (Image)</label>
                            <input type="file" name="payment_slip" accept=".jpg,.jpeg,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Submit Payment</button>
                    </form>
                </div>
                         
                        </div>
                    </div>
                </div>
            </div>
   
</div>

           


    <!-- End Modal HTML -->
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


    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="js/lib/datatables/datatables-init.js"></script>
    <script>
    function updatePaymentAmount() {
        var paymentType = document.getElementById("payment_type").value;
        var courseFee = parseFloat(<?php echo $course_data['course_fee']; ?>);
        var paymentAmountField = document.getElementById("payment_amount");

        if (paymentType === 'Full Payment') {
            paymentAmountField.value = courseFee.toFixed(2); // Format to two decimal places
        } else if (paymentType === 'Installment') {
            paymentAmountField.value = (courseFee / 4).toFixed(2); // Assuming 4 installments
        }
    }
</script>
 

</script>

</body>

</html>