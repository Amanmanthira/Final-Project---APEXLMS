<?php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header("Location: dashboard.php");
    exit;
}
// Start the session
session_start();

// Check if the session variable is set
if(isset($_SESSION["apex_lecturer_id"])) {
    // Get the lecturer ID from the session
    $lecturer_id = $_SESSION["apex_lecturer_id"];

    $query = "SELECT first_name, last_name FROM lecturers WHERE lecturer_id = $lecturer_id";
    $result = mysqli_query($db, $query);

    // Check if the query was successful
    if($result) {
        // Fetch the row from the result set
        $row = mysqli_fetch_assoc($result);

        // Combine the first name and last name
        $lecturer_name = $row['first_name'] . ' ' . $row['last_name'];
    } else {
        // Handle errors here
        $lecturer_name = "Error fetching lecturer's name";
    }
} else {
    // Handle the case where lecturer_id is not set in the session
    $lecturer_name = "Lecturer not logged in";
}
?>
<!-- header header  -->
<div class="header">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- Logo -->
        <div class="navbar-header">
            <a class="navbar-brand" href="dashboard.php">
                <!-- Logo icon -->
                <b><img src="images/favicon.png" alt="homepage" class="dark-logo" /></b>
                <!--End Logo icon -->
                <!-- Logo text -->
                <!-- <span><img src="images/logo-text.png" alt="homepage" class="dark-logo" /></span> -->
            </a>
        </div>
        <!-- End Logo -->
        <div class="navbar-collapse">
            <!-- toggle and nav items -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted  " href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                <li class="nav-item m-l-10"> <a class="nav-link sidebartoggler hidden-sm-down text-muted  " href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
            </ul>
            <!-- User profile and search -->
            <ul class="navbar-nav my-lg-0">
                <!-- Comment -->
                <!-- End Comment -->
                <!-- Profile -->
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted  " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="images/users/5.jpg" alt="user" class="profile-pic" />
                        <?php echo $lecturer_name; // Display the lecturer's name ?>
                    </a>                    
                    <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                        <ul class="dropdown-user">
                            <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
