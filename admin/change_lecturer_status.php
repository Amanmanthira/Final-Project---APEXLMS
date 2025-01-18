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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_POST['lecturer_id'];
    $new_status = $_POST['new_status'];

    // Update lecturer status in the database
    $sql = "UPDATE lecturers SET is_active = ? WHERE lecturer_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $new_status, $lecturer_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }

    $stmt->close();
    $db->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
