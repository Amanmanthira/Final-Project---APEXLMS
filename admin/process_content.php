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

// Custom function to verify the token and fetch content path
function getContentPathFromToken($content_id, $token, $db) {
    // Fetch the content path and type from the database based on the content ID
    $sql = "SELECT content_path, content_type FROM content WHERE content_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $content_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $content_path = $row['content_path'];
        $content_type = $row['content_type'];

        // Verify the token
        $expected_token = md5($content_path); // You can use the same token generation logic here
        if ($token === $expected_token) {
            return array("path" => $content_path, "type" => $content_type);
        }
    }
    return false;
}

// Check if content ID and token are provided in the URL
if (isset($_GET['content_id']) && isset($_GET['token'])) {
    // Extract content ID and token from URL parameters
    $content_id = $_GET['content_id'];
    $token = $_GET['token'];

    // Call the custom function to fetch content path and type based on the token
    $content_info = getContentPathFromToken($content_id, $token, $db);

    if ($content_info) {
        // Serve the content securely
        $content_path = $content_info['path'];
        $content_type = $content_info['type'];
        
        // Convert the content type to appropriate MIME type if needed
        if ($content_type === 'PDF') {
            $content_type = 'application/pdf';
        }
        
        // Set appropriate content type header
        header("Content-Type: $content_type");
        
        // Output the content directly
        readfile($content_path);
        exit();
    }
}

// If the token verification fails or content path is not found, return an error message
echo "Error: Unauthorized access or content not found.";
?>
