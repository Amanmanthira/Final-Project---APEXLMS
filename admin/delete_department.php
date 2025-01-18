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

if(isset($_POST['department_id'])) {
    $department_id = $_POST['department_id'];

    // Prepare and execute SQL query to update is_active to 0 for the specified department
    $sql = "UPDATE Departments SET is_active = 0 WHERE department_id = ?";
    $stmt = mysqli_prepare($db, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $department_id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Department deleted successfully
            echo json_encode(array("status" => "success"));
        } else {
            // Error occurred while deleting department
            echo json_encode(array("status" => "error", "message" => "Failed to delete department"));
        }

        mysqli_stmt_close($stmt);
    } else {
        // Error in preparing SQL statement
        echo json_encode(array("status" => "error", "message" => "Error in preparing SQL statement"));
    }
} else {
    // Invalid request
    echo json_encode(array("status" => "error", "message" => "Invalid request"));
}
?>
