<?php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!-- Left Sidebar -->
<div class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-devider"></li>
                <li> 
                    <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-tachometer"></i><span class="hide-menu">Dashboard</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="dashboard.php">Dashboard</a></li>
                    </ul>
                </li>

                <li> 
                    <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-graduation-cap"></i><span class="hide-menu">Courses</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="my_courses.php">My Courses</a></li>
                    </ul>
                </li>
                
                <li> 
                    <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-money"></i><span class="hide-menu">Payments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="full_payments.php">Full Payments</a></li>
                        <li><a href="installment_payments.php">Installment Payments</a></li>
                        <li><a href="make_installment_payment.php">Make Installment Payment</a></li>
                    </ul>
                </li>
                
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-user-plus"></i><span class="hide-menu">Enrollments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="enrollments.php">My Enrollments</a></li>
                    </ul>
                </li>     
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</div>
<!-- End Left Sidebar -->
