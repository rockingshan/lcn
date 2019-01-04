<?php
    session_start();

if (!isset($_SESSION['user'])) {
	header("location:index.php");
}
//starting the connection to db
require_once "include/connect.php";
include 'include/log.php';
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

if (isset($_GET['edit_flag'])){
	$old_sid=$_GET['editpack_sid'];
	$bronze=$_GET['bronzepack'];
	$silver=$_GET['silverpack'];
	$gold=$_GET['goldpack'];
	$platinum=$_GET['platinumpack'];
	//$power=$_GET['powerpack'];
	$price=$_GET['price'];
	$pack_update_sql="UPDATE package_tb SET bronze='$bronze',silver='$silver',gold='$gold',platinum='$platinum',price='$price' WHERE sid='$old_sid'";
	$editpack_result=mysqli_query($con,$pack_update_sql);
	if (!$editpack_result) { // add this check.
    die('Error: ' . mysqli_error($con));
    }
    write_log("package changed as bronze='$bronze',silver='$silver',gold='$gold',platinum='$platinum',power='$power',price='$price' WHERE sid='$old_sid'");
	header("location:secure_page.php");
}else{
$pcedit_sid=$_GET['sid'];
//making the search in db
$sql = "SELECT * FROM channel_tb INNER JOIN package_tb ON channel_tb.sid=package_tb.sid WHERE package_tb.sid='$pcedit_sid'";

$result = mysqli_query($con,$sql);
$package_result= mysqli_fetch_array($result);
if (!$result) { // add this check.
    die('Invalid query: ' . mysqli_error());
}
}
?>
<head>
	<link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
<link rel="manifest" href="/images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Package EDIT Mode</title>
</head>

<body>
  <div class="container-fluid">
<form action="package_edit.php" method="get">

	<table class="table">
    <thead>
		<tr>
    <th>LCN</th>
    <th>Name </th>
    <th>Bronze </th>
    <th> Silver </th>
    <th> Gold </th>
    <th> Platinum </th>
    <th> Price(&#8377) </th>
  </tr>
</thead>
<tbody>
  <tr>
    <td><?php echo $package_result['lcn'] ?></td>
    <td><?php echo $package_result['channel'] ?></td>
    <td><select name="bronzepack">
    <option value="YES" <?php if($package_result['bronze'] == "YES") { echo "SELECTED"; } ?>>YES</option>
    <option value=" " <?php if($package_result['bronze'] == "") { echo "SELECTED"; } ?>>NO</option
  </select></td>
    <td><select name="silverpack">
    <option value="YES" <?php if($package_result['silver'] == "YES") { echo "SELECTED"; } ?>>YES</option>
    <option value=" " <?php if($package_result['silver'] == "") { echo "SELECTED"; } ?>>NO</option
  </select></td>
    <td><select name="goldpack">
    <option value="YES" <?php if($package_result['gold'] == "YES") { echo "SELECTED"; } ?>>YES</option>
    <option value=" " <?php if($package_result['gold'] == "") { echo "SELECTED"; } ?>>NO</option
  </select></td>
    <td><select name="platinumpack">
    <option value="YES" <?php if($package_result['platinum'] == "YES") { echo "SELECTED"; } ?>>YES</option>
    <option value=" " <?php if($package_result['platinum'] == "") { echo "SELECTED"; } ?>>NO</option
  </select></td>
    <!--<td><select name="powerpack">
    <option value="YES" <?php /*if($package_result['power'] == "YES") { echo "SELECTED"; } */?>>YES</option>
    <option value=" " <?php /*if($package_result['power'] == "") { echo "SELECTED"; } */?>>NO</option
  </select></td>-->
    <td><div class="form-group"><input class="form-control input-sm" id="inputsm" type="text" name="price" value="<?php echo $package_result['price'] ?>" /></div></td>
 </tr>
 <tr>
 	<input type="hidden" name="editpack_sid" value="<?php echo $package_result['sid']; ?>" />
				<input type="hidden" name="edit_flag" value="1" />
				<td class="authtd" colspan="8" align="middle"><input type="submit" class="btn btn-primary" value="Change Package" /></td>
 </tr>
 </tbody>
	</table>
	</div>
</form>








</body>
