<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");
	//redirect to index page if not logged in
}
//starting the connection to db
include ("include/connect.php");
include 'include/hex_maker.php';
include 'include/log.php';
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

$blank_lcn_sql = "SELECT * from lcn_tb WHERE lcn_tb.lcn NOT IN (SELECT lcn FROM channel_tb)";
$blank_lcn_result = mysqli_query($con, $blank_lcn_sql);
if (!$blank_lcn_result) {// add this check.
	die('Invalid query: ' . mysqli_error($con));
}
if (isset($_GET['edit_flag'])) {
	$new_name = $_GET['channel'];
	$new_sid = $_GET['sid'];
	$new_lcn = $_GET['new_lcn'];
	$new_tsid = $_GET['tsid'];
	$new_freq = $_GET['freq'];
	
	$new_lcn_hex = hex_convert($new_lcn);
	$new_sid_hex = hex_convert($new_sid);
	
	$sql1 = "INSERT INTO `channel_tb`(`sid`, `channel`, `lcn`, `lcnhex`) VALUES ('$new_sid','$new_name','$new_lcn','$new_lcn_hex')";
	$sql2 = "INSERT INTO `sid_tb` (`sid`, `ts`,`freq`,`sidhex`) VALUES ('$new_sid','$new_tsid','$new_freq','$new_sid_hex')";
    $sql3 = "INSERT INTO `package_tb`(`sid`, `bronze`, `silver`, `gold`, `platinum`, `diamond`, `price`) VALUES ('$new_sid','','','','','','')";
    $er_res = mysqli_query($con, $sql1);
    $er_res = mysqli_query($con, $sql2);
	$er_res = mysqli_query($con, $sql3);

	if (!$er_res) {// add this check.
		die('Invalid query: ' . mysqli_error($con));
	}

    $_SESSION['sidcounter'][]=$new_sid;
    $_SESSION['sid_lcn'][]=array($new_sid=>$new_lcn_hex);
    write_log($new_sid." '".$new_name."' position ".$new_lcn." added");
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

<script>
$(document).on('keyup', '.numeric-only', function(event) {
   var v = this.value;
   if($.isNumeric(v) === false) {
        //chop off the last char entered
        this.value = this.value.slice(0,-1);
   }
});
	​​
</script>
	</head>

<body>
	<div class="container-fluid">
		<h3>Add New Channel</h3>
		<form action="channel_add.php" method="get">
			<div class="form-group">
			<div class="col-sm-4">
			<label for="inputdefault">New SID</label>
			<input class="form-control" id="inputdefault" type="number" name="sid" maxlength="5" />
			</div>
			<div class="col-sm-4">
			<label for="inputTS" class="col-sm-6">TS ID</label>
			<input class="form-control" id="inputTS" type="number" name="tsid" maxlength="5" />
			</div>
			<div class="col-sm-4">
			<label for="inputFREQ">Frequency</label>
			<input class="form-control" id="inputFREQ" type="number" name="freq" maxlength="5" />
			</div>
		</div>
		<div class="form-group">
			<label for="newchannel">New channel Name</label>
			<input type="text" id="newchannel" name="channel" class="form-control" />
		</div>
		<div class="form-group">
			<label for="selectpos">Select Posiiton</label>
			<?php
			echo '<select name="new_lcn" id="selectpos" class="form-control">';
			while ($blank_lcn_row = mysqli_fetch_array($blank_lcn_result)) {
				echo "<option value=" . $blank_lcn_row['lcn'] . ">" . $blank_lcn_row['genre'] . "   ||   " . $blank_lcn_row['lcn'] . "</option>";
			}
			echo "</select>";
						?>
			</div>
			<input type="hidden" name="edit_flag" value="1" />
			<input type="submit" class="btn btn-success" name="submit" value="Add Channel" />
		</form>
	</div>

</body>
</html>
