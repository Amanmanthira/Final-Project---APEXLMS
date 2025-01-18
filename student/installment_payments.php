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
    <title>APEX Institute  | Payments </title>
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
            <div id="paymentAlert"></div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Installment Payments</h4>
                            <h6 class="card-subtitle">List of all Installment Payments for <?php echo $_SESSION['username']; ?></h6>
                            <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                        <th>Payment ID</th>                                       
                                        <th>Course ID</th>
                                        <th>Course Name</th>
                                        <th>Payment Amount</th>
                                        <th>Installment Number</th>
                                        <th>Payment Date</th>
                                        <th>Payment Status Updated Date</th>
                                        <th>Payment Slip</th>
                                        <th>Payment Status</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                   
                                   $sql = "SELECT cp.payment_id, cp.student_id, cp.course_id, cp.installment_number, cp.installment_amount, cp.installment_payment_date, cp.installment_payment_status, cp.installment_payment_slip_path, cp.payment_status_updated_date, s.full_name AS student_name, c.course_name 
                                   FROM course_installment_payments cp
                                   JOIN students s ON cp.student_id = s.id
                                   JOIN courses c ON cp.course_id = c.course_id
                                   WHERE cp.student_id = $student_id";

                                        $query = mysqli_query($db, $sql);

                                        if (!mysqli_num_rows($query) > 0) {
                                        echo '<td colspan="11"><center>No Payment Data Found </center></td>';
                                        } else {
                                        while ($rows = mysqli_fetch_array($query)) {
                                        echo '<tr>
                                            <td>' . $rows['payment_id'] . '</td>                                          
                                            <td>' . $rows['course_id'] . '</td>
                                            <td>' . $rows['course_name'] . '</td>
                                            <td>' . $rows['installment_amount'] . '</td>
                                            <td>' . $rows['installment_number'] . '</td>
                                            <td>' . $rows['installment_payment_date'] . '</td>
                                            <td>' . $rows['payment_status_updated_date'] . '</td>
                                            <td><a href="#" onclick="showImageModal(\'' . $rows['payment_id'] . '\', \'' . $rows['student_id'] . '\', \'' . $rows['course_id'] . '\', \'' . $rows['installment_payment_date'] . '\', \'' . $rows['course_name'] . '\', \'' . $rows['student_name'] . '\', \'' . $rows['installment_amount'] . '\', \'' . $rows['installment_payment_status'] . '\', \'' . $rows['installment_payment_slip_path'] . '\');"><img src="' . $rows['installment_payment_slip_path'] . '" alt="Payment Slip" style="max-width: 100px; max-height: 100px;"></a></td>                                                    
                                            <td>' . $rows['installment_payment_status'] . '</td>
                                        </tr>';
                                        }
                                        }
                                        ?>
                                </tbody>
                                <tfoot>
                                        <tr>
                                        <th>Payment ID</th>                                      
                                        <th>Course ID</th>
                                        <th>Course Name</th>
                                        <th>Payment Amount</th>
                                        <th>Installment Number</th>
                                        <th>Payment Date</th>
                                        <th>Payment Status Updated Date</th>
                                        <th>Payment Slip</th>
                                        <th>Payment Status</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="paymentAlert"></div>

             <!-- Modal HTML -->
             <div id="paymentSlipModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentSlipModalTitle"></h5>
            </div>
            <div class="modal-body">
                <h6 id="paymentDate"></h6>
                <h6 id="paymentAmount"></h6>
                <h6 id="paymentStatus"></h6>
                <br>
                <img src="" id="paymentSlipModalImage" class="img-fluid">
                <button type="button" class="btn btn-info" onclick="reloadPage()">Close</button>
            </div>
        </div>
    </div>
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
    function reloadPage() {
        window.location.assign(window.location.href);
    window.location.href = 'installment_payments.php';
}

    function showImageModal(paymentID, studentID, courseID, paymentDate, courseName, studentName, paymentAmount, paymentStatus, imageSrc) {
    $('#paymentSlipModal').modal('show');
    $('#paymentSlipModalTitle')
        .text('Payment for ' + courseName + ' by ' + studentName)
        .data('payment-id', paymentID) // Set data attribute for payment ID
        .data('student-id', studentID) // Set data attribute for student ID
        .data('course-id', courseID); // Set data attribute for course ID    
    $('#paymentDate').text('Payment Date: ' + paymentDate);
    $('#paymentAmount').text('Payment Amount: ' + paymentAmount);
    $('#paymentStatus').text('Payment Status: ' + paymentStatus);
    $('#paymentSlipModalImage').attr('src', imageSrc);
}


</script>

</body>

</html>