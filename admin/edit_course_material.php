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

// Fetch course material ID from GET parameter (for example, when you pass the course_material_id via URL)
$course_material_id = isset($_GET['course_material_id']) ? $_GET['course_material_id'] : null;

if ($course_material_id) {
    // Fetch course material details for the given course_material_id
    $sql_course_material = "SELECT * FROM course_materials WHERE course_material_id = '$course_material_id'";
    $query_course_material = mysqli_query($db, $sql_course_material);
    
    if (mysqli_num_rows($query_course_material) > 0) {
        $material_row = mysqli_fetch_assoc($query_course_material);
        $material_name = $material_row['course_material_name'];
        $is_active = $material_row['is_active'];
        $course_id = $material_row['course_id']; // Get the course_id to redirect back to course_materials.php
    } else {
        echo '<script>alert("Course material not found."); window.location.href="course_materials.php";</script>';
        exit();
    }
}

// Handle form submission to update course material
if (isset($_POST['update_material'])) {
    $material_name = mysqli_real_escape_string($db, $_POST['material_name']);
    $is_active = isset($_POST['is_active']) ? 1 : 0; // Check if the material is active

    if (!empty($material_name)) {
        $sql_update_material = "UPDATE course_materials 
                                SET course_material_name = '$material_name', is_active = '$is_active' 
                                WHERE course_material_id = '$course_material_id'";

        if (mysqli_query($db, $sql_update_material)) {
            // Redirect to course_materials.php with course_id
            header("Location: course_materials.php?course_id=$course_id");
            exit();
        } else {
            echo '<script>alert("Error updating course material.");</script>';
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
    <title>APEX Institute | Edit Course Material</title>
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
                                <h4 class="card-title">Edit Course Material for <?php echo $material_name; ?> (Course Material ID: <?php echo $course_material_id; ?>)</h4>
                                <h6 class="card-subtitle">Update the details for the course material</h6>

                                <!-- Edit Course Material Form -->
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="material_name">Course Material Name</label>
                                        <input type="text" class="form-control" id="material_name" name="material_name" value="<?php echo $material_name; ?>" required>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $is_active ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Status</label>
                                    </div>

                                    <button type="submit" name="update_material" class="btn btn-primary">Update Material</button>
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
