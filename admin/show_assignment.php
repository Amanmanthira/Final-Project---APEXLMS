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
// Function to generate a unique token for a resource path and store it in a session
function generateResourceToken($resource_path) {
    // Generate a unique token for the resource path
    $token = md5(uniqid($resource_path, true));
    
    // Store the token and its corresponding resource path in the session
    $_SESSION['resource_tokens'][$token] = $resource_path;
    
    // Return the generated token
    return $token;
}

// Function to fetch assignment details from the database
function getAssignmentDetailsFromDatabase($assignment_id, $db) {
    $sql = "SELECT * FROM assignments WHERE assignment_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $assignment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Fetch assignment details from the result set
        $row = $result->fetch_assoc();
        return $row;
    } else {
        // No assignment found for the given assignment ID
        return false;
    }
}

// Check if assignment ID is provided in the URL
if (isset($_GET['id'])) {
    // Fetch assignment details from the database based on the assignment ID
    $assignment_id = $_GET['id'];

    // Call the function to get assignment details
    $assignmentDetails = getAssignmentDetailsFromDatabase($assignment_id, $db);

    // Check if assignment details are retrieved successfully
    if ($assignmentDetails) {
        // Extract assignment details
        $assignment_name = $assignmentDetails['assignment_name'];
        $assignment_description = $assignmentDetails['assignment_description'];
        $due_date = $assignmentDetails['due_date'];

        // Create the card HTML with assignment details
        $contentCard = '<div class="col-lg-6">';
        $contentCard .= '<div class="card">';
        $contentCard .= '<div class="card-body">';
        $contentCard .= "<h4 class='card-title'><i class='fa fa-tasks'></i> $assignment_name</h4>";
        $contentCard .= "<p class='card-text'><strong>Description:</strong> $assignment_description</p>";
        $contentCard .= "<p class='card-text'><strong>Due Date:</strong> $due_date</p>";

        // Fetch associated resources for the assignment
        $sql = "SELECT * FROM assignment_resources WHERE assignment_id = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $assignment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Display associated resources
        if ($result->num_rows > 0) {
            $contentCard .= "<h5 class='card-title'><i class='fa fa-file'></i> Resources</h5>";
            $contentCard .= "<ul class='card-text'>";
            while ($row = $result->fetch_assoc()) {
                $resource_type = $row['resource_type'];
                $resource_path = $row['resource_path'];
                $icon_class = getResourceIcon($resource_type);
                $resource_token = generateResourceToken($resource_path);
                $contentCard .= "<li><i class='$icon_class'></i> <a href='resource.php?token=$resource_token'>" . basename($resource_path) . "</a></li>";            }
            $contentCard .= "</ul>";
        } else {
            $contentCard .= "<p class='card-text'>No resources available for this assignment.</p>";
        }

        // Close card body and card div
        $contentCard .= '</div>';
        $contentCard .= '</div>';
        $contentCard .= '</div>';
    } else {
        // If assignment not found, display error message
        $contentCard = "<div class='col-lg-6'><p>Assignment not found.</p></div>";
    }
}

// Function to get the icon class based on the resource type
function getResourceIcon($resource_type) {
    switch ($resource_type) {
        case 'PDF':
            return 'fa fa-file-pdf-o';
        case 'Video':
            return 'fa fa-file-video-o';
        case 'Image':
            return 'fa fa-file-image-o';
        case 'Audio':
            return 'fa fa-volume-up';
        case 'Zip':
            return 'fa fa-file-archive-o';
        default:
            return 'fa fa-file';
    }
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
    <title>APEX Institute  | Show Assignment </title>
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
