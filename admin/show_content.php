<!DOCTYPE html>
<html lang="en">
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
// Custom function to generate a secure token
function generateToken($content_path) {
    return md5($content_path);
}

// Custom function to fetch content details from the database
function getContentDetailsFromDatabase($content_id, $db) {
    $sql = "SELECT content_name, content_type, content_path, content_text FROM content WHERE content_id = ?";
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

// Check if content ID is provided in the URL
if (isset($_GET['content_id'])) {
    // Extract content ID from URL parameter
    $content_id = $_GET['content_id'];

    // Call the custom function to get content details from the database
    $contentDetails = getContentDetailsFromDatabase($content_id, $db);

    // Check if content details are retrieved successfully
    if ($contentDetails) {
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
                // For PDF files, embed them using the <embed> tag
                $contentCard .= '<embed src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" type="application/pdf" width="100%" height="600px" />';
                break;
            case 'Audio':
                // For audio files, use the <audio> tag
                $contentCard .= '<audio controls><source src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';
                break;
            case 'Video':
                // For video files, use the <video> tag
                $contentCard .= '<video controls width="100%" height="auto"><source src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" type="video/mp4">Your browser does not support the video element.</video>';
                break;
            case 'Image':
                // For video files, use the <img> tag
                $contentCard .= '<img src="process_content.php?content_id=' . $content_id . '&token=' . $token . '" alt="' . $content_name . '" class="img-fluid" />';
                break;
            case 'Zoom Link':
                // For Zoom links, display them as a link
                $contentCard .= '<p class="zoom-link">Zoom Link: <a href="' . $content_description . '" target="_blank">' . $content_description . '</a></p>';
                break;
            default:
                // Handle unsupported content types
                $contentCard .= '<p>Unsupported content type</p>';
        }

        // Close card divs
        $contentCard .= '</div></div>';
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
    <title>APEX Institute  | Show Content</title>
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
       <?php
        // Require the header.php file
        require('header.php');
        ?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php
        // Require the header.php file
        require('left_sidebar.php');
        ?>
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
    <!-- End Page wrapper -->
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
