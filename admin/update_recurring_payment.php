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
// Check if the request is not made via AJAX
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    // Redirect to dashboard page
    header("Location: dashboard.php");
    exit();
}
// Get current date in 'Y-m-d' format
$currentDate = date("Y-m-d");

// Check if the paymentID, paymentStatus, studentID, courseID, and installmentNumber are set in the POST request
if (isset($_POST['paymentID'], $_POST['paymentStatus'], $_POST['studentID'], $_POST['courseID'])) {
    $paymentID = $_POST['paymentID'];
    $paymentStatus = $_POST['paymentStatus'];
    $studentID = $_POST['studentID'];
    $courseID = $_POST['courseID'];

    // Check if the student already has a confirmed payment for the current course and installment number 1
    $checkInstallmentPaymentQuery = "SELECT * FROM course_installment_payments WHERE student_id='$studentID' AND course_id='$courseID' AND installment_number=1 AND installment_payment_status='Confirmed'";
    $checkInstallmentPaymentResult = mysqli_query($db, $checkInstallmentPaymentQuery);

    if (mysqli_num_rows($checkInstallmentPaymentResult) == 0) {
        echo 'Selected Student is not an installment user for the selected Course';
        exit(); // Stop further execution
    }

    // Check if the installment number is greater than 1 and lower than 5 using the payment ID
    $checkAdditionalInstallmentPaymentQuery = "SELECT * FROM course_installment_payments WHERE payment_id='$paymentID' AND installment_number > 1 AND installment_number < 5";
    $checkAdditionalInstallmentPaymentResult = mysqli_query($db, $checkAdditionalInstallmentPaymentQuery);

    if (mysqli_num_rows($checkAdditionalInstallmentPaymentResult) > 0) {
        // Check if the payment status is set to "Confirmed"
        if ($paymentStatus === 'Confirmed') {
            // Update the payment status and payment_status_updated_date for the specified paymentID
            $updatePaymentQuery = "UPDATE course_installment_payments SET installment_payment_status='$paymentStatus', payment_status_updated_date='$currentDate' WHERE payment_id='$paymentID'";
            $updatePaymentResult = mysqli_query($db, $updatePaymentQuery);

            if ($updatePaymentResult) {
                echo 'success'; // Return success message if update operation is successful
            } else {
                echo 'Error updating payment status';
            }
        } else {
            // Update the payment status for the specified paymentID without changing the date
            $updatePaymentQuery = "UPDATE course_installment_payments SET installment_payment_status='$paymentStatus' WHERE payment_id='$paymentID'";
            $updatePaymentResult = mysqli_query($db, $updatePaymentQuery);

            if ($updatePaymentResult) {
                echo 'success'; // Return success message if update operation is successful
            } else {
                echo 'Error updating payment status';
            }
        }
    } else {
        echo 'Selected student has already paid the full amount for the selected course';
    }
} else {
    echo 'Invalid parameters'; // Return error message if required parameters are not set
}
?>
