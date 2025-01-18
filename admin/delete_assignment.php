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


// Check if assignment ID is provided
if(isset($_POST['assignmentId'])) {
    $assignmentId = $_POST['assignmentId'];

    // Update the is_active field to 0 for the specified assignment ID
    $sql = "UPDATE assignments SET is_active = 0 WHERE assignment_id = '$assignmentId'";
    if (mysqli_query($db, $sql)) {
        echo "Assignment marked as inactive successfully.";
    } else {
        echo "Error marking assignment as inactive: " . mysqli_error($db);
    }
} else {
    echo "Invalid request.";
}
?>
