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
// Function to generate a unique token for a resource path and store it in a session
function generateResourceToken($resource_path) {
    // Generate a unique token for the resource path
    $token = md5(uniqid($resource_path, true));
    
    // Store the token and its corresponding resource path in the session
    $_SESSION['resource_tokens'][$token] = $resource_path;
    
    // Return the generated token
    return $token;
}
// Function to generate a token for the submitted file path
function generateSubmissionToken($submission_file) {
    // Generate a unique token for the submission file path
    $token = md5(uniqid($submission_file, true));
    
    // Store the token and its corresponding submission file path in the session
    $_SESSION['submission_tokens'][$token] = $submission_file;
    
    // Return the generated token
    return $token;
}

// Function to fetch assignment details from the database
function getAssignmentDetailsFromDatabase($assignment_id, $db) {
    $sql = "SELECT * FROM assignments WHERE assignment_id = ? AND is_active = 1";
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
// Function to check if the student is enrolled in the course
function isStudentEnrolledInCourse($student_id, $db, $assignment_id) {
    // Fetch assignment details from the database based on the assignment ID
    $assignmentDetails = getAssignmentDetailsFromDatabase($assignment_id, $db);

    // Check if assignment details are retrieved successfully
    if ($assignmentDetails) {
        // Extract topic ID from assignment details
        $topic_id = $assignmentDetails['topic_id'];

        // SQL query to check enrollment
        $sql = "SELECT ce.* FROM course_enrollments ce
                INNER JOIN topics t ON ce.course_id = t.course_id
                WHERE ce.student_id = ? AND t.topic_id = ? AND ce.is_active = 1";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $student_id, $topic_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    } else {
        // Handle the case where assignment details are not retrieved
        return false;
    }
}
// Check if assignment ID is provided in the URL
if (isset($_GET['id'])) {
    // Fetch assignment details from the database based on the assignment ID
    $assignment_id = $_GET['id'];

    // Call the function to get assignment details
    $assignmentDetails = getAssignmentDetailsFromDatabase($assignment_id, $db);

  // Function to check if submission is allowed based on due date
function isSubmissionAllowed($due_date) {
    // Convert due date to timestamp
    $due_timestamp = strtotime($due_date);
    
    // Get current timestamp
    $current_timestamp = time();
    
    // Calculate difference in seconds between current time and due time
    $time_difference = $due_timestamp - $current_timestamp;
    
    // Calculate difference in days
    $days_difference = floor($time_difference / (60 * 60 * 24));
    
    // Submission is allowed if current time is before due date or one day after due date
    return ($days_difference >= -1);
}
// Check if the current student has already submitted for this assignment
function hasSubmitted($student_id, $assignment_id, $db) {
    $sql = "SELECT * FROM assignment_submissions WHERE student_id = ? AND assignment_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $student_id, $assignment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}
// Fetch submitted details and mark for the current student and assignment
function getSubmittedDetails($student_id, $assignment_id, $db) {
    $sql = "SELECT s.*, m.mark 
            FROM assignment_submissions s 
            LEFT JOIN marks m 
            ON s.student_id = m.student_id AND s.assignment_id = m.assignment_id
            WHERE s.student_id = ? AND s.assignment_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $student_id, $assignment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
if (isStudentEnrolledInCourse($student_id, $db, $assignment_id)) {

    // Check if assignment details are retrieved successfully
    if ($assignmentDetails) {
        // Extract assignment details
        $assignment_name = $assignmentDetails['assignment_name'];
        $assignment_description = $assignmentDetails['assignment_description'];
        $due_date = $assignmentDetails['due_date'];

        // Check if submission is allowed
        $submissionAllowed = isSubmissionAllowed($due_date);

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
                $contentCard .= "<li><i class='$icon_class'></i> <a href='resource.php?token=$resource_token&id=$assignment_id'>" . basename($resource_path) . "</a></li>";
            }
            $contentCard .= "</ul>";
        } else {
            $contentCard .= "<p class='card-text'>No resources available for this assignment.</p>";
        }
                // Check if the student has already submitted for this assignment
if (hasSubmitted($student_id, $assignment_id, $db)) {
    // Fetch submitted details and mark
    $submittedDetails = getSubmittedDetails($student_id, $assignment_id, $db);
    $submission_file = $submittedDetails['submission_path'];
    $mark = $submittedDetails['mark'];

    // Generate token for the submitted file
    $submission_token = generateSubmissionToken($submission_file);

    // Display submitted details and mark
    $contentCard .= "<div class='mt-3'>";
    $contentCard .= "<h5 class='card-title'><i class='fa fa-check-circle'></i> Submission Details</h5>";
    $contentCard .= "<p class='card-text'><strong>Submitted File:</strong> <a href='submitted_file.php?token=$submission_token'>" . basename($submission_file) . "</a></p>";
    
    // Check if a mark is available
    if ($mark !== null) {
        $contentCard .= "<p class='card-text'><strong>Mark:</strong> $mark</p>";
    } else {
        $contentCard .= "<p class='card-text'><strong>Mark:</strong> Not graded yet</p>";
    }
    
    $contentCard .= "</div>";
} else {
    // Display submission form if submission is allowed
    if ($submissionAllowed) {
        $contentCard .= "<div class='mt-3'>";
        $contentCard .= "<h5 class='card-title'><i class='fa fa-upload'></i> Submissions</h5>";
        $contentCard .= "<form id='submissionForm' enctype='multipart/form-data'>";
        $contentCard .= "<div class='custom-file'>";
        $contentCard .= "<input type='file' class='custom-file-input' id='submission_file' name='submission_file' required>";
        $contentCard .= "<label class='custom-file-label' for='submission_file'>Choose file (docx, pdf, zip, rar)</label>";
        $contentCard .= "</div>";
        $contentCard .= "<input type='hidden' name='assignment_id' value='$assignment_id'>";
        $contentCard .= "<button type='button' class='btn btn-primary mt-2'>Submit</button>";
        $contentCard .= "</form>";
        $contentCard .= "</div>";
    } else {
        $contentCard .= "<div class='mt-3'>";
        $contentCard .= "<p class='text-danger'>Submission is not allowed at this time.</p>";
        $contentCard .= "</div>";
    }
}

        // Close card body and card div
        $contentCard .= '</div>';
        $contentCard .= '</div>';
        $contentCard .= '</div>';
  
    } else {
        // If assignment not found, display error message
        $contentCard = "<div class='col-lg-6'><p>Assignment not found.</p></div>";
    }
} else {
    // Display an error message indicating that the student is not enrolled in the course
    $contentCard = "<div class='col-lg-6'><p>You are not enrolled in this course.</p></div>";
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


        <div id="submissionAlert" class="alert d-none" role="alert">
                        <!-- Alert content will be inserted here dynamically -->
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
    <script>
$(document).ready(function(){
    // When a file is selected, update the label text with the file name
    $('#submission_file').change(function(){
        var fileName = $(this).val().split('\\').pop();
        if(fileName){
            $(this).next('.custom-file-label').html(fileName);
        } else {
            $(this).next('.custom-file-label').html('Choose file (docx, pdf, zip, rar)');
        }
    });
    $('#submissionForm button[type="button"]').click(function(e){ // Changed selector to target the button inside #submissionForm
        e.preventDefault();
        var formData = new FormData($('#submissionForm')[0]);
        $.ajax({
            type: 'POST',
            url: 'process_submission.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                if(response.trim() === 'success'){
                    $('#submissionAlert').removeClass('alert-danger').addClass('alert-success').html('Submission successful.').removeClass('d-none').addClass('show');
                    setTimeout(function(){
                        location.reload();
                    }, 3000); // Reload the page after 3 seconds
                    } else {
                    $('#submissionAlert').removeClass('alert-success').addClass('alert-danger').html(response).removeClass('d-none').addClass('show');
                }
            }
        });
    });
});

</script>


   
</body>

</html>
