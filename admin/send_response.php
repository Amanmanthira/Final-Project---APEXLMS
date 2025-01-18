<?php
include("../connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message_id = $_POST['message_id'];
    $subject = $_POST['response_subject'];
    $message = $_POST['response_message'];

    // Fetch recipient email and name
    $query = "SELECT email, name FROM contact WHERE id=?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $recipient_email = $row['email'];
        $recipient_name = $row['name'];

        // Send email
        $mail = new PHPMailer(true); // Use true for exceptions
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;
            $mail->Username = 'amanmanthira32326@gmail.com';
            $mail->Password = 'cboa qnsj iuln mrqy';
            $mail->setFrom('amanmanthira32326@gmail.com', 'APEX INSTITUTE');
            $mail->addReplyTo('amanmanthira32326@gmail.com', 'APEX INSTITUTE');
            $mail->addAddress($recipient_email, $recipient_name);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML(true);

            if ($mail->send()) {
                // Update message status and response
                $updateQuery = "UPDATE contact SET status='Responded', response=? WHERE id=?";
                $stmt = $db->prepare($updateQuery);
                $stmt->bind_param("si", $message, $message_id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update status and response']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $mail->ErrorInfo]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Recipient not found']);
    }
}
?>
