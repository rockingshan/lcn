<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");	//redirect to index page if not logged in
}
//starting the connection to db
require_once "include/connect.php";
include 'include/log.php';
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

$swap_sid=$_GET['sid'];
$edit_sql= "SELECT * from channel_tb WHERE sid='$swap_sid'";
$edit_result = mysqli_query($con,$edit_sql);
if (!$edit_result) { // add this check.
    die('Invalid query: ' . mysqli_error());
}
$edit_row = mysqli_fetch_array($edit_result);

$swap_sql= "SELECT * from channel_tb WHERE sid<>'$swap_sid' ORDER BY lcn";
$swap_result=mysqli_query($con, $swap_sql);

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
		<h3 align="center">Swap LCN </h3>
	</head>

	<body>
		<div class="container-fluid">
		<form action="swap_process.php" method="post">
		<table class="table">
			<tr>
				<th>SID</th><th class="text-center">CHANNEL</th><th class="text-right">LCN</th>
			</tr>
			<tr>
				<td><?php echo $edit_row['sid'] ?></td>
				<td class="text-center"><?php echo $edit_row['channel'] ?></td>
				<td class="text-right"><?php echo $edit_row['lcn'] ?></td>
			</tr>
			<tr align="center" style="margin-top:10px">
				<th></th><th>Select Swapping LCN</th>
			</tr>
			<tr>

				<td colspan="3" align="center">
					<?php
						echo '<select name="swap_sid" class="form-control" style="margin-top:10px">';
						while($swap_row=mysqli_fetch_array($swap_result))
						{
							echo "<option value=".$swap_row['sid'].">".$swap_row['sid']."        ||       ".$swap_row['channel']."       ||        ".$swap_row['lcn']."</option>";
						}
						echo "</select>";
						?>
				</td>
				</tr>
				<tr>
				<td >
					<input type="hidden" name="old_sid" value="<?php echo $edit_row['sid']; ?>" />
					<input class="btn btn-primary" type="submit" value="Swap LCN"  style="margin-top:10px"/>
				</td>
				</tr>
</div>
		</table>
		</form>



	</body>
</html>
