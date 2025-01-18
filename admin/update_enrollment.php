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
    // Check if the enrollment_id parameter is set
    if (isset($_POST['enrollment_id'])) {
        $enrollmentId = $_POST['enrollment_id'];

        // Fetch the current status of the enrollment from the database
        $query = "SELECT is_active FROM course_enrollments WHERE enrollment_id = '$enrollmentId'";
        $result = mysqli_query($db, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $currentStatus = $row['is_active'];

            // Toggle the status
            $newStatus = ($currentStatus == 1) ? 0 : 1;

            // Perform the database update
            $updateQuery = "UPDATE course_enrollments SET is_active = '$newStatus' WHERE enrollment_id = '$enrollmentId'";
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
            // Return error message if enrollment not found
            echo "Enrollment not found";
        }
    } else {
        // Return error message if parameter is missing
        echo "Parameter missing";
    }
} else {
    // Return error message for invalid request method
    echo "Invalid request method";
}
?>
