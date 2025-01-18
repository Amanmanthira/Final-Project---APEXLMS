<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["student_id"]))
{
	header('location:../frontend/login.php');
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

// Check if the token parameter is provided in the URL
if(isset($_GET['token']) && isset($_GET['id'])) {
    // Get the resource token and assignment ID
    $token = $_GET['token'];
    $assignment_id = $_GET['id'];

    // Check if the token exists in the session and retrieve the corresponding resource path
    if(isset($_SESSION['resource_tokens'][$token])) {
        // Retrieve the resource path
        $resource_path = $_SESSION['resource_tokens'][$token];
        
        // Serve the resource file if it exists
        if(file_exists($resource_path)) {
            // Get the topic ID associated with the assignment
            $sql = "SELECT topic_id FROM assignments WHERE assignment_id = $assignment_id";
            $result = mysqli_query($db, $sql);
            $row = mysqli_fetch_assoc($result);
            $topic_id = $row['topic_id'];

            // Match the topic ID with the courses table to get the course ID
            $sql = "SELECT course_id FROM topics WHERE topic_id = $topic_id";
            $result = mysqli_query($db, $sql);
            $row = mysqli_fetch_assoc($result);
            $course_id = $row['course_id'];

            // Match the course ID with the course enrollments table to ensure student enrollment
            $sql = "SELECT * FROM course_enrollments WHERE student_id = $student_id AND course_id = $course_id AND is_active = 1";
            $result = mysqli_query($db, $sql);

            // If the student is enrolled in the course and enrollment is active
            if(mysqli_num_rows($result) > 0) {
                // Set appropriate headers
                header('Content-Type: ' . mime_content_type($resource_path));
                header('Content-Disposition: attachment; filename="' . basename($resource_path) . '"');

                // Output the file contents
                readfile($resource_path);
                exit();
            } else {
                echo "You are not enrolled in this course!";
            }
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
    echo "Resource token or assignment ID not provided!";
}

?>
