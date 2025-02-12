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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom Styling */
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            margin: 0;
            padding: 0;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .padding {
            padding: 20px;
        }
        #chat-container {
            height: 500px;
            overflow-y: auto;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            scrollbar-width: thin;
            scrollbar-color: #6C63FF #f1f1f1;
        }
        #chat-container::-webkit-scrollbar {
            width: 8px;
        }
        #chat-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        #chat-container::-webkit-scrollbar-thumb {
            background: #6C63FF;
            border-radius: 10px;
        }
        .chat-message {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            animation: fadeIn 0.5s ease-out;
        }
        .chat-message .user {
            text-align: right;
            font-weight: 900;
            color: #fff;
            background: linear-gradient(135deg, #6C63FF, #4E4DFF);
            max-width: 75%;
            margin-left: auto;
            padding: 12px 18px;
            border-radius: 20px 20px 0 20px;
            word-wrap: break-word;
            box-shadow: 0 4px 10px rgba(108, 99, 255, 0.2);
            position: relative;
        }
        .chat-message .user::before {
            content: '🧑‍🎓';
            position: absolute;
            left: -50px;
            top: 0;
            font-size: 35px;
        }
        .chat-message .bot {
            text-align: left;
            font-weight: 900;
            color: #333;
            background: #f1f1f1;
            max-width: 75%;
            margin-right: auto;
            padding: 12px 18px;
            border-radius: 20px 20px 20px 0;
            word-wrap: break-word;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .chat-message .bot::before {
            content: '🤖';
            position: absolute;
            right: -60px;
            top: 0;
            font-size: 34px;
        }
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
            background: linear-gradient(135deg, #6C63FF, #4E4DFF);
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
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 99, 255, 0.3);
        }
        #loading {
            display: none;
            position: absolute;
            right: 10px;
            top: 10px;
        }
        .typing-indicator {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin: 10px 0;
            font-style: italic;
            background: rgba(255, 255, 255, 0.9);
            color: #6C63FF;
            padding: 12px 18px;
            border-radius: 20px;
            max-width: fit-content;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #6C63FF;
            border-radius: 50%;
            display: inline-block;
            margin: 0 3px;
            animation: bounce 1.4s infinite ease-in-out;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); opacity: 0.3; }
            50% { transform: translateY(-5px); opacity: 1; }
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 20px;
        }
        .welcome-message img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    margin-bottom: 10px;
    animation: float 3s ease-in-out infinite; /* applies floating animation */
}

@keyframes float {
    0% {
        transform: translateY(0); /* starting position */
    }
    50% {
        transform: translateY(-10px); /* moves slightly up */
    }
    100% {
        transform: translateY(0); /* returns to original position */
    }
}

.welcome-message p {
    font-size: 1.5rem; /* slightly larger size for modern look */
    color: #00bcd4; /* a vibrant techy color */
    font-weight: 600; /* semi-bold for a techy feel */
    line-height: 1.8; /* improved readability with extra space */
    font-family: 'Roboto', 'Helvetica Neue', sans-serif; /* modern, techy font */
    letter-spacing: 0.5px; /* slight spacing for a clean look */
    text-transform: uppercase; /* gives it a more futuristic feel */
    background: linear-gradient(90deg, #00bcd4, #8e2de2); /* gradient text effect */
    -webkit-background-clip: text; /* clips the gradient to the text */
    color: transparent; /* makes the text transparent to show gradient */
    text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08); /* subtle shadow for depth */
    transition: transform 0.3s ease-in-out; /* smooth hover effect */
}

.welcome-message p:hover {
    transform: scale(1.05); /* subtle scale on hover */
    color: #fff; /* changes text color on hover for contrast */
}


    </style>
</head>

<body class="fix-header">
    <div id="main-wrapper">
        <?php require 'header.php'; ?>
        <?php require 'left_sidebar.php'; ?>
        <div class="page-wrapper padding">
            <div class="container-fluid">
                <h3 class="text-primary">🤖 <span style="font-weight: 900;">Apex</span><span style="font-weight: 100;">Bot</span></h3>
                <div class="card" style="margin: 10px;">
                    <div class="card-body">
                        <!-- Chatbot UI -->
                        <div id="chat-container">
                            <div id="chat-messages">
                                <!-- Welcome Message -->
                                <div class="welcome-message">
                                    <img src="./images/bott.png" alt="Chatbot Image">
                                    <p>Hi, I'm ApexBot! How can I help you today?</p>
                                </div>
                            </div>
                            <div id="typing-indicator" class="typing-indicator" style="display: none;">
                                <span>.</span>
                                <span>.</span>
                                <span>.</span>
                            </div>
                        </div>
                        <div class="input-group mt-3">
                            <input type="text" id="user-input" class="form-control" placeholder="Type your message...">
                            <button id="send-btn" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
       $(document).ready(function() {
    let lastUserInput = ''; // Store the last user input
    let isFirstMessage = true; // Track if it's the first message

    $('#send-btn').click(function() {
        const userInput = $('#user-input').val().trim();
        if (userInput === '') {
            Swal.fire('Error', 'Please enter a message!', 'error');
            return;
        }

        if (userInput !== lastUserInput) {
            // Remove the welcome message if it's the first message
            if (isFirstMessage) {
                $('.welcome-message').remove();
                isFirstMessage = false;
            }

            $('#chat-messages').append(`<div class="chat-message"><span class="user">You:</span> ${userInput}</div>`);
            lastUserInput = userInput;
        }

        $('#user-input').val('');
        $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);

        $('#typing-indicator').show();

        setTimeout(function() {
            $.ajax({
                url: 'http://127.0.0.1:5000/chat',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ message: userInput }),
                success: function(response) {
                    const botMessage = response.response || ""; 
                    $('#typing-indicator').hide();

                    if (botMessage) {
                        $('#chat-messages').append(`<div class="chat-message"><span class="bot">Bot:</span></div>`);
                        const paragraphs = botMessage.split(/\r?\n/);
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
                    $('#typing-indicator').hide();
                }
            });
        }, 4000); // Show typing indicator for 4 seconds
    });
});
    </script>
</body>
</html>
