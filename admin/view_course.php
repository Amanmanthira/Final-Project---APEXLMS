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
// Retrieve the course_id from the GET parameters
$course_id = $_GET['course_id'];

// Prepare and execute a query to fetch the course_name based on the course_id
$query = "SELECT course_name FROM courses WHERE course_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->bind_result($course_name);
$stmt->fetch();
$stmt->close();
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
    <title>APEX Institute | <?php echo $course_name; ?></title>
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
               <?php
// Fetch and display the details of the selected course
if(isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Check if the course exists
    $check_sql = "SELECT COUNT(*) as count FROM courses WHERE course_id = $course_id";
    $check_query = mysqli_query($db, $check_sql);
    $check_result = mysqli_fetch_assoc($check_query);
    $course_exists = $check_result['count'] > 0;

    if ($course_exists) { 
    
    $sql = "SELECT c.*, d.department_name, GROUP_CONCAT(CONCAT(l.first_name, ' ', l.last_name) SEPARATOR ', ') AS lecturers 
            FROM courses c 
            LEFT JOIN course_lecturers cl ON c.course_id = cl.course_id 
            LEFT JOIN lecturers l ON cl.lecturer_id = l.lecturer_id 
            LEFT JOIN departments d ON c.department_id = d.department_id 
            WHERE c.course_id = $course_id"; 
    
    $query = mysqli_query($db, $sql);

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        echo '<div class="col-md-12">';
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h6 class="card-subtitle">Course ID: ' . $row['course_id'] . '</h6>';
        echo '<img src="' . $row['course_image'] . '" class="card-img-top" alt="Course Image"><br><br>';
        echo '<div class="text-center"><h4 class="card-title"><strong style="font-size: 24px;">' . $row['course_name'] . '</strong></h4></div>';
        echo '<div class="text-center"><p class="card-text">' . $row['course_description'] . '</p></div>';
        echo '<p class="card-text">Department: ' . $row['department_name'] . '</p>';
        echo '<p class="card-text">Start Date: ' . $row['start_date'] . '</p>';
        echo '<p class="card-text">End Date: ' . $row['end_date'] . '</p>';
        echo '<p class="card-text">Lecturers: ' . $row['lecturers'] . '</p>';
        echo '<p class="card-text">Course Fee (LKR): ' . $row['course_fee'] . '</p>';
        echo '<p class="card-text">Created At: ' . $row['created_at'] . '</p>';
        echo '<p class="card-text">Updated At: ' . $row['updated_at'] . '</p>';
        echo '
        <!-- Button to trigger modal -->
        <div class="col-md-12">
        <a href="add_topic.php?course_id=' . $row['course_id'] . '" class="btn btn-primary">Add Topic</a>
        <a href="add_announcement.php?course_id=' . $row['course_id'] . '" class="btn btn-success">Add Announcement</a>
        <a href="course_materials.php?course_id=' . $row['course_id'] . '" class="btn btn-info">Manage Couse Materials</a>
        </div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
// Fetch and display announcements
$announcement_sql = "SELECT * FROM course_announcements WHERE course_id = $course_id ORDER BY created_date DESC";
$announcement_query = mysqli_query($db, $announcement_sql);

if (mysqli_num_rows($announcement_query) > 0) {
    echo '<div class="col-md-12">';
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<h5 class="card-title">Announcements</h5>';
    
    while ($announcement_row = mysqli_fetch_assoc($announcement_query)) {
        echo '<div class="alert alert-info" role="alert">';
        
        // Display the "Inactive" badge next to the date
        echo '<h6><i class="fa fa-calendar"></i> ' . $announcement_row['created_date'];
        if ($announcement_row['is_active'] == 0) {
            echo '<span class="badge badge-danger ml-2">Inactive</span>';
        }
        echo '</h6>';
        
        echo '<h5><i class="fa fa-bullhorn"></i> ' . $announcement_row['announcement'] . '</h5>';
        echo '<div class="btn-group" role="group" aria-label="Announcement Actions">';
        echo '<a href="edit_announcement.php?course_id=' . $row['course_id'] . '&announcement_id=' . $announcement_row['announcement_id'] . '" class="btn btn-primary btn-sm">Edit</a>';
        echo '<button type="button" class="btn btn-danger btn-sm ml-2" onclick="setStatusAnnouncement(' . $announcement_row['announcement_id'] . ')">Set Status</button>';
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="col-md-12">';
    echo '<div class="alert alert-danger" role="alert">No announcements available.</div>';
    echo '</div>';
}
       // Fetch topics for the given course ID
       $sql_topics = "SELECT * FROM topics WHERE course_id = $course_id ORDER BY creation_date ASC";
       $result_topics = mysqli_query($db, $sql_topics);

// Check if there are topics available
if (mysqli_num_rows($result_topics) > 0) {
    echo '<div class="col-md-12">';
    echo '<div class="card mt-3">';
    echo '<div class="card-body">';
    echo '<h2 class="card-title">Topics</h2>';
    echo '<ul class="list-group list-group-flush">';
    // Initialize count variable
    $count = 1;
// Inside the while loop where you display topics
while ($row_topic = mysqli_fetch_assoc($result_topics)) {
    echo '<li class="list-group-item pl-4"><h4 class="list-group-item-heading">' . $count . '. ' . $row_topic['topic_name'] . '</h4>
    <button type="button" class="btn btn-sm btn-info float-right ml-2" onclick="openAssignmentModal(\'' . $row_topic['topic_id'] . '\', \'' . $row_topic['topic_name'] . '\')">Add Assignment</button>
    <a href="edit_topic.php?course_id=' . $row['course_id'] . '&topic_id=' . $row_topic['topic_id'] . '" class="btn btn-sm btn-primary float-right ml-2">Edit</a>';

if ($row_topic['is_active'] == 1) {
    echo '<button type="button" class="btn btn-sm btn-danger float-right ml-2" onclick="deleteTopic(\'' . $row_topic['topic_id'] . '\')">Delete</button>';
}

echo '<button type="button" class="btn btn-sm btn-dark float-right ml-2" onclick="openAddContentModal(\'' . $row_topic['topic_id'] . '\')">Add Content</button>';

if ($row_topic['is_active'] == 0) {
    echo '<span class="badge badge-danger ml-2">Inactive</span>';
    echo '<button type="button" class="btn btn-sm btn-success float-right ml-2" onclick="activateTopic(\'' . $row_topic['topic_id'] . '\')">Activate</button>';
}

echo '<ul class="pl-3">'; // Start nested list for content
// Fetch content for the current topic
$topic_id = $row_topic['topic_id'];
$sql_content = "SELECT * FROM content WHERE topic_id = $topic_id ORDER BY is_active DESC"; // Order by is_active to display active first
$result_content = mysqli_query($db, $sql_content);

// Check if there is content available for the topic
if (mysqli_num_rows($result_content) > 0) {
    // Iterate over each content item
    while ($row_content = mysqli_fetch_assoc($result_content)) {
        echo '<li>';
        // Determine file type and display relevant icon
        $icon = '';
        switch ($row_content['content_type']) {
            case 'PDF':
                $icon = '<i class="fa fa-file-pdf-o"></i>';
                break;
            case 'Audio':
                $icon = '<i class="fa fa-volume-up"></i>';
                break;
            case 'Video':
                $icon = '<i class="fa fa-file-video-o"></i>';
                break;
            case 'Image':
                $icon = '<i class="fa fa-file-image-o"></i>';
                break;
            case 'Zoom Link': // Check for Zoom Link content type
                $icon = '<i class="fa fa-video-camera"></i>';
                break;
            default:
                $icon = '<i class="fa fa-file"></i>';
                break;
        }

        // Display "Inactive" text and "Activate" button if content is not active
        $inactive_text = $row_content['is_active'] ? '' : '<span class="badge badge-danger">Inactive</span>';
        $activate_button = $row_content['is_active'] ? '' : '<button type="button" class="btn btn-sm btn-success ml-2 btn-activate" data-content-id="' . $row_content['content_id'] . '">Activate</button>';

        // Generate a unique identifier for each content item (you can use the content ID or any other unique identifier)
        $content_identifier = $row_content['content_id']; 
        $content_url = 'show_content.php?content_id=' . $content_identifier;

        echo $icon . ' <a href="' . $content_url . '">' . $row_content['content_name'] . '</a>' . $inactive_text . $activate_button;

        // Add the Bootstrap margin class to create bottom margin
        echo '<div class="mb-2">';
        echo '<button type="button" class="btn btn-sm btn-info ml-2 edit-content-btn" data-content-id="' . $row_content['content_id'] . '" data-content-name="' . $row_content['content_name'] . '" data-content-type="' . $row_content['content_type'] . '" data-content-text="' . $row_content['content_text'] . '" data-current-file="' . $row_content['content_path'] . '">Edit</button>';

        // Only render the delete button if the content is active
        if ($row_content['is_active'] == 1) {
            echo '<button type="button" class="btn btn-sm btn-danger  ml-2" onclick="deleteContent(\'' . $row_content['content_id'] . '\')">Delete</button>';
        }

        // Check if the content type is "Zoom Link" and show the button to view attendance
        if ($row_content['content_type'] == 'Zoom Link') {
            echo '<a href="view_attendance.php?topic_id=' . $row_content['topic_id'] . '&content_id=' . $row_content['content_id'] . '" class="btn btn-sm btn-primary ml-2">View Attendance</a>';
        }

        echo '</div>';
        echo '</li>';
    }
} else {
    echo '<li>No content found for this topic.</li>';
}

    echo '<br>';
    // Fetch both active and inactive assignments for the current topic
    $sql_assignments = "SELECT a.*, ar.resource_type, ar.resource_path 
    FROM assignments AS a 
    LEFT JOIN assignment_resources AS ar 
    ON a.assignment_id = ar.assignment_id 
    WHERE a.topic_id = $topic_id";

        $result_assignments = mysqli_query($db, $sql_assignments);

        // Check if there are assignments available for the topic
        if (mysqli_num_rows($result_assignments) > 0) {
            // Display assignments
            echo '<h4>Assignments</h4>';
            echo '<ul class="fa-ul">';
            while ($row_assignment = mysqli_fetch_assoc($result_assignments)) {
                echo '<li><span class="fa-li"><i class="fa fa-clipboard"></i></span>';
                // Check if the assignment is active or inactive
                $assignment_status = $row_assignment['is_active'] ? '' : '<span class="badge badge-danger">Inactive</span>';
                echo '<a href="show_assignment.php?id=' . $row_assignment['assignment_id'] . '">' . $row_assignment['assignment_name'] . ' ' . $assignment_status . '</a>';
                echo '<div class="mb-2">';
                echo '<button type="button" class="btn btn-sm btn-info ml-2 edit-assignment-btn" onclick="showEditAssignmentModal(\'' . $row_assignment['assignment_id'] . '\', \'' . $row_assignment['assignment_name'] . '\', \'' . $row_assignment['assignment_description'] . '\', \'' . $row_assignment['due_date'] . '\', \'' . $row_assignment['resource_type'] . '\', \'' . $row_assignment['resource_path'] . '\')">Edit</button>';
                // Only render the delete button if the assignment is active
                if ($row_assignment['is_active'] == 1) {
                    echo '<button type="button" class="btn btn-sm btn-danger ml-2" onclick="deleteAssignment(\'' . $row_assignment['assignment_id'] . '\')">Delete</button>';
                } else {
                    echo '<button type="button" class="btn btn-sm btn-success ml-2" onclick="activateAssignment(\'' . $row_assignment['assignment_id'] . '\')">Activate</button>';
                }
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No assignments found for this topic.</p>';
        }
        echo '</ul>'; // End nested list for content
        echo '</li>'; // End list item for topic
        $count++; // Increment count
    }


    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    // If no topics found
    echo '<div class="col-md-12">';
    echo '<div class="card mt-3">';
    echo '<div class="card-body">';
    echo '<p>No topics found for this course.</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

} else {
    echo '<div class="card">
    <div class="card-body">
        <div class="col-12">
            <p>No Course Found</p>
        </div>
    </div>
</div>';           
}
} else {
    echo '<div class="card">
    <div class="card-body">
        <div class="col-12">
            <p>Course ID does not exist</p>
        </div>
    </div>
</div>';
}
} else {
    echo '<div class="card">
    <div class="card-body">
        <div class="col-12">
            <p>Course ID not provided</p>
        </div>
    </div>
</div>';}
?>
</div>

            </div>
            <!-- End Page Content -->
<!-- Add Content Modal -->
<div class="modal fade" id="addContentModal" tabindex="-1" role="dialog" aria-labelledby="addContentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContentModalLabel">Add Content</h5>
            </div>
            <div class="modal-body">
                <form id="addContentForm" enctype="multipart/form-data">
                    <input type="hidden" id="topicId" name="topicId" value="<?php echo $topic_id; ?>">
                    <div class="form-group">
                        <label for="contentName">Content Name</label>
                        <input type="text" class="form-control" id="contentName" name="contentName" required>
                    </div>
                    <div class="form-group">
                        <label for="contentType">Content Type</label>
                        <select class="form-control" id="contentType" name="contentType" required>
                            <option value="Zoom Link">Zoom Link</option>
                            <option value="PDF">PDF</option>
                            <option value="Audio">Audio</option>
                            <option value="Video">Video</option>
                            <option value="Image">Image</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contentText">Content Text / Description</label>
                        <textarea class="form-control" id="contentText" name="contentText" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="class_time">Class Time</label>
                        <input type="datetime-local" class="form-control" id="class_time" name="class_time">
                    </div>
                    <div id="fileInputContainer" style="display: none;">
                        <div class="form-group">
                            <label for="contentFile">Upload File</label>
                            <input type="file" class="form-control-file" id="contentFile" name="contentFile">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Content</button>
                    <button type="button" class="btn btn-secondary" onclick="reloadPage()">Close</button>
                    <!-- Hidden containers for success and error messages -->
                    <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="successMessageText"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="errorMessageText"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Content Modal -->
<div class="modal fade" id="editContentModal" tabindex="-1" role="dialog" aria-labelledby="editContentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContentModalLabel">Edit Content</h5>
            </div>
            <div class="modal-body">
                <form id="editContentForm" enctype="multipart/form-data">
                    <input type="hidden" id="editContentId" name="contentId">
                    <div class="form-group">
                        <label for="editContentName">Content Name</label>
                        <input type="text" class="form-control" id="editContentName" name="contentName" required>
                    </div>
                    <div class="form-group">
                        <label for="editContentType">Content Type</label>
                        <select class="form-control" id="editContentType" name="contentType" required>
                            <?php
                            $content_types = array("Zoom Link", "PDF", "Audio", "Video" ,"Image"); 
                            foreach ($content_types as $type) {
                                echo '<option value="' . $type . '">' . $type . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editContentText">Content Text / Description</label>
                        <textarea class="form-control" id="editContentText" name="contentText" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_class_time">Class Time</label>
                        <input type="datetime-local" class="form-control" id="edit_class_time" name="class_time">
                    </div>
                    <div class="form-group" id="editFileInputContainer" style="display: none;">
                        <label for="editContentFile">Upload File</label>
                        <input type="file" class="form-control-file" id="editContentFile" name="contentFile">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Content</button>
                    <button type="button" class="btn btn-secondary" onclick="reloadPage()">Close</button>
                    <!-- Hidden containers for success and error messages -->
                    <div id="successMessage2" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="successMessage2Text"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="errorMessage2" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="errorMessage2Text"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal for adding assignment -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1" role="dialog" aria-labelledby="addAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAssignmentModalLabel">Add Assignment</h5>
            </div>
            <div class="modal-body">
                <!-- Assignment form -->
                <form id="addAssignmentForm" enctype="multipart/form-data">
                <input type="hidden" id="assignmentTopicId" name="assignmentTopicId">

                    <div class="form-group">
                        <label for="assignmentName">Assignment Name</label>
                        <input type="text" class="form-control" id="assignmentName" name="assignmentName" required>
                    </div>
                    <div class="form-group">
                        <label for="assignmentDescription">Assignment Description</label>
                        <textarea class="form-control" id="assignmentDescription" name="assignmentDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="dueDate">Due Date</label>
                        <input type="date" class="form-control" id="dueDate" name="dueDate" required>
                    </div>
                    <!-- Optional resource section -->
                    <div class="form-group">
                        <label for="resourceType">Resource Type</label>
                        <select class="form-control" id="resourceType" name="resourceType">
                            <option value="">Select Resource Type</option>
                            <option value="PDF">PDF</option>
                            <option value="Video">Video</option>
                            <option value="Image">Image</option>
                            <option value="Audio">Audio</option>
                            <option value="Zip">Zip</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="resourceFile">Resource File</label>
                        <input type="file" class="form-control-file" id="resourceFile" name="resourceFile" accept="" required>
                    </div>
                    <!-- Hidden containers for success and error messages -->
                    <div id="successMessage3" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="successMessage3Text"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="errorMessage3" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="errorMessage3Text"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="reloadPage()">Close</button>
                <button type="button" class="btn btn-primary" onclick="addAssignment()">Add Assignment</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for editing assignment -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1" role="dialog" aria-labelledby="editAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssignmentModalLabel">Edit Assignment</h5>
            </div>
            <div class="modal-body">
                <!-- Assignment form -->
                <form id="editAssignmentForm" enctype="multipart/form-data">
                    <input type="hidden" id="editAssignmentId" name="editAssignmentId">

                    <div class="form-group">
                        <label for="editAssignmentName">Assignment Name</label>
                        <input type="text" class="form-control" id="editAssignmentName" name="editAssignmentName" required>
                    </div>
                    <div class="form-group">
                        <label for="editAssignmentDescription">Assignment Description</label>
                        <textarea class="form-control" id="editAssignmentDescription" name="editAssignmentDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editDueDate">Due Date</label>
                        <input type="date" class="form-control" id="editDueDate" name="editDueDate" required>
                    </div>
                    <!-- Optional resource section -->
                    <div class="form-group">
                        <label for="editResourceType">Resource Type</label>
                        <select class="form-control" id="editResourceType" name="editResourceType">
                            <option value="">Select Resource Type</option>
                            <option value="PDF">PDF</option>
                            <option value="Video">Video</option>
                            <option value="Image">Image</option>
                            <option value="Audio">Audio</option>
                            <option value="Zip">Zip</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editResourceFile">Resource File</label>
                        <input type="file" class="form-control-file" id="editResourceFile" name="editResourceFile" accept="">
                    </div>
                    <div class="form-group">
                        <label id="currentFileLabel"></label>
                        <span id="currentFile"></span>
                    </div>
                    <!-- Hidden containers for success and error messages -->
                    <div id="successMessage4" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="successMessage4Text"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="errorMessage4" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                        <span id="errorMessage4Text"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="reloadPage()">Close</button>
                <button type="button" class="btn btn-primary" onclick="editAssignment()">Save Changes</button>
            </div>
        </div>
    </div>
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
    <script>
// Define the openAddContentModal function
function openAddContentModal(topicId) {
    // Set the value of the hidden input field to the topicId
    $('#topicId').val(topicId);
    // Show the modal
    $('#addContentModal').modal('show');
}

// Change the click event handler to call openAddContentModal function
$('.show-add-content-modal').click(function() {
    // Get the topic ID from the data attribute
    var topicId = $(this).data('topic-id');
    // Call the openAddContentModal function with the topic ID
    openAddContentModal(topicId);
});
// JavaScript function to show the edit assignment modal with assignment details
function showEditAssignmentModal(assignmentId, assignmentName, assignmentDescription, dueDate, resourceType, resourcePath) {
    // Populate the edit modal fields with the retrieved data
    $('#editAssignmentId').val(assignmentId);
    $('#editAssignmentName').val(assignmentName);
    $('#editAssignmentDescription').val(assignmentDescription);
    $('#editDueDate').val(dueDate);
    $('#editResourceType').val(resourceType);

    // Show the current file path
    if (resourcePath) {
        var fileNameWithoutAssignments = resourcePath.replace('assignments/', ''); // Remove "assignments/" part
        $('#currentFile').text('Current File: ' + fileNameWithoutAssignments);
    } else {
        $('#currentFile').text('No file selected');
    }


    // Show the edit assignment modal
    $('#editAssignmentModal').modal('show');
}

 // Reload the page function
 function reloadPage() {
        location.reload();
    }

// Function to handle the deletion of a topic
function deleteTopic(topicId) {
    // Send an AJAX request to delete the topic
    $.ajax({
        type: 'POST',
        url: 'delete_topic.php',
        data: { topicId: topicId },
        success: function(response) {
            // Show success message as Bootstrap alert
            $('.container-fluid').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            // Reload the page after 1 second
            setTimeout(function() {
                reloadPage();
            }, 1000);
        },
        error: function() {
            // Show error message as Bootstrap alert
            $('.container-fluid').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error deleting topic. Please try again later.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
    });
}


$(document).ready(function() {
    // Handle content type selection
    $('#contentType').change(function() {
        var selectedType = $(this).val();
        if (selectedType === 'PDF' || selectedType === 'Audio' || selectedType === 'Video' || selectedType === 'Image') {
            $('#fileInputContainer').show();
        } else {
            $('#fileInputContainer').hide();
        }
    });

    // Handle submission of the "Add Content" form
    $('#addContentForm').submit(function(event) {
        event.preventDefault();
        
        // Check if a file is required for the selected content type
        var contentType = $('#contentType').val();
        if ((contentType === 'PDF' || contentType === 'Audio' || contentType === 'Video' || contentType === 'Image') && $('#contentFile').get(0).files.length === 0) {
            // If a file is required and not uploaded, show an error message
            $('#errorMessageText').text('Please upload a file for the selected content type.');
            $('#errorMessage').show();
            return; // Exit the function to prevent form submission
        }
        
        // If a file is uploaded or not required, proceed with form submission
        var formData = new FormData($(this)[0]);
        $.ajax({
            type: 'POST',
            url: 'add_content.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.startsWith("success")) {
                    // Show success message
                    $('#successMessageText').text(response.substring(8));
                    $('#successMessage').show();
                    // Close the modal
                    $('#addContentModal').modal('hide');
                    // Reload the page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000); // 2000 milliseconds = 2 seconds
                } else {
                    // Show error message
                    $('#errorMessageText').text(response.substring(6));
                    $('#errorMessage').show();
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                $('#errorMessageText').text('Error adding content: ' + errorMessage);
                $('#errorMessage').show();
            }
        });
    });

    // Handle submission of the "Edit Content" form
    $('#editContentForm').submit(function(event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            type: 'POST',
            url: 'edit_content.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Check if the response contains "success" or "error"
                if (response.startsWith("success:")) {
                    $('#successMessage2Text').text(response.replace("success: ", ""));
                    $('#successMessage2').show();
                    // Close the modal
                    $('#editContentModal').modal('hide');
                    // Reload the page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000); // 2000 milliseconds = 2 seconds
                } else if (response.startsWith("error:")) {
                    $('#errorMessage2Text').text(response.replace("error: ", ""));
                    $('#errorMessage2').show();
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                $('#errorMessageText').text("Error editing content: " + errorMessage);
                $('#errorMessage').show();
            }
        });
    });

    // Trigger the openEditContentModal function when the "Edit" button is clicked
    $('.edit-content-btn').click(function() {
        // Retrieve data attributes from the button
        var contentId = $(this).data('content-id');
        var contentName = $(this).data('content-name');
        var contentType = $(this).data('content-type');
        var contentText = $(this).data('content-text');
        var currentFile = $(this).data('current-file');
        var classTime = $(this).data('class-time'); // Retrieve class time

        // Populate the edit modal fields with the retrieved data
        $('#editContentId').val(contentId);
        $('#editContentName').val(contentName);
        $('#editContentType').val(contentType);
        $('#editContentText').val(contentText);
        $('#edit_class_time').val(classTime); // Set class time

        // Show the edit content modal
        $('#editContentModal').modal('show');

        // Show or hide the file input based on content type
        if (contentType === 'PDF' || contentType === 'Audio' || contentType === 'Video' || contentType === 'Image') {
            $('#editFileInputContainer').show();
            // If current file exists, display its name without the "content/" part
            if (currentFile) {
                var fileNameWithoutContent = currentFile.replace('content/', ''); // Remove "content/" part
                $('#editFileInputContainer label').text('Current File: ' + fileNameWithoutContent);
            } else {
                $('#editFileInputContainer.label').text('Upload File');
            }
        } else {
            $('#editFileInputContainer').hide();
        }
    });
});

// Function to handle the deletion of content
function deleteContent(contentId) {
    // Send an AJAX request to delete_content.php
    $.ajax({
        type: 'POST',
        url: 'delete_content.php',
        data: { contentId: contentId },
        success: function(response) {
            // Show success message or handle the response as needed
            $('.container-fluid').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            // Optionally reload the page or update the content list
            location.reload();
        },
        error: function(xhr, status, error) {
            // Show error message if request fails
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            $('.container-fluid').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error deleting content: ' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
    });
}
// Add event listener to activate button
$(document).on('click', '.btn-activate', function() {
    // Log a message to indicate that the button click event is being handled
    console.log('Activation button clicked');
    
    // Get content ID from the button's data attribute
    var contentId = $(this).data('content-id');
    
    // Log the content ID to verify it's being retrieved correctly
    console.log('Content ID:', contentId);
    
    // Call the activateContent function with the content ID
    activateContent(contentId);
});


// Function to activate content
function activateContent(contentId) {
    // Send AJAX request to activate_content.php
    $.ajax({
        type: 'POST',
        url: 'activate_content.php',
        data: { contentId: contentId },
        success: function(response) {
            // Show success message or handle the response as needed
            $('.container-fluid').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            // Optionally reload the page or update the content list
            location.reload();
        },
        error: function(xhr, status, error) {
            // Show error message if request fails
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            $('.container-fluid').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error activating content: ' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
    });
}
// Function to open the add assignment modal
function openAssignmentModal(topicId, topicName) {
    console.log("Topic ID before adding: " + topicId);

    // Set the values of the hidden input fields in the modal
    $('#assignmentTopicId').val(topicId);
    $('#assignmentTopicName').text(topicName);
    // Show the modal
    $('#addAssignmentModal').modal('show');
}


$('#resourceType').change(function() {
    var selectedType = $(this).val();
    var acceptValue = "";
    switch(selectedType) {
        case "PDF":
            acceptValue = ".pdf";
            break;
        case "Audio":
            acceptValue = "audio/*";
            break;
        case "Video":
            acceptValue = "video/*";
            break;
        case "Image":
            acceptValue = "image/*";
            break;
        case "Zip":
            acceptValue = ".zip,.rar";
            break;
        default:
            acceptValue = "";
            break;
    }
    $('#resourceFile').attr('accept', acceptValue);
});

    // Function to add an assignment
function addAssignment() {
    // Get form data
    var assignmentName = $('#assignmentName').val();
    var assignmentDescription = $('#assignmentDescription').val();
    var dueDate = $('#dueDate').val();
    var topicId = $('#assignmentTopicId').val();
    
    // Prepare FormData object to send form data and files
    var formData = new FormData();
    formData.append('assignmentName', assignmentName);
    formData.append('assignmentDescription', assignmentDescription);
    formData.append('dueDate', dueDate);
    formData.append('topicId', topicId);

    // Add selected resource file if present
    var resourceType = $('#resourceType').val();
    var resourceFile = $('#resourceFile')[0].files[0];
    if (resourceFile) {
        formData.append('resourceType', resourceType);
        formData.append('resourceFile', resourceFile);
    }

    // Send AJAX request to add_assignment.php
        $.ajax({
        type: 'POST',
        url: 'add_assignment.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Check if the response starts with "success" or "error"
            if (response.startsWith("success")) {
                // Show success message
                $('#successMessage3Text').text(response.substring(8)); // Remove "success: "
                $('#successMessage3').show();
                // Reload the page after 2 seconds
                setTimeout(function() {
                    location.reload();
                }, 2000); // 2000 milliseconds = 2 seconds
            } else {
                // Show error message
                $('#errorMessage3Text').text(response.substring(6)); // Remove "error: "
                $('#errorMessage3').show();
            }
        },
        error: function(xhr, status, error) {
            // Show error message if request fails
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            $('#errorMessage3Text').text('Error adding assignment: ' + errorMessage);
            $('#errorMessage3').show();
        }
    });

}
// Function to edit an assignment
function editAssignment() {
    // Get form data
    var assignmentId = $('#editAssignmentId').val();
    var assignmentName = $('#editAssignmentName').val();
    var assignmentDescription = $('#editAssignmentDescription').val();
    var dueDate = $('#editDueDate').val();
    
    // Prepare FormData object to send form data and files
    var formData = new FormData();
    formData.append('assignmentId', assignmentId);
    formData.append('assignmentName', assignmentName);
    formData.append('assignmentDescription', assignmentDescription);
    formData.append('dueDate', dueDate);

    // Add selected resource file if present
    var resourceType = $('#editResourceType').val();
    var resourceFile = $('#editResourceFile')[0].files[0];
    if (resourceFile) {
        formData.append('resourceType', resourceType);
        formData.append('resourceFile', resourceFile);
    }

    // Send AJAX request to edit_assignment.php
                $.ajax({
                type: 'POST',
                url: 'edit_assignment.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Check if the response starts with "success" or "error"
                    if (response.startsWith("success")) {
                        // Show success message
                        $('#successMessage4Text').text(response.substring(8)); // Remove "success: "
                        $('#successMessage4').show();
                        // Reload the page after 2 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // 2000 milliseconds = 2 seconds
                    } else {
                        // Show error message
                        $('#errorMessage4Text').text(response.substring(6)); // Remove "error: "
                        $('#errorMessage4').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message if request fails
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    $('#errorMessage4Text').text('Error editing assignment: ' + errorMessage);
                    $('#errorMessage4').show();
                }
            });

}
// JavaScript function to handle the deletion of assignments
function deleteAssignment(assignmentId) {
    // Send an AJAX request to delete_assignment.php
    $.ajax({
        type: 'POST',
        url: 'delete_assignment.php', // Assuming the URL for deleting assignments is delete_assignment.php
        data: { assignmentId: assignmentId },
        success: function(response) {
            // Show success message or handle the response as needed
            $('.container-fluid').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            // Optionally reload the page or update the assignment list
            location.reload();
        },
        error: function(xhr, status, error) {
            // Show error message if request fails
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            $('.container-fluid').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error deleting assignment: ' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
    });
}
    // JavaScript function to handle the activation of assignments
function activateAssignment(assignmentId) {
    // Send an AJAX request to activate_assignment.php
    $.ajax({
        type: 'POST',
        url: 'activate_assignment.php', // Assuming the URL for activating assignments is activate_assignment.php
        data: { assignmentId: assignmentId },
        success: function(response) {
            // Show success message or handle the response as needed
            $('.container-fluid').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            // Optionally reload the page or update the assignment list
            location.reload();
        },
        error: function(xhr, status, error) {
            // Show error message if request fails
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            $('.container-fluid').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error activating assignment: ' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
    });
}

function setStatusAnnouncement(announcementId) {
        // Send an AJAX request to deactivate_announcement.php
        $.ajax({
            type: 'POST',
            url: 'set_status_announcement.php', 
            data: { announcementId: announcementId },
            success: function(response) {
                // Show success message or handle the response as needed
                $('.container-fluid').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                // Optionally reload the page or update the announcement list
                location.reload();
            },
            error: function(xhr, status, error) {
                // Show error message if request fails
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                $('.container-fluid').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Error deleting announcement: ' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
        });
    }
</script>
</body>

</html>
