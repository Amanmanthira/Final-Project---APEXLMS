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
    <title>APEX Institute | Messages</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
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
        // Require the left_sidebar.php file
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
                                <h4 class="card-title">Messages</h4>
                                <h6 class="card-subtitle">List of all messages</h6>
                                <div class="table-responsive m-t-40">
                                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Message ID</th>
                                                <th>Sender Name</th>
                                                <th>Email</th>
                                                <th>Subject</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Response</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $sql = "SELECT * FROM contact";
                                            $query = mysqli_query($db, $sql);

                                            if (!mysqli_num_rows($query) > 0) {
                                                echo '<tr><td colspan="9"><center>No Messages Data Found</center></td></tr>';
                                            } else {
                                                while ($rows = mysqli_fetch_array($query)) {
                                                    $status = ($rows['status'] == 1) ? 'Read' : 'Unread';
                                                    echo '<tr>
                                                        <td>' . $rows['id'] . '</td>
                                                        <td>' . $rows['name'] . '</td>
                                                        <td>' . $rows['email'] . '</td>
                                                        <td>' . $rows['subject'] . '</td>
                                                        <td>' . $rows['message'] . '</td>
                                                        <td>' . $rows['status'] . '</td>
                                                        <td>' . $rows['response'] . '</td>
                                                        <td>' . $rows['created_date'] . '</td>
                                                        <td>' . $rows['updated_date'] . '</td>
                                                        <td>
                                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#respondModal" data-id="' . htmlspecialchars($rows['id'], ENT_QUOTES, 'UTF-8') . '" data-email="' . htmlspecialchars($rows['email'], ENT_QUOTES, 'UTF-8') . '" data-name="' . htmlspecialchars($rows['name'], ENT_QUOTES, 'UTF-8') . '">Respond</button>
                                                        </td>
                                                    </tr>';
                                                }
                                            }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                            <tr>
                                                <th>Message ID</th>
                                                <th>Sender Name</th>
                                                <th>Email</th>
                                                <th>Subject</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Response</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="alert-container"></div>
                    </div>
                    <!-- Respond Modal -->
<div class="modal fade" id="respondModal" tabindex="-1" role="dialog" aria-labelledby="respondModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="respondModalLabel">Respond to Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="respondForm">
                <div class="modal-body">
                    <input type="hidden" name="message_id" id="message_id">
                    <div class="form-group">
                        <label for="response_subject">Subject</label>
                        <input type="text" class="form-control" id="response_subject" name="response_subject" required>
                    </div>
                    <div class="form-group">
                        <label for="response_message">Message</label>
                        <textarea class="form-control" id="response_message" name="response_message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Response</button>
                </div>
            </form>
        </div>
    </div>
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
    <!-- DataTables JavaScript -->
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
$(document).ready(function() {
    // Handle Respond button click
    $('#respondModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var email = button.data('email');
        var name = button.data('name');

        var modal = $(this);
        modal.find('#message_id').val(id);
        modal.find('#response_subject').val('');
        modal.find('#response_message').val('');
    });

    // Handle form submission
    $('#respondForm').on('submit', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'send_response.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    var data = JSON.parse(response);

                    if (data.success) {
                        // Reload the page
                        location.reload();
                        $('#respondModal').modal('hide');
                        showAlert('Response sent successfully', 'alert-success');
                    } else {
                        showAlert(data.message, 'alert-danger');
                    }
                } catch (e) {
                    console.error("Failed to parse JSON: ", e);
                    showAlert('Invalid response format', 'alert-danger');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: ", textStatus, errorThrown);
                showAlert('An error occurred', 'alert-danger');
            }
        });
    });

    function showAlert(message, alertClass) {
        $('.alert-container').html('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
    }
});

    </script>
</body>
</html>
