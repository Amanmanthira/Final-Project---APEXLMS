<?php
// Include your database connection
include("../connection/connect.php");

// Get the search query from AJAX request
$search = $_POST['query'];

// Prepare and execute the SQL query to search for courses
$sql = "SELECT * FROM courses WHERE course_name LIKE '%$search%' AND is_active = 1";
$result = mysqli_query($db, $sql);

// Check if there are any results
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo '<a href="course.php?id='.$row['course_id'].'">'.$row['course_name'].'</a><br>';
    }
}else{
    echo 'No results found';
}

// Close the database connection
mysqli_close($db);
?>
