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

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the student_id and status parameters are set
    if (isset($_POST['student_id']) && isset($_POST['status'])) {
        $studentId = $_POST['student_id'];
        $currentStatus = $_POST['status'];

        // Toggle the status
        $newStatus = ($currentStatus == 1) ? 0 : 1;

        // Perform the database update
        $updateQuery = "UPDATE students SET is_active = '$newStatus' WHERE id = '$studentId'";
        $updateResult = mysqli_query($db, $updateQuery);

        // Check if the query was successful
        if ($updateResult) {
            // Return success message to JavaScript
            echo "success";
        } else {
            // Return error message to JavaScript
            echo mysqli_error($db);
        }
    } else {
        // Return error message if parameters are missing
        echo "Parameters missing";
    }
} else {
    // Return error message for invalid request method
    echo "Invalid request method";
}
?>
