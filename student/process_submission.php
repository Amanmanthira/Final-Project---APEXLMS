<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Redirect to login page if user is not logged in
if (empty($_SESSION["student_id"])) {
    header('location:../frontend/login.php');
    exit(); // Stop script execution after redirection
}

// Check if student account is active
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
$result = mysqli_query($db, $sql);
$row_count = mysqli_num_rows($result);

// Redirect to error.php if student account is not active
if ($row_count != 1) {
    header('location:../frontend/error.php');
    exit(); // Stop script execution after redirection
}

// Check if form is submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Get assignment ID from form
    $assignment_id = $_POST['assignment_id'];

    // Define allowed file types
    $allowed_types = array('docx', 'pdf', 'zip', 'rar');

    // Define upload directory
    $upload_dir = "../assets/private/assignments_submissions/";

    // Get file details
    $file_name = $_FILES['submission_file']['name'];
    $file_tmp = $_FILES['submission_file']['tmp_name'];
    $file_size = $_FILES['submission_file']['size'];
    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Check if file type is allowed
    if (!in_array($file_type, $allowed_types)) {
        echo "Error: Only DOCX, PDF, ZIP, and RAR files are allowed.";
        exit();
    }

    // Check if the student has already submitted for this assignment
    $existing_submission_sql = "SELECT * FROM assignment_submissions WHERE student_id = '$student_id' AND assignment_id = '$assignment_id'";
    $existing_submission_result = mysqli_query($db, $existing_submission_sql);

    if (mysqli_num_rows($existing_submission_result) > 0) {
        echo "Error: You have already submitted for this assignment.";
        exit();
    }

    // Generate a unique file name with prefix and unique ID
    $unique_id = uniqid();
    $new_file_name = "assignment_submission_" . $unique_id . "." . $file_type;
    $submission_path = $upload_dir . $new_file_name;

    // Move the uploaded file to the upload directory
    if (move_uploaded_file($file_tmp, $submission_path)) {
        // Insert submission details into database
        $insert_sql = "INSERT INTO assignment_submissions (student_id, assignment_id, submission_path) VALUES ('$student_id', '$assignment_id', '$submission_path')";
        if (mysqli_query($db, $insert_sql)) {
            // Return success message
            echo "success";
            exit();
        } else {
            echo "Error: " . mysqli_error($db);
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}
?>
