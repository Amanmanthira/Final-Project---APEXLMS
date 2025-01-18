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

function validateFile($file) {
    // Check if the file is uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Error uploading the file.";
    }

    // Check file size
    if ($file['size'] > 100000000) { // 100MB
        return "The uploaded file is too large.";
    }

    // Allow certain file formats
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ['pdf', 'mp3', 'wav', 'mp4', 'avi', 'jpg', 'jpeg', 'png', 'gif'])) {
        return "Only PDF, MP3, WAV, MP4, AVI , JPG , JPEG , PNG and GIF files are allowed.";
    }

    return "";
}

function uploadFile($file, $destination) {
    $target_directory = "../assets/private/content/";
    $unique_id = uniqid(); // Generate unique ID
    $file_name = "content_" . $unique_id . "_" . basename($file['name']); // Prefix with unique ID
    $target_file = $target_directory . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $target_file;
    } else {
        return "Error uploading the file.";
    }
}


// Handle form submission and file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topicId = $_POST['topicId'];
    $contentName = $_POST['contentName'];
    $contentType = $_POST['contentType'];
    $contentText = $_POST['contentText'];

    // Check if file is uploaded for PDF, Audio, Video or Image content types
    if (in_array($contentType, ['PDF', 'Audio', 'Video' , 'Image']) && isset($_FILES['contentFile'])) {
        // Validate and upload file
        $validation_result = validateFile($_FILES['contentFile']);
        if ($validation_result === "") {
            // Handle file upload
            $contentPath = uploadFile($_FILES['contentFile'], '../assets/private/content/');
        } else {
            echo $validation_result;
            exit();
        }
    } else {
        // Handle content text for Zoom Link or other content types
        $contentPath = ''; // No file uploaded
    }

        // Check if content name already exists
        $query = "SELECT COUNT(*) as count FROM content WHERE content_name = '$contentName' AND topic_id = '$topicId'";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $classTime = $_POST['class_time']; // This will be in 'YYYY-MM-DDTHH:MM' format
        // Convert to MySQL-compatible format
        $classTime = date('Y-m-d H:i:s', strtotime($classTime));

        if ($row['count'] > 0) {
            echo "error: Content with the same name already exists.";
            exit();
        }

        // Insert content into the database
        $sql = "INSERT INTO content (content_name, content_type, content_text, content_path, topic_id, created_at, class_time,is_active) 
                VALUES ('$contentName', '$contentType', '$contentText', '$contentPath', '$topicId', '$classTime', NOW(), 1)";
        if (mysqli_query($db, $sql)) {
            echo "success: Content added successfully.";
        } else {
            echo "error: Error adding content: " . mysqli_error($db);
        }
} else {
    echo "error: Invalid request.";
}
?>
