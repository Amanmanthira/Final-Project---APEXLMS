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
    <title>APEX Institute  | Students</title>
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
                            <h4 class="card-title">All Students</h4>
                            <h6 class="card-subtitle">List of all Students</h6>
                            <div class="table-responsive m-t-40">
                                <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Full Name</th>
                                            <th>Address</th>
                                            <th>Gender</th>
                                            <th>Mobile Number</th>
                                            <th>Created At</th>
                                            <th>Update Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $sql = "SELECT * FROM students";
                                        $query = mysqli_query($db, $sql);

                                        if (!mysqli_num_rows($query) > 0) {
                                            echo '<td colspan="7"><center>No Students Data Found </center></td>';
                                        } else {
                                            while ($rows = mysqli_fetch_array($query)) {
                                                $status = ($rows['is_active'] == 1) ? 'Active' : 'Disabled';
                                                echo '<tr>
                                                    <td>' . $rows['id'] . '</td>
                                                    <td>' . $rows['username'] . '</td>
                                                    <td>' . $rows['email'] . '</td>
                                                    <td>' . $rows['full_name'] . '</td>
                                                    <td>' . $rows['address'] . '</td>
                                                    <td>' . $rows['gender'] . '</td>
                                                    <td>' . $rows['mobile_number'] . '</td>
                                                    <td>' . $rows['created_at'] . '</td>
                                                    <td>' . $rows['update_date'] . '</td>
                                                    <td>' . $status . '</td>
                                                    <td>
                                                    <button class="btn btn-danger btn-sm change-status" data-student-id="' . $rows['id'] . '" data-status="' . $rows['is_active'] . '">Change Status</button>
                                                    </td>
                                                </tr>';
                                            }
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Full Name</th>
                                            <th>Address</th>
                                            <th>Gender</th>
                                            <th>Mobile Number</th>
                                            <th>Created At</th>
                                            <th>Update Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="alert-container"></div>

                </div>
            </div>
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
    $(document).ready(function(){
        $('.change-status').click(function(){
            var studentId = $(this).data('student-id');
            var currentStatus = $(this).data('status');

            // Reference to the clicked element
            var clickedElement = $(this);

            // Perform AJAX request to change the status
            $.ajax({
                type: 'POST',
                url: 'delete_student.php', 
                data: { student_id: studentId, status: currentStatus },
                success: function(response) {
                    // Handle success response
                    if (response === 'success') {
                        // Toggle the status text in the table
                        var newText = (currentStatus == 1) ? 'Disabled' : 'Active';
                        clickedElement.closest('tr').find('td:eq(9)').text(newText);
                        // Update the data attribute with the new status
                        clickedElement.data('status', (currentStatus == 1) ? 0 : 1);
                        // Show success alert
                        showAlert('Success! Status changed successfully.', 'alert-success');
                        // Reload the page after 2 seconds
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else {
                        // Show error alert
                        showAlert('Status change failed', 'alert-danger');
                    }
                },
                error: function() {
                    // Show error alert
                    showAlert('Error occurred while processing the request', 'alert-danger');
                }
            });
        });

        // Function to show Bootstrap alert
        function showAlert(message, alertType) {
            var alertHtml = '<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' +
                                message +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                    '<span aria-hidden="true">&times;</span>' +
                                '</button>' +
                            '</div>';
            $('.alert-container').html(alertHtml);
        }
    });
</script>

</body>

</html>