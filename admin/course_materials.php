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
    
    // Fetch course materials for the given course_id
    $sql_course_materials = "SELECT * FROM course_materials WHERE course_id = '$course_id'";
    $query_course_materials = mysqli_query($db, $sql_course_materials);
    
    // Get the course name
    if (mysqli_num_rows($query_course_name) > 0) {
        $course_row = mysqli_fetch_assoc($query_course_name);
        $course_name = $course_row['course_name'];
    } else {
        $course_name = 'Course not found';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APEX Institute | Course Materials</title>
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
                                <h4 class="card-title">Course Materials for <?php echo $course_name; ?> (Course ID: <?php echo $course_id; ?>)</h4>
                                <h6 class="card-subtitle">List of all materials for the selected course</h6>

                                <!-- Button to go to Add Course Material page -->
                                <a href="add_course_material.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary mb-3">Add New Course Material</a>

                                <div class="table-responsive m-t-40">
                                    <table id="courseMaterialsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Course Material ID</th>
                                                <th>Course Material Name</th>
                                                <th>Active</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Actions</th> <!-- New column for actions -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($query_course_materials) && mysqli_num_rows($query_course_materials) > 0) {
                                                while ($row = mysqli_fetch_assoc($query_course_materials)) {
                                                    $material_id = $row['course_material_id'];
                                                    $is_active = $row['is_active'];
                                                    
                                                    echo '<tr>';
                                                    echo '<td>' . $row['course_material_id'] . '</td>';
                                                    echo '<td>' . $row['course_material_name'] . '</td>';
                                                    echo '<td>' . ($is_active ? 'Yes' : 'No') . '</td>';
                                                    echo '<td>' . $row['created_at'] . '</td>';
                                                    echo '<td>' . $row['updated_at'] . '</td>';
                                                    
                                                    // Actions column with Edit, Change Status, and View options
                                                    echo '<td>';
                                                    echo '<a href="edit_course_material.php?course_material_id=' . $material_id . '" class="btn btn-info btn-sm">Edit</a> ';
                                                    
                                                   
                                                    // View link
                                                    echo '<a href="view_course_material.php?course_material_id=' . $material_id . '" class="btn btn-primary btn-sm">View</a>';
                                                    echo '</td>';
                                                    
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="6"><center>No course materials found for this course.</center></td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
