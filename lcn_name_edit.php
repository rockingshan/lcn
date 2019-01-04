<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");	//redirect to index page if not logged in
}
//starting the connection to db
require_once "include/connect.php";
include 'include/log.php';
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

if (isset($_GET['edit_flag'])){
	$new_name=$_GET['new_name'];
	$old_sid=$_GET['editname_sid'];
	$old_name=$_GET['old_name'];
	$name_update_sql="UPDATE channel_tb SET channel='$new_name' WHERE sid='$old_sid'";
	$editname_result=mysqli_query($con,$name_update_sql);
	if (!$editname_result) { // add this check.
    die('Error: ' . mysqli_error($con));
    }
    write_log($old_sid." name changed from'".$old_name."' to '".$new_name."'");
	header("location:secure_page.php");
}else{
$edit_sid=$_GET['sid'];
$editname_sql= "SELECT * from channel_tb WHERE sid=$edit_sid";
$editname_result = mysqli_query($con,$editname_sql);
if (!$editname_result) { // add this check.
    die('Error: ' . mysqli_error($con));
}
$editname_row = mysqli_fetch_array($editname_result);
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


	</head>

	<body>
		<div class="container-fluid">
	<h2>Edit Channel Name</h2>

		<form action="lcn_name_edit.php" method="get">
			<div class="form-group">
		<table>
			<tbody>
			<tr>
				<td style="padding-right:10px"><label class="control-label"><?php echo $editname_row['channel'] ?></label></td>
				<td align="center"><input type="text" class="form-control" name="new_name" value="<?php echo $editname_row['channel'] ?>"/></td>
				<input type="hidden" name="editname_sid" value="<?php echo $editname_row['sid']; ?>" />
				<input type="hidden" name="edit_flag" value="1" />
				<input type="hidden" name="old_name" value="<?php echo $editname_row['channel'] ?>" />
				<td style="padding-left:10px"><input type="submit" value="Change Name" class="btn btn-primary" /></td>
			</tr>
		</tbody>
		</table>
		<div>
		</form>
	</div>
</body>
</html>
