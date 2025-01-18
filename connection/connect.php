<?php

//database connection
$servername = "localhost"; //server
$username = "root"; //username
$password = ""; //password
$dbname = "lms";  //database

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($db->connect_error) {
    // Redirect to connection error page
    header("Location: connection_error.php");
    exit(); // Stop further execution
}

// Function to prepare SQL statements
function prepare_stmt($sql) {
    global $db;
    return $db->prepare($sql);
}

?>