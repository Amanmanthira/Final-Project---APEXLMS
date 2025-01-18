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

// Check if department_id is set in the POST data
if(isset($_POST['department_id'])) {
    $departmentId = $_POST['department_id'];
    
    // Update the is_active field of the department to 1 (active)
    $sql = "UPDATE Departments SET is_active = 1 WHERE department_id = '$departmentId'";
    $query = mysqli_query($db, $sql);
    
    if($query) {
        // Department activation successful
        $response = array("status" => "success");
        echo json_encode($response);
    } else {
        // Department activation failed
        $response = array("status" => "error");
        echo json_encode($response);
    }
} else {
    // Department ID not provided in POST data
    $response = array("status" => "error", "message" => "Department ID not provided");
    echo json_encode($response);
}
?>
