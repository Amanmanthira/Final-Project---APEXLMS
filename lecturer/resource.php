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