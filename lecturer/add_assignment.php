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

// Handle form submission and file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get assignment data from POST
    $assignmentName = $_POST['assignmentName'];
    $assignmentDescription = $_POST['assignmentDescription'];
    $dueDate = $_POST['dueDate'];
    $topicId = $_POST['topicId'];

    // Check if the assignment name already exists
    $check_sql = "SELECT COUNT(*) FROM assignments WHERE assignment_name = '$assignmentName'";
    $check_result = mysqli_query($db, $check_sql);
    $row = mysqli_fetch_array($check_result);
    if ($row[0] > 0) {
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
        } else {
            echo $validation_result;
            exit();
        }
    } else {
        // No file uploaded
        $resourcePath = '';
    }

    // Insert assignment into the database
    $sql = "INSERT INTO assignments (assignment_name, assignment_description, due_date, topic_id, created_at, is_active) 
            VALUES ('$assignmentName', '$assignmentDescription', '$dueDate', '$topicId', NOW(), 1)";
    if (mysqli_query($db, $sql)) {
        // Get the last inserted assignment ID
        $assignmentId = mysqli_insert_id($db);
        
        // Insert assignment resource into the database if a file is uploaded
        if (!empty($resourcePath)) {
            $sql_resource = "INSERT INTO assignment_resources (assignment_id, resource_type, resource_path) 
                             VALUES ('$assignmentId', '$_POST[resourceType]', '$resourcePath')";
            mysqli_query($db, $sql_resource);
        }

        echo "success: Assignment added successfully.";
    } else {
        echo "error: Error adding assignment: " . mysqli_error($db);
    }
} else {
    echo "error: Invalid request.";
}
?>
