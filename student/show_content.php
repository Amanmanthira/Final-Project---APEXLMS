<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["student_id"]))
{
    header('location:../frontend/login.php');
}

// Check if student account is active
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
$result = mysqli_query($db, $sql);
$row_count = mysqli_num_rows($result);

// Redirect to error.php if student account is not active
if($row_count != 1) {
    header('location:../frontend/error.php');
    exit(); // Ensure script execution stops after redirection
}

// Custom function to generate a secure token
function generateToken($content_path) {
    return md5($content_path);
}

// Custom function to fetch content details from the database
function getContentDetailsFromDatabase($content_id, $db) {
    $sql = "SELECT content_name, content_type, content_path, content_text, topic_id FROM content WHERE content_id = ? AND is_active = 1";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $content_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Fetch content details from the result set
        $row = $result->fetch_assoc();
        return $row;
    } else {
        // No content found for the given content ID
        return false;
    }
}

// Check if the student is enrolled in the course where the content belongs
function isStudentEnrolledInCourse($student_id, $topic_id, $db) {
    $sql = "
        SELECT ce.course_id 
        FROM course_enrollments ce 
        JOIN topics t ON t.course_id = ce.course_id 
        WHERE ce.student_id = ? 
          AND t.topic_id = ? 
          AND ce.is_active = 1
    ";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $student_id, $topic_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result->num_rows > 0;
}

// Check if content ID is provided in the URL
if (isset($_GET['content_id'])) {
    // Extract content ID from URL parameter
    $content_id = $_GET['content_id'];

    // Call the custom function to get content details from the database
    $contentDetails = getContentDetailsFromDatabase($content_id, $db);

    // Check if content details are retrieved successfully
    if ($contentDetails) {
        // Check if the student is enrolled in the course
        if (isStudentEnrolledInCourse($student_id, $contentDetails['topic_id'], $db)) {
            // Extract necessary information
            $content_type = $contentDetails['content_type'];
            $content_path = $contentDetails['content_path'];
            $content_description = $contentDetails['content_text'];

            // Generate a secure token using the content path
            $token = generateToken($content_path);

            // Store the token in session for later verification
            $_SESSION['content_tokens'][$content_id] = $token;

            // Define variables to store content card HTML
            $contentCard = '';

            $content_name = $contentDetails['content_name'];
            $content_text = $contentDetails['content_text'];

            // Define content card HTML
            $contentCard .= '<div class="card"><div class="card-body">';
            $contentCard .= '<h3 class="card-title"><i class="fa fa-file-text-o"></i> ' . $content_name . '</h3>';
            // Check if the content type is not a Zoom link
            if ($content_type !== 'Zoom Link') {
                // Add the content text
                $contentCard .= '<h6 class="card-text">' . $content_text . '</h6>';
            }
            // Add HTML based on content type
            switch ($content_type) {
                case 'PDF':
                    $contentCard .= '<embed src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" type="application/pdf" width="100%" height="600px" />';
                    break;
                case 'Audio':
                    $contentCard .= '<audio controls><source src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';
                    break;
                case 'Video':
                    $contentCard .= '<video controls width="100%" height="auto"><source src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" type="video/mp4">Your browser does not support the video element.</video>';
                    break;
                case 'Image':
                    $contentCard .= '<img src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" alt="' . $content_name . '" class="img-fluid" />';
                    break;
                case 'Zoom Link':
                    $contentCard .= '<p class="zoom-link">Zoom Link: <a href="' . $content_description . '" target="_blank">' . $content_description . '</a></p>';
                    break;
                default:
                    $contentCard .= '<p>Unsupported content type</p>';
            }

            // Close card divs
            $contentCard .= '</div></div>';
        } else {
            $contentCard = '<div class="card"><div class="card-body"><p>You are not enrolled in this course.</p></div></div>';
        }
    } else {
        // Content not found, display an error message
        $contentCard = '<div class="card"><div class="card-body"><p>Content not found.</p></div></div>';
    }
} else {
    // Content ID not provided, display an error message
    $contentCard = '<div class="card"><div class="card-body"><p>Content ID not provided.</p></div></div>';
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>APEX Institute | Show Content</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
    <!--[if lt IE 9]>
    <script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header fix-sidebar">
    <!-- Preloader - style you can find in spinners.css -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php require 'header.php'; ?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php require 'left_sidebar.php'; ?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper -->
        <div class="page-wrapper">
            <!-- Page content -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <?php echo $contentCard; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Container fluid -->
</div>
<!-- End Wrapper -->
<!-- All Jquery -->
<script src="js/lib/jquery/jquery.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="js/lib/bootstrap/js/popper.min.js"></script>
<script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="js/jquery.slimscroll.js"></script>
<!--Menu sidebar -->
<script src="js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
<!--Custom JavaScript -->
<script src="js/custom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.337/pdf.min.js"></script>

</body>

</html>
