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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted data
    $mark = $_POST['mark'];
    $studentId = $_POST['studentId'];
    $assignmentId = $_POST['assignmentId'];

    // Validate the mark (you can add more validation as needed)
    if (!is_numeric($mark) || $mark < 0 || $mark > 100) {
        // Invalid mark, display an error message
        echo "error: Invalid mark. Please enter a numeric value between 0 and 100.";
        exit; // Stop further execution
    }

    // Check if the mark already exists for the student and assignment
    $query = "SELECT * FROM marks WHERE student_id = ? AND assignment_id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ii", $studentId, $assignmentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0) {
        // Mark already exists, update the existing record
        $updateQuery = "UPDATE marks SET mark = ? WHERE student_id = ? AND assignment_id = ?";
        $updateStmt = mysqli_prepare($db, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "iii", $mark, $studentId, $assignmentId);
        
        if(mysqli_stmt_execute($updateStmt)) {
            // Mark updated successfully
            echo "success: Mark updated successfully.";
        } else {
            // Error occurred while updating the mark
            echo "error: Error: " . mysqli_error($db);
        }

        // Close the prepared statement for update
        mysqli_stmt_close($updateStmt);
    } else {
        // Mark does not exist, insert a new record
        $insertQuery = "INSERT INTO marks (student_id, assignment_id, mark) VALUES (?, ?, ?)";
        $insertStmt = mysqli_prepare($db, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "iii", $studentId, $assignmentId, $mark);

        if (mysqli_stmt_execute($insertStmt)) {
            // Mark saved successfully
            echo "success: Mark saved successfully.";
        } else {
            // Error occurred while saving the mark
            echo "error: Error: " . mysqli_error($db);
        }

        // Close the prepared statement for insert
        mysqli_stmt_close($insertStmt);
    }

    // Close the prepared statement for select and database connection
    mysqli_stmt_close($stmt);
    mysqli_close($db);
} else {
    // If the form is not submitted via POST method, display an error message
    echo "error: Form submission method not allowed.";
}
?>