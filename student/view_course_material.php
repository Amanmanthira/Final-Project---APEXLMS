<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if student is logged in and account is active
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
$result = mysqli_query($db, $sql);
$row_count = mysqli_num_rows($result);

// Redirect to error.php if student account is not active
if($row_count != 1) {
    header('location:../frontend/error.php');
    exit(); // Ensure script execution stops after redirection
}

// Fetch course material ID from GET parameter
$course_material_id = isset($_GET['course_material_id']) ? $_GET['course_material_id'] : null;
$course_name = "";
$course_id = "";

// Fetch the course details for the given course_material_id
if ($course_material_id) {
    $sql_course_details = "SELECT c.course_id, c.course_name
                           FROM courses c
                           JOIN course_materials cm ON c.course_id = cm.course_id
                           WHERE cm.course_material_id = '$course_material_id'";
    $query_course_details = mysqli_query($db, $sql_course_details);

    if (mysqli_num_rows($query_course_details) > 0) {
        $course_row = mysqli_fetch_assoc($query_course_details);
        $course_id = $course_row['course_id'];
        $course_name = $course_row['course_name'];
    } else {
        echo '<script>alert("Course material not found."); window.location.href="course_materials.php";</script>';
        exit();
    }
}

// Handle material received status update
if (isset($_POST['update_received'])) {
    $status = "Received"; // Mark status as "Received"
    $sql_update_status = "UPDATE course_material_distribution 
                          SET received_date = NOW(), status = '$status' 
                          WHERE course_material_id = '$course_material_id' AND student_id = '$student_id'";

    if (mysqli_query($db, $sql_update_status)) {
        echo '<script>alert("Material marked as received successfully.");</script>';
    } else {
        echo '<script>alert("Error updating material status.");</script>';
    }
}

// Fetch the enrolled material for the logged-in student
$sql_enrollments = "
    SELECT ce.student_id, ce.enrollment_date, ce.is_active AS student_status, cm.course_material_name, 
           cmd.sent_date, cmd.received_date, cmd.status
    FROM course_enrollments ce
    LEFT JOIN course_materials cm ON cm.course_id = ce.course_id
    LEFT JOIN course_material_distribution cmd ON cmd.student_id = ce.student_id AND cmd.course_material_id = cm.course_material_id
    WHERE ce.student_id = '$student_id' AND cm.course_material_id = '$course_material_id'";

$query_enrollments = mysqli_query($db, $sql_enrollments);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APEX Institute | Course Material Distribution</title>
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
                                <h4 class="card-title">Course Material for <?php echo $course_name; ?> (Course Material ID: <?php echo $course_material_id; ?>)</h4>
                                <h6 class="card-subtitle">Your material distribution status</h6>

                                <div class="table-responsive m-t-40">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Sent Date</th>
                                                <th>Received Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($query_enrollments) > 0) {
                                                while ($row = mysqli_fetch_assoc($query_enrollments)) {
                                                    echo '<tr>';
                                                    echo '<td>' . $row['course_material_name'] . '</td>';
                                                    echo '<td>' . $row['sent_date'] . '</td>';
                                                    echo '<td>' . ($row['received_date'] ? $row['received_date'] : 'Not Received') . '</td>';
                                                    echo '<td>' . ($row['status'] ? $row['status'] : 'Not Sent') . '</td>';
                                                    
                                                    // Show 'Mark as Received' button if the status is not already 'Received'
                                                    if ($row['status'] != 'Received') {
                                                        echo '<td>
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="student_id" value="' . $student_id . '">
                                                                    <button type="submit" name="update_received" class="btn btn-success">Mark as Received</button>
                                                                </form>
                                                              </td>';
                                                    } else {
                                                        echo '<td><span class="badge badge-success">Received</span></td>';
                                                    }

                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="5" class="text-center">No materials assigned to you.</td></tr>';
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
</body>

</html>
