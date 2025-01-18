<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if lecturer_id is set in session
if(isset($_SESSION["apex_lecturer_id"])) {
    // Retrieve lecturer_id from session
    $lecturer_id = $_SESSION["apex_lecturer_id"];

    // Check if lecturer account is active
    $sql = "SELECT * FROM lecturers WHERE lecturer_id = '$lecturer_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);

    // Redirect to error.php if lecturer account is not active
    if($row_count != 1) {
        header('location: error.php');
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if lecturer_id is not set in session
    header('location: index.php');
    exit(); // Ensure script execution stops after redirection
}

// Retrieve the course_id from the GET parameters
$course_id = $_GET['course_id'];

// Check if the lecturer is assigned to the course
$query = "SELECT * FROM course_lecturers WHERE course_id = ? AND lecturer_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $course_id, $lecturer_id);
$stmt->execute();
$stmt->store_result();
$row_count = $stmt->num_rows;
$stmt->close();

if ($row_count != 1) {
    // Redirect to my_courses.php if the lecturer is not assigned to the course
    header('location: my_courses.php');
    exit(); // Ensure script execution stops after redirection
}


// Initialize error and success messages
$errorMsg = "";
$successMsg = "";

$courseFound = false;

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    // Fetch course details from the database
    $course_query = "SELECT course_name FROM courses WHERE course_id = '$course_id'";
    $course_result = mysqli_query($db, $course_query);
    if ($course_result) {
        $course_data = mysqli_fetch_assoc($course_result);
        if ($course_data) {
            $courseFound = true;
            $course_name = $course_data['course_name'];
        } else {
            // Handle error if course details are not found
            $errorMsg = "Error fetching course details";
        }
    } else {
        // Handle database query error
        $errorMsg = "Error executing database query";
    }
} else {
    // If course ID is not provided in the URL, display an error message or handle it as per your requirement
    $errorMsg = "Course ID is missing!";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the input
    $course_id = $_GET['course_id'];
    $topic_name = $_POST['topicName'];

    // Check if the topic already exists
    $check_query = "SELECT * FROM topics WHERE course_id = '$course_id' AND topic_name = '$topic_name'";
    $result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Topic already exists, set error message
        $errorMsg = "Topic already exists!";
    } else {
        // Insert the topic into the database
        $insert_query = "INSERT INTO topics (course_id, topic_name, creation_date) VALUES ('$course_id', '$topic_name', NOW())";
        if (mysqli_query($db, $insert_query)) {
            // Success message
            $successMsg = "Topic added successfully!";
        } else {
            // Error message
            $errorMsg = "Error adding topic: " . mysqli_error($db);
        }
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
    <title>APEX Institute  | Add Topic </title>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add Topic</h4>
                            <h6 class="card-subtitle">Add Topic for <?php echo isset($course_name) ? $course_name : 'Unknown Course'; ?></h6>

                    <!-- Add Topic Form -->
                    <div class="container mt-4">
                        <?php if(isset($errorMsg) && !empty($errorMsg)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errorMsg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($successMsg) && !empty($successMsg)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $successMsg; ?>
                            </div>
                            <script>
                                // JavaScript to redirect to view_course.php after success
                                setTimeout(function() {
                                    window.location.href = "view_course.php?course_id=<?php echo $course_id; ?>";
                                }, 2000); // Redirect after 2 seconds (2000 milliseconds)
                            </script>
                        <?php endif; ?>
                        <?php if ($courseFound): ?>
                            <form id="addTopicForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?course_id=' . $course_id); ?>">
                                <div class="form-group">
                                    <label for="topicName">Topic Name</label>
                                    <input type="text" class="form-control" id="topicName" name="topicName" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Topic</button>
                            </form>
                        <?php endif; ?>
                      </div>

                        </div>
                    </div>
                </div>
            </div>
   
</div>

           


    <!-- End Modal HTML -->
            <!-- End Page Content -->
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

</script>

</body>

</html>