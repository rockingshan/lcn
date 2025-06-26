<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['user'])) {
	header("location:index.php");	//redirect to index page if not logged in
}
//starting the connection to db
require_once "include/connect.php";
include 'include/log.php';

$edit_cid = $_GET['channel_id'];
$edit_sql = "SELECT * FROM `channel_master_tb` 
LEFT JOIN broadcaster_tb ON channel_master_tb.broadcaster_id = broadcaster_tb.broadcaster_id
WHERE channel_master_tb.channel_id='$edit_cid'";
$edit_result = mysqli_query($auth, $edit_sql);
if (!$edit_result) { // add this check.
	die('Invalid query: ' . mysqli_error());
}
$editname_row = mysqli_fetch_array($edit_result);

$broad_sql = "SELECT * FROM broadcaster_tb";
$broad_result = mysqli_query($auth, $broad_sql);
$broad_result_array = mysqli_fetch_array($broad_result);

?>

<html>

<head>
	<link rel="icon" type="image/png" sizes="192x192" href="images/android-icon-192x192.png">
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
							<td style="padding-right:10px"><label class="control-label"><?php echo $editname_row['channelName'] ?></label></td>
							<td align="center"><input type="text" class="form-control" name="new_name" value="<?php echo $editname_row['channelName'] ?>" /></td>
							<input type="hidden" name="editname_sid" value="<?php echo $editname_row['channel_id']; ?>" />
							<?php 
							 echo '<select name="broadcaster_id" class="form-select">';
								if ($broad_result->num_rows > 0) {
								while ($row = $broad_result->fetch_assoc()) {
								echo '
								<option value="' . htmlspecialchars($row[" broadcaster_id"]) . '">' . htmlspecialchars($row["broadcaster"]) . '</option>' ;
									}
									} else {
									echo '<option value="">No users found</option>' ;
									}

									echo '</select>' ;
									?>
									<input type="hidden" name="edit_flag" value="1" />
								<input type="hidden" name="old_name" value="<?php echo $editname_row['channelName'] ?>" />
								<td style="padding-left:10px"><input type="submit" value="Change Name" class="btn btn-primary" /></td>
						</tr>
					</tbody>
				</table>
				<div>
		</form>
	</div>
</body>

</html>