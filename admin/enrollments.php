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
// Initialize an empty array to store enrollment data
$enrollments = [];

// Check if course_id is set and not empty
if(isset($_GET['course_id']) && !empty($_GET['course_id'])) {
    $courseID = $_GET['course_id']; // Assuming you're passing course_id in the URL

    // Fetch relevant enrollments data from the database
    $enrollmentQuery = "SELECT ce.*, s.full_name AS student_name, c.course_name FROM course_enrollments ce
                        JOIN students s ON ce.student_id = s.id
                        JOIN courses c ON ce.course_id = c.course_id
                        WHERE ce.course_id = '$courseID'";
    $enrollmentResult = mysqli_query($db, $enrollmentQuery);

    // Check if the query executed successfully
    if ($enrollmentResult) {
        // Fetch all rows and store them in the $enrollments array
        while ($enrollmentRow = mysqli_fetch_assoc($enrollmentResult)) {
            $enrollments[] = $enrollmentRow;
        }
    } else {
        // Handle database query error
        echo "Error: " . mysqli_error($db);
    }
} else {
    // Handle invalid or empty course_id
    header("Location: all_courses.php");
    exit(); // Exit the script
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
    <title>APEX Institute  | Enrollments </title>
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
                            <h4 class="card-title">All Enrollments</h4>
                            <h6 class="card-subtitle">List of all Enrollments for <?php echo isset($enrollments[0]['course_name']) ? $enrollments[0]['course_name'] : 'Unknown Course'; ?></h6>

                            <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Enrollment ID</th>
                                                <th>Student ID</th>
                                                <th>Student Name</th>                                                
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
                                                <th>Balance (LKR)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($enrollments as $enrollmentRow) {
                                            $isActive = ($enrollmentRow['is_active'] == 1) ? 'Active' : 'Inactive';

                                            echo '<tr>
                                            
                                                    <td>' . $enrollmentRow['enrollment_id'] . '</td>
                                                    <td>' . $enrollmentRow['student_id'] . '</td>
                                                    <td>' . $enrollmentRow['student_name'] . '</td> 
                                                    <td>' . $enrollmentRow['enrollment_date'] . '</td>
                                                    <td>' . $isActive . '</td>
                                                    <td>' . $enrollmentRow['balance'] . '</td>
                                                    <td>
                                                    <button class="btn btn-danger btn-sm delete-btn" onclick="updateStatus('. $enrollmentRow['enrollment_id'] .')">Set Status</button>
                                                    </td>
                                                    </tr>';
                                        }
                                        if (empty($enrollments)) {
                                            echo '<tr><td colspan="6"><center>No Enrollment Data Found</center></td></tr>';
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Enrollment ID</th>
                                                <th>Student ID</th>
                                                <th>Student Name</th>                                                
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
                                                <th>Balance (LKR)</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="alert-container" class="container">
   
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


    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="js/lib/datatables/datatables-init.js"></script>
    <script>
   function updateStatus(enrollmentId) {
    console.log("Enrollment ID: " + enrollmentId); // Log the enrollment ID to the console

    $.ajax({
        type: 'POST',
        url: 'update_enrollment.php',
        data: { enrollment_id: enrollmentId },
        success: function(response) {
            // Display success or error message
            if (response === 'success') {
                showAlert('success', 'Status updated successfully.');
            } else {
                showAlert('danger', 'Error: ' + response);
            }
        },
        error: function(xhr, status, error) {
            showAlert('danger', 'Error: ' + error);
        }
    });

    // Reload the page after 2 seconds
    setTimeout(function() {
        location.reload();
    }, 1000);
}


        // Function to display Bootstrap alert
        function showAlert(type, message) {
            var alertDiv = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                            message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>');
            $('#alert-container').prepend(alertDiv);
        }
    
</script>

 

</script>

</body>

</html>