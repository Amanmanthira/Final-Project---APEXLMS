<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if student_id is set in session
if(isset($_SESSION["student_id"])) {
    // Retrieve student_id from session
    $student_id = $_SESSION["student_id"];

    // Check if student account is active
    $sql = "SELECT * FROM students WHERE id = '$student_id' AND is_active = 1";
    $result = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($result);

    // Redirect to error.php if student account is not active
    if($row_count != 1) {
        header('location:../frontend/error.php');
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if student_id is not set in session
    header('location:../frontend/login.php');
    exit(); // Ensure script execution stops after redirection
}
// Fetch enrollment data for the current student_id along with course details
$sql_enrollments = "SELECT ce.enrollment_id, ce.course_id, ce.student_id, ce.enrollment_date, ce.is_active, c.course_name 
                    FROM course_enrollments ce
                    JOIN courses c ON ce.course_id = c.course_id
                    WHERE ce.student_id = '$student_id'";
$result_enrollments = mysqli_query($db, $sql_enrollments);
$enrollments = mysqli_fetch_all($result_enrollments, MYSQLI_ASSOC);
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">All Enrollments</h4>
                            <h6 class="card-subtitle">List of all Enrollments for <?php echo $_SESSION['username']; ?></h6>

                            <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Course Name</th>
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            foreach ($enrollments as $enrollmentRow) {
                                                $isActive = ($enrollmentRow['is_active'] == 1) ? 'Active' : 'Inactive';

                                                echo '<tr>
                                                        <td>' . $enrollmentRow['course_name'] . '</td>
                                                        <td>' . $enrollmentRow['enrollment_date'] . '</td>                                           
                                                        <td>' . $isActive . '</td>   
                                                    </tr>';
                                            }
                                            if (empty($enrollments)) {
                                                echo '<tr><td colspan="4"><center>No Enrollment Data Found</center></td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Course Name</th>
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
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