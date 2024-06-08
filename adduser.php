<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");
	//redirect to index page if not logged in
}
if ((isset($_SESSION['user'])) && $_SESSION['is_admin'] == 0) {
    header("location:secure_page.php"); //check if user is admin
}
require_once "include/connect.php";

if (isset($_GET['edit_flag'])) {
  $user = mysqli_real_escape_string($auth,$_GET['username']);
  $first_name = mysqli_real_escape_string($auth,$_GET['first_name']);
  $last_name = mysqli_real_escape_string($auth,$_GET['last_name']);
  $email = mysqli_real_escape_string($auth,$_GET['email']);
  $pass = md5(mysqli_real_escape_string($auth,$_GET['password']));
  if(isset($_GET['is_admin'])){$is_admin = '1';}else{$is_admin = '0';}
  if(isset($_GET['is_active'])){$is_active = '1';}else{$is_active = '0';}

  $user_add_sql = "INSERT INTO `auth_tb` (`user`, `pass`, `first_name`, `last_name`, `email`, `is_admin`, `is_active`) VALUES ('$user', '$pass', '$first_name', '$last_name', '$email', '$is_admin', '$is_active') ";
  $er_res = mysqli_query($auth, $user_add_sql);
  if (!$er_res) {// add this check.
		die('Invalid query: ' . mysqli_error($auth));
  }
  header("location:secure_page.php");

}

?>

<html>
	<head>
		<link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
<link rel="manifest" href="/images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<script>  
 $(document).ready(function(){  
   $('#username').blur(function(){

     var username = $(this).val();

     $.ajax({
      url:'checkuser.php',
      method:"POST",
      data:{user_name:username},
      success:function(data)
      {
       if(data != '0')
       {
        $('#availability').html('<span class="text-danger">Username not available</span>');
        $('#submit').attr("disabled", true);
       }
       else
       {
        $('#availability').html('<span class="text-success">Username Available</span>');
        $('#submit').attr("disabled", false);
       }
      }
     })

  });
 });  
</script>

</head>

<body>
	<div class="container-fluid">
    <form class="form-horizontal" action="adduser.php" method="get">
      <fieldset>
        <legend>Add/Modify User</legend>
        <div class="form-group">
          <label class="col-md-4 control-label" for="username">Username</label>  
          <div class="col-md-4">
            <input id="username" name="username" type="text" placeholder="username" class="form-control input-md" required="" minlength="6"><span id="availability"></span>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="password">Password</label>
          <div class="col-md-4">
            <input id="password" name="password" type="password" placeholder="******" class="form-control input-md" required="" minlength="6">
            <span class="help-block">Alphanumeric 6 characters</span>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="first_name">First name</label>  
          <div class="col-md-4">
            <input id="first_name" name="first_name" type="text" placeholder="First Name" class="form-control input-md" required="" minlength="3">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="last_name">Last Name</label>  
          <div class="col-md-4">
            <input id="last_name" name="last_name" type="text" placeholder="Last Name" class="form-control input-md">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="email">Email</label>  
          <div class="col-md-4">
            <input id="email" name="email" type="email" placeholder="Email" class="form-control input-md" required="">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="is_active"></label>
          <div class="col-md-4">
            <label class="checkbox-inline" for="is_active">
              <input type="checkbox" name="is_active" id="is_active" value="1"> Active
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-md-4 control-label" for="is_admin"></label>
        <div class="col-md-4">
          <label class="checkbox-inline" for="is_admin">
            <input type="checkbox" name="is_admin" id="is_admin" value="1"> Administrator
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-md-4 control-label" for="submit"></label>
        <div class="col-md-4">
          <button id="submit" name="submit" class="btn btn-success">Save</button>
        </div>
      </div>
      <input type="hidden" name="edit_flag" value="1" />
    </fieldset>
  </form>
</div>
</body>