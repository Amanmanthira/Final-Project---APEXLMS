<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["student_id"]))
{
    header('location:../frontend/login.php');
    exit(); // Ensure script execution stops after redirection
}

// Check if student account is active
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
$result = mysqli_query($db, $sql);
$row_count = mysqli_num_rows($result);

// Redirect to error.php if student account is not active
if($row_count != 1) {
    header('location:../frontend/error.php');
    exit(); // Ensure script execution stops after redirection
}

// Custom function to verify the token, fetch content path, and check enrollment
function getContentPathFromToken($content_id, $token, $student_id, $db) {
    // Fetch the content path, type, and topic_id from the database based on the content ID
    $sql = "SELECT content_path, content_type, topic_id FROM content WHERE content_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $content_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $content_path = $row['content_path'];
        $content_type = $row['content_type'];
        $topic_id = $row['topic_id'];

        // Verify the token
        $expected_token = md5($content_path); // You can use the same token generation logic here
        if ($token === $expected_token) {
            // Check if the student is enrolled in the course associated with the topic
            $enrollment_sql = "
                SELECT ce.course_id 
                FROM course_enrollments ce 
                JOIN topics t ON t.course_id = ce.course_id 
                WHERE ce.student_id = ? 
                  AND t.topic_id = ? 
                  AND ce.is_active = 1
            ";
            $enrollment_stmt = mysqli_prepare($db, $enrollment_sql);
            mysqli_stmt_bind_param($enrollment_stmt, "ii", $student_id, $topic_id);
            mysqli_stmt_execute($enrollment_stmt);
            $enrollment_result = mysqli_stmt_get_result($enrollment_stmt);

            if ($enrollment_result->num_rows > 0) {
                return array("path" => $content_path, "type" => $content_type);
            }
        }
    }
    return false;
}

// Check if content ID and token are provided in the URL
if (isset($_GET['content_id']) && isset($_GET['token'])) {
    // Extract content ID and token from URL parameters
    $content_id = $_GET['content_id'];
    $token = $_GET['token'];

    // Call the custom function to fetch content path and type based on the token and check enrollment
    $content_info = getContentPathFromToken($content_id, $token, $student_id, $db);

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
