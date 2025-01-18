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
// Get the department ID from the query string
$department_id = $_GET['department_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = $_POST['department_name'];
    $description = $_POST['description'];

    // Check for duplicates
    $duplicate_query = "SELECT * FROM Departments WHERE (department_name='$department_name' OR description='$description') AND department_id != '$department_id'";
    $duplicate_result = mysqli_query($db, $duplicate_query);

    if (mysqli_num_rows($duplicate_result) > 0) {
        $errorMsg = "Department name or description already exists. Please choose a different name or description.";
    } else {
        // Update the department in the database
        $update_query = "UPDATE Departments SET department_name='$department_name', description='$description' WHERE department_id='$department_id'";
        $update_result = mysqli_query($db, $update_query);

        if ($update_result) {
            $successMsg = "Department updated successfully.";
        } else {
            $errorMsg = "Error updating department. Please try again.";
        }
    }
}


// Fetch the department details from the database
$fetch_query = "SELECT * FROM Departments WHERE department_id='$department_id'";
$fetch_result = mysqli_query($db, $fetch_query);
$department = mysqli_fetch_assoc($fetch_result);
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
    <title>APEX Institute  | Edit Department</title>
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
                    <h4 class="card-title">Edit Department</h4>
                    <h6 class="card-subtitle"><?php echo $department['department_name']; ?></h6>
                      <!-- Bootstrap alert for error message -->
                      <?php if(isset($errorMsg) && !empty($errorMsg)): ?>
                                    <div class="alert alert-danger mt-4" role="alert">
                                        <?php echo $errorMsg; ?>
                                    </div>
                                    <?php endif; ?>
                    
                    <!-- Bootstrap alert for success message -->
                    <?php if(isset($successMsg) && !empty($successMsg)): ?>
                    <div class="alert alert-success mt-4" role="alert">
                        <?php echo $successMsg;
                            echo '<script>
                                // Set a timeout to redirect after 2 seconds (2000 milliseconds)
                                setTimeout(function() {
                                    window.location.href = "all_departments.php";
                                }, 2000);
                            </script>'; 
                        ?>
                    </div>
                    <?php endif; ?>
                    <form method="POST">
                                <div class="form-group">
                                    <label for="department_name">Department Name</label>
                                    <input type="text" class="form-control" id="department_name" name="department_name" value="<?php echo $department['department_name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo $department['description']; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Department</button>
                            </form>
         
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
  
</body>

</html>