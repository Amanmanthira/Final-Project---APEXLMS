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

$departments_query = "SELECT * FROM departments";
$departments_result = mysqli_query($db, $departments_query);
$departments = mysqli_fetch_all($departments_result, MYSQLI_ASSOC);

function validateImage($image) {
    $check = getimagesize($image['tmp_name']);
    if ($check === false) {
        return "The uploaded file is not an image.";
    }
    if ($image['size'] > 5000000) {
        return "The uploaded file is too large.";
    }
    $image_file_type = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
    return "";
}

function uploadImage($image) {
    $target_directory = "../assets/public/course_images/";
    $image_file_type = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    $unique_filename = uniqid('image_', true) . '.' . $image_file_type; // Generate a unique filename
    
    $target_file = $target_directory . $unique_filename;

    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        return $target_file;
    } else {
        return "Error uploading the file.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = mysqli_real_escape_string($db, $_POST['course_name']);
    $course_description = mysqli_real_escape_string($db, $_POST['course_description']);
    $department_id = $_POST['department_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $course_fee = mysqli_real_escape_string($db, $_POST['course_fee']);

    // Check for duplicate course name
    $duplicate_check_query = "SELECT * FROM courses WHERE course_name = ?";
    $stmt = mysqli_prepare($db, $duplicate_check_query);
    mysqli_stmt_bind_param($stmt, "s", $course_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    A course with this name already exists.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    } else {
        if (!empty($_FILES['course_image']['name'])) {
            $image_error = validateImage($_FILES['course_image']);
            if ($image_error === "") {
                $course_image = uploadImage($_FILES['course_image']);
                if (is_string($course_image)) {
                    if (!empty($_FILES['course_image_square']['name'])) {
                        $image_square_error = validateImage($_FILES['course_image_square']);
                        if ($image_square_error === "") {
                            $course_image_square = uploadImage($_FILES['course_image_square']);
                            if (is_string($course_image_square)) {
                                $insert_course_query = "INSERT INTO courses (course_name, course_description, department_id, start_date, end_date, course_image, course_image_square, course_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt = mysqli_prepare($db, $insert_course_query);
                                mysqli_stmt_bind_param($stmt, "ssisssss", $course_name, $course_description, $department_id, $start_date, $end_date, $course_image, $course_image_square, $course_fee);
                                if (mysqli_stmt_execute($stmt)) {
                                    $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Course added successfully. Redirecting to <a href="all_courses.php">All Courses</a> in 2 seconds...
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <script>
                                        setTimeout(function(){
                                            window.location.href = "all_courses.php";
                                        }, 2000);
                                    </script>';
                                } else {
                                    $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                Error adding course: ' . mysqli_error($db) . '
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>';
                                }
                            } else {
                                $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            ' . $course_image_square . '
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>';
                            }
                        } else {
                            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        ' . $image_square_error . '
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>';
                        }
                    } else {
                        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Please select a square image.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
                    }
                } else {
                    $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ' . $course_image . '
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                }
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ' . $image_error . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            }
        } else {
            $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Please select a rectangular image.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    }
    mysqli_stmt_close($stmt);
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>APEX Institute | Add Course </title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header">
    <div id="main-wrapper">
        <?php require('header.php'); ?>
        <?php require('left_sidebar.php'); ?>
        <div class="page-wrapper">
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Course</h3>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-outline-primary">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Add Course</h4>
                            </div>
                            <br>
                            <div class="card-body">
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="course_name">Course Name</label>
                                        <input type="text" class="form-control" id="course_name" name="course_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="course_description">Course Description</label>
                                        <textarea class="form-control" id="course_description" name="course_description" rows="5" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="department_id">Department</label>
                                        <select class="form-control" id="department_id" name="department_id" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $department) { ?>
                                                <option value="<?php echo $department['id']; ?>"><?php echo $department['department_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="course_fee">Course Fee</label>
                                        <input type="number" class="form-control" id="course_fee" name="course_fee" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="course_image">Course Image (Rectangular)</label>
                                        <input type="file" class="form-control-file" id="course_image" name="course_image" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="course_image_square">Course Image (Square)</label>
                                        <input type="file" class="form-control-file" id="course_image_square" name="course_image_square" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Add Course</button>
                                    <?php echo $error; ?>
                                    <?php echo $success; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require('footer.php'); ?>
    </div>
</body>
</html>
