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

// Check if the paymentID, paymentStatus, studentID, and courseID are set in the POST request
if (isset($_POST['paymentID'], $_POST['paymentStatus'], $_POST['studentID'], $_POST['courseID'])) {
    $paymentID = $_POST['paymentID'];
    $paymentStatus = $_POST['paymentStatus'];
    $studentID = $_POST['studentID'];
    $courseID = $_POST['courseID'];

    // Check if the student already has a confirmed payment for the current course
    $checkFullPaymentQuery = "SELECT * FROM course_full_payments WHERE student_id='$studentID' AND course_id='$courseID' AND payment_status='Confirmed'";
    $checkFullPaymentResult = mysqli_query($db, $checkFullPaymentQuery);

    if (mysqli_num_rows($checkFullPaymentResult) > 0) {
        echo 'Student already has a confirmed payment for the current course'; // Return message if student already has a confirmed payment
    } else {
        // Check if the payment status is set to "Confirmed"
        if ($paymentStatus === 'Confirmed') {
            // Update the payment status and payment_status_updated_date for the specified paymentID
            $updatePaymentQuery = "UPDATE course_full_payments SET payment_status='$paymentStatus', payment_status_updated_date='$currentDate' WHERE payment_id='$paymentID'";
            $updatePaymentResult = mysqli_query($db, $updatePaymentQuery);

            if ($updatePaymentResult) {
                // Insert a new entry into course_enrollments table
                $insertEnrollmentQuery = "INSERT INTO course_enrollments (student_id, course_id, enrollment_date, is_active) VALUES ('$studentID', '$courseID', '$currentDate', 1)";
                $insertEnrollmentResult = mysqli_query($db, $insertEnrollmentQuery);

                if ($insertEnrollmentResult) {
                    echo 'success'; // Return success message if both update and insert operations are successful
                } else {
                    echo 'Error inserting enrollment record';
                }
            } else {
                echo 'Error updating payment status';
            }
        } else {
            // Update the payment status for the specified paymentID without changing the date
            $updatePaymentQuery = "UPDATE course_full_payments SET payment_status='$paymentStatus' WHERE payment_id='$paymentID'";
            $updatePaymentResult = mysqli_query($db, $updatePaymentQuery);
        
            if ($updatePaymentResult) {
                echo 'success'; // Return success message if update operation is successful
            } else {
                echo 'Error updating payment status';
            }
        }
        
    }
} else {
    echo 'Invalid parameters'; // Return error message if required parameters are not set
}
?>
