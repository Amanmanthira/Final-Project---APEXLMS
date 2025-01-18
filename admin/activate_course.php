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

if(isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Update the course to set is_active to 1 (active)
    $update_sql = "UPDATE courses SET is_active = 1 WHERE course_id = '$course_id'";
    $update_query = mysqli_query($db, $update_sql);
    
    if($update_query) {
        // Redirect back to the page where inactive courses are displayed with a success message
        header("Location: all_courses.php?response=Course activated successfully");
        exit();
    } else {
        // Redirect back to the page where inactive courses are displayed with an error message
        header("Location: all_courses.php?response=Failed to activate course");
        exit();
    }
} else {
    // Redirect back to the page where inactive courses are displayed with an error message if course_id is not provided
    header("Location: all_courses.php?response=Course ID not provided");
    exit();
}
?>
