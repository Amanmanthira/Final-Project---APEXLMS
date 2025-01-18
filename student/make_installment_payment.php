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


// Fetch student details from session
$student_id = $_SESSION['student_id'];
$username = $_SESSION['username'];


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
    $course_id = $_POST['course_name'];
    $paymentAmount = $_POST['payment_amount'];
    $paymentDate = $_POST['payment_date'];
    $installmentNumber = $_POST['installment_number']; 
    
    // Retrieve payment slip image file
    $paymentSlipExtension = strtolower(pathinfo($_FILES['payment_slip']['name'], PATHINFO_EXTENSION)); // Get the file extension
    $allowedExtensions = array('jpg', 'jpeg', 'png'); // Allowed file extensions
    if (in_array($paymentSlipExtension, $allowedExtensions)) {
        $paymentSlipName = uniqid('payment_slip_') . '.' . $paymentSlipExtension; // Generate a unique name with the correct extension
        $paymentSlipTemp = $_FILES['payment_slip']['tmp_name'];
        $paymentSlipPath = "../assets/private/payment_slips/" . $paymentSlipName;

        // Move uploaded payment slip file to the designated directory
        move_uploaded_file($paymentSlipTemp, $paymentSlipPath);

        // Insert installment details into course_installment_payments table
        $insertInstallmentPaymentQuery = "INSERT INTO course_installment_payments (student_id, course_id, installment_number, installment_amount, installment_payment_date, installment_payment_status, installment_payment_slip_path) VALUES ('$student_id', '$course_id', '$installmentNumber', '$paymentAmount', '$paymentDate', 'Pending', '$paymentSlipPath')";
        $result = mysqli_query($db, $insertInstallmentPaymentQuery);

        if($result) {
            // Payment successfully inserted, display success message
            $successMsg = "Installment payment submitted successfully.Verification is pending.";
        } else {
            // Error occurred while inserting payment, handle as needed (e.g., display error message)
            $errorMsg = "Error occurred while processing payment. Please try again.";
        }
    } else {
        // Invalid file extension, handle as needed (e.g., display error message)
        $errorMsg = "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
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
    <title>APEX Institute  | Installment Payment </title>
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
                            <h4 class="card-title">Installment Payment</h4>
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
                                        <label for="course_name">Select Course</label>
                                        <select class="form-control" id="course_name" name="course_name" required>
                                        <option value="">Select Course</option> <!-- Default option -->
                                        <?php
                                        // Fetch courses from the database where installment payment status is confirmed and installment number is 1
                                        $installment_payment_query = mysqli_query($db, "SELECT ce.course_id, c.course_name, c.course_fee
                                                                                        FROM course_enrollments ce
                                                                                        INNER JOIN course_installment_payments cip ON ce.course_id = cip.course_id 
                                                                                        INNER JOIN courses c ON ce.course_id = c.course_id
                                                                                        WHERE cip.student_id = '$student_id' 
                                                                                        AND cip.installment_payment_status = 'Confirmed' 
                                                                                        AND cip.installment_number = 1");

                                        while($course = mysqli_fetch_assoc($installment_payment_query)) {
                                            echo "<option value='" . $course['course_id'] . "'>" . $course['course_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="course_fee">Course Fee (LKR)</label>
                                        <input type="text" class="form-control" id="course_fee" name="course_fee" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="balance">Balance (LKR)</label>
                                        <input type="text" class="form-control" id="balance" name="balance" disabled>
                                    </div>
                                    <div class="form-group balance-message"></div>

                                    <div class="form-group" id="payment-details">
    <div class="form-group">
        <label for="payment_amount">Payment Amount (LKR)</label>
        <input type="text" class="form-control" id="payment_amount" name="payment_amount" value="<?php echo $paymentAmount; ?>" required readonly>
    </div>
    <div class="form-group">
        <label for="installment_number">Installment Number</label>
        <input type="text" class="form-control" id="installment_number" name="installment_number" value="<?php echo $installmentNumber; ?>" required readonly>
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
</div>
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
   $(document).ready(function(){
    $('#course_name').change(function(){
        var course_id = $(this).val(); // Get the selected course ID
        var student_id = '<?php echo $student_id; ?>'; // Get the student ID from PHP
        
        // AJAX request
        $.ajax({
            url: 'fetch_course_details.php', // Update the URL to your PHP file that handles the AJAX request
            type: 'post',
            dataType: 'json',
            data: {course_id: course_id, student_id: student_id},
            success: function(response) {
                // Update form fields with fetched data
                $('#payment_amount').val(response.payment_amount.toFixed(2));
                $('#course_fee').val(response.course_fee);
                $('#balance').val(response.balance.toFixed(2));
                $('#installment_number').val(parseInt(response.highest_installment_number) + 1);

                // Update balance dynamically
                if (response.balance == 0) {
                    $('.balance-message').html('<div class="alert alert-success mt-4" role="alert">You have already paid the full amount by installments for the selected course.</div>');
                    $('#payment-details').hide(); // Hide payment details
                } else {
                    $('.balance-message').html(''); // Clear previous message if any
                    $('#payment-details').show(); // Show payment details
                }
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error(xhr.responseText);
            }
        });
    });
});


</script>

</body>

</html>