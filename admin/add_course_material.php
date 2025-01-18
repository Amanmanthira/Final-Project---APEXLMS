<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if user is logged in
if (!isset($_SESSION['apex_admin_id'])) {
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
        session_destroy();
        header("Location: index.php");
        exit();
    }
}

// Fetch course ID from GET parameter (for example, when you pass the course_id via URL)
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if ($course_id) {
    // Fetch course name for the given course_id
    $sql_course_name = "SELECT course_name FROM courses WHERE course_id = '$course_id'";
    $query_course_name = mysqli_query($db, $sql_course_name);

    // Get the course name
    if (mysqli_num_rows($query_course_name) > 0) {
        $course_row = mysqli_fetch_assoc($query_course_name);
        $course_name = $course_row['course_name'];
    } else {
        $course_name = 'Course not found';
    }
}

// Handle form submission to add new course material
if (isset($_POST['add_material'])) {
    $material_name = mysqli_real_escape_string($db, $_POST['material_name']);
    $is_active = 1;

    if (!empty($material_name)) {
        $sql_add_material = "INSERT INTO course_materials (course_id, course_material_name, is_active) 
                             VALUES ('$course_id', '$material_name', '$is_active')";
        if (mysqli_query($db, $sql_add_material)) {
            // Redirect to course_materials.php with course_id
            header("Location: course_materials.php?course_id=$course_id");
            exit();
        } else {
            echo '<script>alert("Error adding course material.");</script>';
        }
    } else {
        echo '<script>alert("Material name is required.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APEX Institute | Add Course Material</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header fix-sidebar">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
        </svg>
    </div>
    <div id="main-wrapper">
        <?php require('header.php'); ?>
        <?php require('left_sidebar.php'); ?>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Add Course Material for <?php echo $course_name; ?> (Course ID: <?php echo $course_id; ?>)</h4>
                                <h6 class="card-subtitle">Enter the details for the new course material</h6>

                                <!-- Add Course Material Form -->
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="material_name">Course Material Name</label>
                                        <input type="text" class="form-control" id="material_name" name="material_name" required>
                                    </div>
                                    <button type="submit" name="add_material" class="btn btn-primary">Add Material</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/datatables-init.js"></script>
</body>

</html>