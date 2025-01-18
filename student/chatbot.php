<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Authentication
if (isset($_SESSION["student_id"])) {
    $student_id = $_SESSION["student_id"];
    $sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    if (mysqli_num_rows($result) != 1) {
        header('location:../frontend/error.php');
        exit();
    }
} else {
    header('location:../frontend/login.php');
    exit();
}
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APEX Chatbot</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom Styling */
        body {
            font-family: 'Arial', sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .padding{
            padding : 20px;
        }
        #chat-container {
            height: 500px;
            overflow-y: auto;
            border-radius: 15px;
            background: linear-gradient(135deg, #e9eff4, #f1f7fc);
            padding: 20px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            transition: background 0.3s ease;
            margin-bottom: 20px;
        }

        .chat-message {
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            animation: fadeIn 0.5s ease-out;
        }

        /* User's message styling */
        .chat-message .user {
            text-align: right;
            font-weight: bold;
            color: #fff;
            background-color: #6C63FF;
            max-width: 75%;
            margin-left: auto;
            padding: 15px 20px;
            border-radius: 20px 20px 0 20px;
            word-wrap: break-word;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Bot's message styling */
        .chat-message .bot {
            text-align: left;
            font-weight: normal;
            color: #333;
            background-color: #28a745;
            max-width: 75%;
            margin-right: auto;
            padding: 15px 20px;
            border-radius: 20px 20px 20px 0;
            word-wrap: break-word;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Input group styling */
        .input-group {
            position: relative;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        #user-input {
            border-radius: 30px;
            border: 1px solid #ddd;
            padding: 15px 20px;
            font-size: 1rem;
            width: 80%;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        #user-input:focus {
            border-color: #6C63FF;
            box-shadow: 0 0 10px rgba(108, 99, 255, 0.5);
        }

        #send-btn {
            background: linear-gradient(45deg, #6C63FF, #4E4DFF);
            border-radius: 30px;
            color: white;
            font-size: 1rem;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(108, 99, 255, 0.2);
        }

        #send-btn:hover {
            background: linear-gradient(45deg, #4E4DFF, #6C63FF);
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
        }

        #loading {
            display: none;
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 18px;
            color: #28a745;
        }

        /* Animation for chat messages */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="fix-header">
    <div id="main-wrapper">
        <?php require 'header.php'; ?>
        <?php require 'left_sidebar.php'; ?>
        <div class="page-wrapper padding">
            <div class="container-fluid">
                <h3 class="text-primary">🤖 Apex Bot</h3>
                <div class="card">
                    <div class="card-body">
                        <!-- Chatbot UI -->
                        <div id="chat-container">
                            <div id="chat-messages"></div>
                        </div>
                        <div class="input-group mt-3">
                            <input type="text" id="user-input" class="form-control" placeholder="Type your message...">
                            <button id="send-btn" class="btn btn-primary">Send</button>
                            <div id="loading">...</div> <!-- Loading indicator -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let lastUserInput = ''; // Store the last user input

            // Send message to chatbot
            $('#send-btn').click(function() {
                const userInput = $('#user-input').val().trim();
                if (userInput === '') {
                    Swal.fire('Error', 'Please enter a message!', 'error');
                    return;
                }

                // Only add user message if it's different from the last one
                if (userInput !== lastUserInput) {
                    // Add user message to chat
                    $('#chat-messages').append(`<div class="chat-message"><span class="user">You:</span> ${userInput}</div>`);
                    lastUserInput = userInput; // Update the last input with the new one
                }

                $('#user-input').val('');
                $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);

                // Show loading indicator
                $('#loading').show();

                // Send AJAX request to chatbot API
                $.ajax({
                    url: 'http://127.0.0.1:5000/chat', // API endpoint
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        message: userInput
                    }),
                    success: function(response) {
                        // Ensure the response contains the 'reply' field
                        const botMessage = response.response || ""; // Empty string if no reply exists
                        if (botMessage) {
                            // Add bot message header once
                            $('#chat-messages').append(`<div class="chat-message"><span class="bot">Bot:</span></div>`);

                            // Break bot message into paragraphs and append separately
                            const paragraphs = botMessage.split(/\r?\n/); // Split by newlines or other delimiters
                            paragraphs.forEach(function(paragraph) {
                                if (paragraph.trim() !== '') {
                                    $('#chat-messages').append(`<div class="chat-message">${paragraph}</div>`);
                                }
                            });
                            $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to connect to the chatbot API!', 'error');
                    },
                    complete: function() {
                        // Hide loading indicator
                        $('#loading').hide();
                    }
                });
            });
        });
    </script>

</body>

</html>
