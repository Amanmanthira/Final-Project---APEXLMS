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
// Check if the request is not made via AJAX
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    // Redirect to dashboard page
    header("Location: dashboard.php");
    exit();
}
// Check if course_id and student_id are provided
if(isset($_POST['course_id']) && isset($_POST['student_id'])) {
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];

    // Fetch course details from the database
    $query = mysqli_query($db, "SELECT course_fee
                                FROM courses
                                WHERE course_id = '$course_id'");
    $course = mysqli_fetch_assoc($query);

    // Calculate total paid amount for the course
    $total_paid_query = mysqli_query($db, "SELECT SUM(installment_amount) AS total_paid 
                                           FROM course_installment_payments 
                                           WHERE student_id = '$student_id' 
                                           AND course_id = '$course_id' 
                                           AND installment_payment_status = 'Confirmed'");
    $total_paid_data = mysqli_fetch_assoc($total_paid_query);
    $total_paid = $total_paid_data['total_paid'];
    $course_fee = $course['course_fee'];

    // Calculate remaining balance
    $balance = $course['course_fee'] - $total_paid;

    // Fetch the highest installment number
    $installment_number_query = mysqli_query($db, "SELECT MAX(installment_number) AS highest_installment_number
                                                   FROM course_installment_payments
                                                   WHERE course_id = '$course_id'
                                                   AND student_id = '$student_id'
                                                   AND installment_payment_status = 'Confirmed'");
    $installment_number_data = mysqli_fetch_assoc($installment_number_query);
    $highest_installment_number = $installment_number_data['highest_installment_number'];
     // Calculate payment amount
    $payment_amount = $course_fee / 4;
    // Prepare response as JSON
    $response = array(
        'payment_amount' => $payment_amount,
        'course_fee' => $course['course_fee'],
        'balance' => $balance,
        'highest_installment_number' => $highest_installment_number
    );

    // Return response as JSON
    echo json_encode($response);
} else {
    // Invalid request
    echo json_encode(array('error' => 'Invalid request'));
}
?>
