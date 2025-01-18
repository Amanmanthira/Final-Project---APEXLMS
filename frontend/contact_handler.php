<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    $status = 'Pending';

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "All fields are required.";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit();
    }

    $sql = "INSERT INTO contact (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        echo "Prepare failed: " . htmlspecialchars($db->error);
        exit();
    }

    $stmt->bind_param("sssss", $name, $email, $subject, $message, $status);

    if ($stmt->execute()) {
        echo "Your message has been sent successfully!";
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $db->close();
} else {
    echo "Invalid request method.";
}
?>
