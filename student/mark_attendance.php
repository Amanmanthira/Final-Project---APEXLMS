<?php
include("../connection/connect.php");
session_start();

if (empty($_SESSION["student_id"])) {
    error_log("Error: User not logged in.");
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$student_id = $_SESSION["student_id"];
$topic_id = $_POST['topic_id'];
$content_id = $_POST['content_id']; // New content_id received from the client

// Log student, topic, and content details
error_log("Debug: Student ID = $student_id, Topic ID = $topic_id, Content ID = $content_id");

// Check if the student is enrolled in the course and retrieve the course_id
$sql_enrollment_check = "SELECT ce.course_id FROM course_enrollments ce
                         JOIN topics t ON ce.course_id = t.course_id
                         WHERE ce.student_id = ? AND t.topic_id = ? AND ce.is_active = 1";
$stmt = $db->prepare($sql_enrollment_check);
if (!$stmt) {
    error_log("Error: Failed to prepare enrollment check query - " . $db->error);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit();
}
$stmt->bind_param("ii", $student_id, $topic_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("Info: Student not enrolled in the course or invalid topic. Student ID = $student_id, Topic ID = $topic_id");
    echo json_encode(['success' => false, 'message' => 'You are not enrolled in this course or topic is invalid.']);
    exit();
}

// Retrieve the course_id
$row = $result->fetch_assoc();
$course_id = $row['course_id'];
error_log("Debug: Retrieved Course ID = $course_id");

// Check if attendance is already marked
$sql_check_attendance = "SELECT * FROM attendance WHERE student_id = ? AND topic_id = ? AND content_id = ?";
$stmt = $db->prepare($sql_check_attendance);
if (!$stmt) {
    error_log("Error: Failed to prepare attendance check query - " . $db->error);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit();
}
$stmt->bind_param("iii", $student_id, $topic_id, $content_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    error_log("Info: Attendance already marked for Student ID = $student_id, Topic ID = $topic_id, Content ID = $content_id");
    echo json_encode(['success' => false, 'message' => 'Attendance already marked.']);
    exit();
}

// Mark attendance
$sql_mark_attendance = "INSERT INTO attendance (student_id, course_id, topic_id, content_id, attendance_date) VALUES (?, ?, ?, ?, NOW())";
$stmt = $db->prepare($sql_mark_attendance);
if (!$stmt) {
    error_log("Error: Failed to prepare attendance marking query - " . $db->error);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit();
}
$stmt->bind_param("iiii", $student_id, $course_id, $topic_id, $content_id);

if ($stmt->execute()) {
    error_log("Info: Attendance successfully marked for Student ID = $student_id, Course ID = $course_id, Topic ID = $topic_id, Content ID = $content_id");
    echo json_encode(['success' => true]);
} else {
    error_log("Error: Failed to mark attendance for Student ID = $student_id, Course ID = $course_id, Topic ID = $topic_id, Content ID = $content_id - " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to mark attendance.']);
}
?>
