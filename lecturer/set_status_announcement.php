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
// Check if announcement ID is provided
if(isset($_POST['announcementId'])) {
    $announcementId = $_POST['announcementId'];

    // Fetch the current status of the announcement
    $status_query = "SELECT is_active FROM course_announcements WHERE announcement_id = '$announcementId'";
    $status_result = mysqli_query($db, $status_query);

    if ($status_result) {
        $row = mysqli_fetch_assoc($status_result);
        $current_status = $row['is_active'];

        // Invert the current status
        $new_status = $current_status == 1 ? 0 : 1;

        // Update the status to the new value
        $update_query = "UPDATE course_announcements SET is_active = '$new_status' WHERE announcement_id = '$announcementId'";
        if (mysqli_query($db, $update_query)) {
            echo "Announcement status updated successfully.";
        } else {
            echo "Error updating announcement status: " . mysqli_error($db);
        }
    } else {
        echo "Error fetching announcement status: " . mysqli_error($db);
    }
} else {
    echo "Invalid request.";
}
?>
