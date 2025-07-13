<?php

@include 'db.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:admin_page.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

   <title>admin page</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="login.css">

</head>
<body>
   
<div class="container">

   <div class="content">
      <h3>hi, <span>admin</span></h3>
      <h1>welcome <span><?php echo $_SESSION['admin_name'] ?></span></h1>
      <p>this is an admin page</p>  
      <a href="login_form.php" class="btn">login</a>
      <a href="register_form.php" class="btn">register</a>
      <a href="index.html" class="btn">Home</a>
      <a href="admin.php" class="btn">Add photo</a>
      <a href="ad.php" class="btn">View bookings</a>
      <a href="logout.php" class="btn">logout</a>

   </div>

</div>

</body>
</html>