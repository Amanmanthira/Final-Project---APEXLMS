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
    <title>APEX Institute  | Departments</title>
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
                    <h4 class="card-title">All Departments</h4>
                    <h6 class="card-subtitle">List of all Departments</h6>
                    <div class="table-responsive m-t-40">
                        <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Department ID</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch active departments
                                $sql_active = "SELECT * FROM Departments WHERE is_active = 1";
                                $query_active = mysqli_query($db, $sql_active);

                                if (!mysqli_num_rows($query_active) > 0) {
                                    echo '<tr><td colspan="5"><center>No Departments Data Found </center></td></tr>';
                                } else {
                                    while ($rows = mysqli_fetch_array($query_active)) {
                                        echo '<tr>
                                            <td>' . $rows['department_id'] . '</td>
                                            <td>' . $rows['department_name'] . '</td>
                                            <td>' . $rows['description'] . '</td>
                                            <td>Yes</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm edit-btn" data-department-id="' . $rows['department_id'] . '">Edit</button>
                                                <button class="btn btn-danger btn-sm delete-btn" data-department-id="' . $rows['department_id'] . '">Delete</button>
                                            </td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Department ID</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Add a button to show inactive departments and handle the click event -->
<button class="btn btn-primary m-t-10" id="showInactiveBtn">Show Inactive Departments</button>
<!-- Add a secondary table to show inactive departments with a different class -->
<br>
<br>
<table id="example23" class="display nowrap table table-hover table-striped table-bordered inactive-departments-table" cellspacing="0" width="100%" style="display:none;">
    <thead>
        <tr>
            <th>Department ID</th>
            <th>Department Name</th>
            <th>Description</th>
            <th>Active</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
    // Fetch inactive departments
    $sql_inactive = "SELECT * FROM Departments WHERE is_active = 0";
    $query_inactive = mysqli_query($db, $sql_inactive);

    if (mysqli_num_rows($query_inactive) > 0) {
        // If there are inactive departments
        while ($rows = mysqli_fetch_array($query_inactive)) {
            echo '<tr>
                <td>' . $rows['department_id'] . '</td>
                <td>' . $rows['department_name'] . '</td>
                <td>' . $rows['description'] . '</td>
                <td>No</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-department-id="' . $rows['department_id'] . '">Edit</button>
                    <button class="btn btn-success btn-sm activate-btn" data-department-id="' . $rows['department_id'] . '">Activate</button>
                </td>
            </tr>';
        }
    } else {
        // If there are no inactive departments
        echo '<tr><td colspan="5"><center>There are no inactive departments.</center></td></tr>';
    }
    ?>

    </tbody>
</table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Content -->    </div>
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
    <!-- Add this script section at the end of your HTML body -->
<script>
$(document).ready(function(){
    // Attach click event to the delete button
    $(".delete-btn").click(function(){
        // Get the department ID from the data attribute
        var departmentId = $(this).data("department-id");
        
        // Send an Ajax request to delete the department
        $.ajax({
            url: "delete_department.php", // URL of the PHP script to handle deletion
            method: "POST",
            data: { department_id: departmentId }, // Data to be sent to the server
            dataType: "json", // Data type expected from the server
            success: function(response){
                // Check if the deletion was successful
                if(response.status == "success"){
                    // Show success message using Bootstrap alert
                    $("#alert-container").html('<div class="alert alert-success alert-dismissible fade show" role="alert">Department deleted successfully.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    // Reload the page to update the department list
                    location.reload();
                } else {
                    // Show error message using Bootstrap alert
                    $("#alert-container").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to delete department. Please try again later.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            },
            error: function(){
                // Show error message using Bootstrap alert
                $("#alert-container").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error occurred while processing your request. Please try again later.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
        });
    });
    $(".edit-btn").click(function(){
        var departmentId = $(this).data("department-id");

        // Redirect to the edit department page with the department ID
        window.location.href = "edit_department.php?department_id=" + departmentId;
    });
});
$(document).ready(function(){
    // Show inactive departments on button click
    $("#showInactiveBtn").click(function(){
        $(".inactive-departments-table").show(); // Show tables with the inactive-departments-table class
    });

    // Attach click event to the activate button for inactive departments
    $(document).on("click", ".activate-btn", function(){
        var departmentId = $(this).data("department-id");

        // Send an Ajax request to activate the department
        $.ajax({
            url: "activate_department.php", // URL of the PHP script to handle activation
            method: "POST",
            data: { department_id: departmentId },
            dataType: "json",
            success: function(response){
                if(response.status == "success"){
                    // Show success message using Bootstrap alert
                    $("#alert-container").html('<div class="alert alert-success alert-dismissible fade show" role="alert">Department activated successfully.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    // Reload the page to update the department list
                    location.reload();
                } else {
                    // Show error message using Bootstrap alert
                    $("#alert-container").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to activate department. Please try again later.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            },
            error: function(){
                // Show error message using Bootstrap alert
                $("#alert-container").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error occurred while processing your request. Please try again later.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
        });
    });
});
</script>

</body>

</html>