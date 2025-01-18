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
        return "Only PDF, MP3, WAV, MP4, AVI, JPG, JPEG, PNG, and GIF files are allowed.";
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
    $contentId = $_POST['contentId']; // Additional: Retrieve content ID for editing
    $contentName = $_POST['contentName'];
    $contentType = $_POST['contentType'];
    $contentText = $_POST['contentText'];

    // Check if the content name already exists, excluding the current content ID
    $sql_check = "SELECT COUNT(*) AS count FROM content WHERE content_name = '$contentName' AND content_id != '$contentId'";
    $result_check = mysqli_query($db, $sql_check);
    $row_check = mysqli_fetch_assoc($result_check);

    if ($row_check['count'] > 0) {
        echo "error: Content name already exists. Please choose a different name.";
        exit();
    }

    // Check if file is uploaded for PDF, Audio, Video, and Image content types
    if (in_array($contentType, ['PDF', 'Audio', 'Video', 'Image']) && isset($_FILES['contentFile']) && $_FILES['contentFile']['error'] != UPLOAD_ERR_NO_FILE) {
        // Validate and upload file
        $validation_result = validateFile($_FILES['contentFile']);
        if ($validation_result === "") {
            // Handle file upload
            $contentPath = uploadFile($_FILES['contentFile'], '../assets/private/content/');
            $sql = "UPDATE content 
                    SET content_name = '$contentName', content_type = '$contentType', 
                        content_text = '$contentText', content_path = '$contentPath' 
                    WHERE content_id = '$contentId'";
        } else {
            echo $validation_result;
            exit();
        }
    } else {
        // No new file uploaded, retain the existing content path
        $sql = "UPDATE content 
                SET content_name = '$contentName', content_type = '$contentType', 
                    content_text = '$contentText' 
                WHERE content_id = '$contentId'";
    }

    // Update content in the database
    if (mysqli_query($db, $sql)) {
        echo "success: Content updated successfully.";
    } else {
        echo "error: Error updating content: " . mysqli_error($db);
    }
} else {
    echo "error: Invalid request.";
}
?>
