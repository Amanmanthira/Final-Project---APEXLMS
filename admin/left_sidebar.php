<?php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!-- Left Sidebar  -->
<div class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-devider"></li>
                <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-tachometer"></i><span class="hide-menu">Dashboard</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="dashboard.php">Dashboard</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-sitemap"></i><span class="hide-menu">Admins</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_admins.php">All Admins</a></li>
                        <li><a href="add_admins.php">Add Admins</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-user"></i><span class="hide-menu">Students</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_students.php">All Students</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-sitemap"></i><span class="hide-menu">Departments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_departments.php">All Departments</a></li>
                        <li><a href="add_department.php">Add Department</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-user-circle-o"></i><span class="hide-menu">Lecturers</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_lecturers.php">All Lecturers</a></li>
                        <li><a href="add_lecturer.php">Add Lecturer</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-graduation-cap"></i><span class="hide-menu">Courses</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_courses.php">All Courses</a></li>
                        <li><a href="add_course.php">Add Course</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-money"></i><span class="hide-menu">Payments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_payments.php">All Payments</a></li>
                        <li><a href="full_payments.php">Full Payments</a></li>
                        <li><a href="installment_payments.php">Installment Payments</a></li>
                        <li><a href="all_recurring_payments.php">Recurring Payments</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fa fa-envelope"></i>
                        <span class="hide-menu">Messages</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="messages.php">All Messages</a></li>

                    </ul>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</div>
<!-- End Left Sidebar  -->