<?php
session_start();
session_destroy();
$url = '../frontend/index.php';
header('Location: ' . $url);

?>