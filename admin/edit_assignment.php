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

    // Allow certain file formats based on resource type
    $allowed_extensions = [];
    switch ($_POST['resourceType']) {
        case 'PDF':
            $allowed_extensions = ['pdf'];
            break;
        case 'Audio':
            $allowed_extensions = ['mp3', 'wav'];
            break;
        case 'Video':
            $allowed_extensions = ['mp4', 'avi'];
            break;
        case 'Image':
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            break;
        case 'Zip':
            $allowed_extensions = ['zip', 'rar'];
            break;
        default:
            return "Invalid resource type.";
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check if file extension is allowed
    if (!in_array($file_extension, $allowed_extensions)) {
        return "Invalid file format for the selected resource type.";
    }

    return ""; // File is valid
}

function uploadFile($file) {
    $target_directory = "../assets/private/assignments/";
    $unique_id = uniqid(); // Generate unique ID
    $file_name = "assignment_" . $unique_id . "_" . basename($file['name']); // Prefix with "assignment" and unique ID
    $target_file = $target_directory . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $target_file;
    } else {
        return "Error uploading the file.";
    }
}

// Handle form submission and file uploads for editing assignment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get assignment data from POST
    $assignmentId = $_POST['assignmentId'];
    $assignmentName = $_POST['assignmentName'];
    $assignmentDescription = $_POST['assignmentDescription'];
    $dueDate = $_POST['dueDate'];

    // Check if assignment name already exists for a different assignment
    $sql_check_name = "SELECT assignment_id FROM assignments WHERE assignment_name = '$assignmentName' AND assignment_id != '$assignmentId'";
    $result_check_name = mysqli_query($db, $sql_check_name);

    if (mysqli_num_rows($result_check_name) > 0) {
        echo "error: Assignment name already exists.";
        exit();
    }

    // Check if file is uploaded for PDF, Audio, or Video content types
    if (isset($_FILES['resourceFile'])) {
        // Validate and upload file
        $validation_result = validateFile($_FILES['resourceFile']);
        if ($validation_result === "") {
            // Handle file upload
            $resourcePath = uploadFile($_FILES['resourceFile']);
            
            // Update assignment resource in the database
            $sql_update_resource = "UPDATE assignment_resources SET resource_type = '$_POST[resourceType]', resource_path = '$resourcePath' WHERE assignment_id = '$assignmentId'";
            mysqli_query($db, $sql_update_resource);
        } else {
            echo $validation_result;
            exit();
        }
    }

    // Update assignment details in the database
    $sql_update_assignment = "UPDATE assignments SET assignment_name = '$assignmentName', assignment_description = '$assignmentDescription', due_date = '$dueDate' WHERE assignment_id = '$assignmentId'";
    if (mysqli_query($db, $sql_update_assignment)) {
        echo "success: Assignment updated successfully.";
    } else {
        echo "error: Error updating assignment: " . mysqli_error($db);
    }
} else {
    echo "error: Invalid request.";
}
?>
