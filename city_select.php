<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
session_start();

if (!isset($_SESSION['user'])) {
	header("location:index.php");	
}
require_once "include/connect.php";
//mysqli_select_db($con,'meghbela_lcn_db_kol') or die("No database");
$user_id = $_SESSION['user_id']; //get user id from session //prepare sql in below line. matches two table and get city list
$sql_submit="SELECT * FROM city_tb,user_privilege_tb WHERE user_privilege_tb.user_id = $user_id AND city_tb.city_id = user_privilege_tb.city_id ORDER BY user_privilege_tb.city_id";
$res_submit=mysqli_query($auth,$sql_submit) or die(mysqli_error($con));
//$res_data=mysqli_fetch_assoc($res_submit);

?>
<!-- Latest compiled and minified CSS of Bootstrap CSS-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>

<body>
<div align="center" width="800px">
<form class="form-horizontal" method="post" action="secure_page.php">
<fieldset>

<!-- Form Name -->
<legend>Select City</legend>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Select City here</label>
  <div class="col-md-5">
    <select id="selectdb" name="selectdb" class="form-control">
    <?php
    while($row = mysqli_fetch_assoc($res_submit)){

      echo "<option value=".$row[city_id].">".$row[city_name]."</option>";
    
    } ?>
    </select>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Go</button>
  </div>
</div>

</fieldset>
</form>
</div>
</body>
</html>