<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if lecturer_id is set in session
if(isset($_SESSION["apex_lecturer_id"])) {
    // Retrieve lecturer_id from session
    $lecturer_id = $_SESSION["apex_lecturer_id"];

    // Check if lecturer account is active
    $sql = "SELECT * FROM lecturers WHERE lecturer_id = '$lecturer_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);

    // Redirect to error.php if lecturer account is not active
    if($row_count != 1) {
        header('location: error.php');
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if lecturer_id is not set in session
    header('location: index.php');
    exit(); // Ensure script execution stops after redirection
}

// Retrieve the course_id from the GET parameters
$course_id = $_GET['course_id'];

// Check if the lecturer is assigned to the course
$query = "SELECT * FROM course_lecturers WHERE course_id = ? AND lecturer_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $course_id, $lecturer_id);
$stmt->execute();
$stmt->store_result();
$row_count = $stmt->num_rows;
$stmt->close();

if ($row_count != 1) {
    // Redirect to my_courses.php if the lecturer is not assigned to the course
    header('location: my_courses.php');
    exit(); // Ensure script execution stops after redirection
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
