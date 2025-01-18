<?php
include("../connection/connect.php");
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
$error = '';
$success = '';

// Fetch lecturers list from the database
$lecturers_query = "SELECT * FROM lecturers";
$lecturers_result = mysqli_query($db, $lecturers_query);
$lecturers = mysqli_fetch_all($lecturers_result, MYSQLI_ASSOC);

// Fetch the course ID from the GET method
if(isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Check if the course exists
    $check_course_query = "SELECT * FROM courses WHERE course_id = ?";
    $stmt = mysqli_prepare($db, $check_course_query);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Redirect if the course doesn't exist
    if(mysqli_num_rows($result) == 0) {
        header("Location: all_courses.php");
        exit();
    }
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected lecturer ID from the form
    if(isset($_POST['lecturer_id'])) {
        $lecturer_id = $_POST['lecturer_id'];
        
        // Insert selected lecturer into the course_lecturers table with the course ID
        $insert_query = "INSERT INTO course_lecturers (course_id, lecturer_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($db, $insert_query);
        mysqli_stmt_bind_param($stmt, "ii", $course_id, $lecturer_id);
        if(mysqli_stmt_execute($stmt)) {
            $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Lecturer assigned successfully.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
        } else {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error assigning lecturer. Please try again.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
        }
    } else {
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        No lecturer selected. Please select a lecturer.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
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
    <title>APEX Institute | Assign Lecturers </title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header">
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
         <!-- Page wrapper  -->
         <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Course</h3>
                </div>
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-outline-primary">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Assign Lecturers</h4>
                            </div>
                            <br>
                            <div class="card-body">
                            <?php
                            // Fetch course details based on the course_id
                            $course_query = "SELECT * FROM courses WHERE course_id = ?";
                            $stmt = mysqli_prepare($db, $course_query);
                            mysqli_stmt_bind_param($stmt, "i", $course_id);
                            mysqli_stmt_execute($stmt);
                            $course_result = mysqli_stmt_get_result($stmt);
                            $course = mysqli_fetch_assoc($course_result);

                            // Display course name and ID
                            if ($course) {
                                echo "<h5>Course ID: " . $course['course_id'] . "</h5>";
                                echo "<h5>Course Name: " . $course['course_name'] . "</h5>";
                            }

                            // Fetch currently assigned lecturers for the course
                            $assigned_lecturers_query = "SELECT lecturers.first_name, lecturers.last_name FROM course_lecturers JOIN lecturers ON course_lecturers.lecturer_id = lecturers.lecturer_id WHERE course_lecturers.course_id = ?";
                            $stmt = mysqli_prepare($db, $assigned_lecturers_query);
                            mysqli_stmt_bind_param($stmt, "i", $course_id);
                            mysqli_stmt_execute($stmt);
                            $assigned_lecturers_result = mysqli_stmt_get_result($stmt);
                            $assigned_lecturers = mysqli_fetch_all($assigned_lecturers_result, MYSQLI_ASSOC);

                            // Display currently assigned lecturers or message if none
                            if (!empty($assigned_lecturers)) {
                                echo "<h5>Currently Assigned Lecturers:</h5>";
                                echo "<ul>";
                                foreach ($assigned_lecturers as $lecturer) {
                                    echo "<li>" . $lecturer['first_name'] . " " . $lecturer['last_name'] . "</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>No lecturers assigned to this course.</p>";
                            }
                            ?>

                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?course_id=' . $course_id); ?>" method="post">
                                <div class="form-group">
                                    <label for="lecturer_id">Select Lecturer</label>
                                    <select class="form-control" id="lecturer_id" name="lecturer_id">
                                        <option value="">Select Lecturer</option>
                                        <?php foreach ($lecturers as $lecturer): ?>
                                            <option value="<?php echo $lecturer['lecturer_id']; ?>">
                                                <?php echo $lecturer['first_name'] . ' ' . $lecturer['last_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Assign Lecturer</button>
                                <?php echo $error; ?>
                                <?php echo $success; ?>
                            </form>

                                                    </div>
                        </div>
                    </div>
                </div>
                <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
        </div>
        <!-- End Page wrapper  -->
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

</body>

</html>
