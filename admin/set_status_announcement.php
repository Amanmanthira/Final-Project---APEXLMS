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
