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
// Check if the token parameter is provided in the URL
if(isset($_GET['token'])) {
    // Get the resource token
    $token = $_GET['token'];

    // Check if the token exists in the session and retrieve the corresponding resource path
    if(isset($_SESSION['resource_tokens'][$token])) {
        $resource_path = $_SESSION['resource_tokens'][$token];

        // Serve the resource file
        if(file_exists($resource_path)) {
            // Set appropriate headers
            header('Content-Type: ' . mime_content_type($resource_path));
            header('Content-Disposition: attachment; filename="' . basename($resource_path) . '"');

            // Output the file contents
            readfile($resource_path);
            exit();
        } else {
            // If the file doesn't exist, show an error message or redirect to a relevant page
            echo "Resource not found!";
        }
    } else {
        // If the token doesn't exist in the session, show an error message or redirect to a relevant page
        echo "Invalid resource token!";
    }
} else {
    // If the token parameter is not provided, show an error message or redirect to a relevant page
    echo "Resource token not provided!";
}
?>