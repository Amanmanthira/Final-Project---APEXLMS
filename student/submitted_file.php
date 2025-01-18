<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["student_id"])) {
	header('location:../frontend/login.php');
	exit();
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
if(isset($_GET['token'])) {
    // Get the submission token
    $token = $_GET['token'];

    // Check if the token exists in the session and retrieve the corresponding submission file path
    if(isset($_SESSION['submission_tokens'][$token])) {
        // Retrieve the submission file path
        $submission_file = $_SESSION['submission_tokens'][$token];
        
        // Serve the submission file
        if(file_exists($submission_file)) {
            // Set appropriate headers
            header('Content-Type: ' . mime_content_type($submission_file));
            header('Content-Disposition: attachment; filename="' . basename($submission_file) . '"');

            // Output the file contents
            readfile($submission_file);
            exit();
        } else {
            // If the file doesn't exist, show an error message or redirect to a relevant page
            echo "Submitted file not found!";
        }
    } else {
        // If the token doesn't exist in the session, show an error message or redirect to a relevant page
        echo "Invalid submission token!";
    }
} else {
    // If the token parameter is not provided, show an error message or redirect to a relevant page
    echo "Submission token not provided!";
}
?>
