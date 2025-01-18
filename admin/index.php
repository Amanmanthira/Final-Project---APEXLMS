<!DOCTYPE html>
<html lang="en" >
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(isset($_SESSION['apex_admin_id'])) {
  header("Location: dashboard.php");
  exit();
}
if(isset($_POST['submit']))
{
    $apex_admin_name = $_POST['apex_admin_name'];
    $apex_admin_password = $_POST['apex_admin_password'];
    
    if(!empty($_POST["submit"])) 
    {
        $loginquery = "SELECT * FROM admin WHERE apex_admin_name='$apex_admin_name'";
        $result = mysqli_query($db, $loginquery);
        $row = mysqli_fetch_array($result);
    
        if(is_array($row) && password_verify($apex_admin_password, $row['apex_admin_password']))
        {
            $_SESSION["apex_admin_id"] = $row['apex_admin_id'];
            header("Location: dashboard.php");
          } 
        else
        {
            $message = "Invalid Username or Password!";
        }
    }
}
?>

<head>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
  <title>APEX Institute | Admin Dasboard</title>
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
<link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

      <link rel="stylesheet" href="css/login.css">

  
</head>

<body>

  
<div class="container">
  <div class="info">
    <h1>Apex Institute </h1><h2> Admin Login</h2>
  </div>
</div>
<div class="form">
  <div class="thumbnail"><img src="images/manager.png"/></div>
  
  <span style="color:red;"><?php echo $message; ?></span>
   <span style="color:green;"><?php echo $success; ?></span>
   <br>
   <br>
  <form class="login-form" action="index.php" method="post">
    <input type="text" placeholder="Username" name="apex_admin_name"/>
    <input type="password" placeholder="Password" name="apex_admin_password"/>
    <input type="submit"  name="submit" value="Login" />
  </form>
  
</div>

  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script src='js/index.js'></script>
  

    

  

</body>

</html>
