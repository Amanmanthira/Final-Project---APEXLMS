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
$error = '';
$success = '';

// Fetch departments list from the database
$departments_query = "SELECT * FROM departments";
$departments_result = mysqli_query($db, $departments_query);
$departments = mysqli_fetch_all($departments_result, MYSQLI_ASSOC);

// Fetch the course ID from the URL parameters
if(isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Fetch the current data for the course using prepared statement
    $course_query = "SELECT * FROM courses WHERE course_id = ?";
    $stmt = mysqli_prepare($db, $course_query);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $course_result = mysqli_stmt_get_result($stmt);
    $course_data = mysqli_fetch_assoc($course_result);

    // Check if the course data exists
    if(!$course_data) {
        header("Location: all_courses.php");
        exit();
    }
} 

function validateImage($image) {
    // Check if the file is an actual image or a fake image
    $check = getimagesize($image['tmp_name']);
    if ($check === false) {
        return "The uploaded file is not an image.";
    }

    // Check file size
    if ($image['size'] > 5000000) { // 5MB
        return "The uploaded file is too large.";
    }

    // Allow certain file formats
    $image_file_type = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Only JPG, JPEG, PNG & GIF files are allowed.";
    }

    return "";
}

function uploadImage($image, $target_directory) {
    // Generate a unique filename
    $image_name = uniqid() . '_' . basename($image['name']);
    $target_file = $target_directory . $image_name;

    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        return $target_file;
    } else {
        return "Error uploading the file.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $course_name = mysqli_real_escape_string($db, $_POST['course_name']);
    $course_description = mysqli_real_escape_string($db, $_POST['course_description']);
    $department_id = $_POST['department_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $course_fee = mysqli_real_escape_string($db, $_POST['course_fee']);

     // Check for duplicate course name
     $duplicate_course_query = "SELECT COUNT(*) as count FROM courses WHERE course_name = ? AND course_id != ?";
     $stmt_duplicate_course = mysqli_prepare($db, $duplicate_course_query);
     mysqli_stmt_bind_param($stmt_duplicate_course, "si", $course_name, $course_id);
     mysqli_stmt_execute($stmt_duplicate_course);
     $duplicate_course_result = mysqli_stmt_get_result($stmt_duplicate_course);
     $duplicate_course_row = mysqli_fetch_assoc($duplicate_course_result);
     $duplicate_count = $duplicate_course_row['count'];

     if ($duplicate_count > 0) {
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Course name already exists. Please choose a different name.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>'; 
            } else {            

    if (!empty($_FILES['course_image']['name'])) {
        $image_error = validateImage($_FILES['course_image']);
        if ($image_error === "") {
            $course_image = uploadImage($_FILES['course_image'], "../assets/public/course_images/");
            if (is_string($course_image)) {
                // Upload successful, continue with other operations
                // Update data in the courses table with the new image and course fee
                $update_course_query = "UPDATE courses SET course_name=?, course_description=?, department_id=?, start_date=?, end_date=?, course_image=?, course_fee=? WHERE course_id=?";
                $stmt = mysqli_prepare($db, $update_course_query);
                mysqli_stmt_bind_param($stmt, "ssissssi", $course_name, $course_description, $department_id, $start_date, $end_date, $course_image, $course_fee, $course_id);

                if (mysqli_stmt_execute($stmt)) {
                    $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Course updated successfully.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
                    echo '<script>
                            setTimeout(function() {
                                window.location.href = "all_courses.php";
                            }, 2000);
                          </script>';
                } else {
                    $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error updating course: ' . mysqli_error($db) . '
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                }
            } else {
                // Error uploading the image
                $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ' . $course_image . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            }
        } else {
            // Image validation error
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . $image_error . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    } else {
        // No image uploaded
        // Update course data without updating the course image
        $update_course_query = "UPDATE courses SET course_name=?, course_description=?, department_id=?, start_date=?, end_date=?, course_fee=? WHERE course_id=?";
        $stmt = mysqli_prepare($db, $update_course_query);
        mysqli_stmt_bind_param($stmt, "ssisssi", $course_name, $course_description, $department_id, $start_date, $end_date, $course_fee, $course_id);

        if (mysqli_stmt_execute($stmt)) {
            $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Course updated successfully.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "all_courses.php";
                    }, 2000);
                  </script>';
        } else {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error updating course: ' . mysqli_error($db) . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    }
  
    // Handling course_image_square
    if (!empty($_FILES['course_image_square']['name'])) {
        $image_square_error = validateImage($_FILES['course_image_square']);
        if ($image_square_error === "") {
            $course_image_square = uploadImage($_FILES['course_image_square'], "../assets/public/course_images/");
            if (is_string($course_image_square)) {
                // Update course_image_square in the courses table
                $update_image_square_query = "UPDATE courses SET course_image_square=? WHERE course_id=?";
                $stmt_square = mysqli_prepare($db, $update_image_square_query);
                mysqli_stmt_bind_param($stmt_square, "si", $course_image_square, $course_id);
                mysqli_stmt_execute($stmt_square);
            } else {
                // Error uploading the image square
                $error .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ' . $course_image_square . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            }
        } else {
            // Image square validation error
            $error .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . $image_square_error . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
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
    <title>APEX Institute | Update Course </title>
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
                                <h4 class="m-b-0 text-white">Update Course</h4>
                            </div>
                            <br>
                            <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="course_id" value="<?php echo $course_data['course_id']; ?>">
                                    <div class="form-group">
                                        <label for="course_name">Course Name</label>
                                        <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo $course_data['course_name']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="course_description">Course Description</label>
                                        <textarea class="form-control" id="course_description" name="course_description" rows="4" required><?php echo $course_data['course_description']; ?></textarea>
                                    </div>
                                 <div class="form-group">
                                    <label for="course_image">Course Image</label>
                                    <?php if (!empty($course_data['course_image'])): ?>
                                        <input type="file" class="form-control-file" id="course_image" name="course_image" placeholder="<?php echo $course_data['course_image']; ?>">
                                    <?php else: ?>
                                        <input type="file" class="form-control-file" id="course_image" name="course_image">
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Upload an image for the course.</small>
                                </div>
                                <!-- Display the current course image if it exists -->
                                <?php if (!empty($course_data['course_image'])): ?>
                                    <div class="form-group">
                                        <label>Current Image:</label><br>
                                        <img src="<?php echo $course_data['course_image']; ?>" alt="Current Image" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="course_image_square">Course Image Square</label>
                                    <?php if (!empty($course_data['course_image_square'])): ?>
                                        <input type="file" class="form-control-file" id="course_image_square" name="course_image_square" placeholder="<?php echo $course_data['course_image_square']; ?>">
                                    <?php else: ?>
                                        <input type="file" class="form-control-file" id="course_image_square" name="course_image_square">
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Upload an image for the course.</small>
                                </div>
                                  <!-- Display the current course square image if it exists -->
                                  <?php if (!empty($course_data['course_image_square'])): ?>
                                    <div class="form-group">
                                        <label>Current Square Image:</label><br>
                                        <img src="<?php echo $course_data['course_image_square']; ?>" alt="Current Square Image" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                    <div class="form-group">
                                        <label for="department_id">Department</label>
                                        <select class="form-control" id="department_id" name="department_id" required>
                                            <option value="" disabled selected>Select Department</option>
                                            <?php foreach ($departments as $department): ?>
                                                <option value="<?php echo $department['department_id']; ?>" <?php if($department['department_id'] == $course_data['department_id']) echo 'selected'; ?>><?php echo $department['department_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $course_data['start_date']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $course_data['end_date']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="course_fee">Course Fee (LKR)</label>
                                        <input type="number" class="form-control" id="course_fee" name="course_fee" value="<?php echo $course_data['course_fee']; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Course</button>
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
