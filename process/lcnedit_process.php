<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['user'])) {
	header("location:index.php");	//redirect to index page if not logged in
}
//starting the connection to db
require_once "../include/connect.php";
require_once "../include/hex_maker.php";
require_once "../include/log.php";


$new_lcn=$_POST['new_lcn'];
$edit_sid=$_POST['edit_sid'];

$get_old_lcn_sql="SELECT lcn FROM `channel_tb` WHERE sid='$edit_sid' ";
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");
$get_old_lcn_row=mysqli_fetch_array(mysqli_query($con, $get_old_lcn_sql));
$old_lcn=$get_old_lcn_row['lcn'];
$new_lcn_hex=hex_convert($new_lcn);

$lcn_edit_sql="UPDATE channel_tb SET `lcn`='$new_lcn', `lcnhex`='$new_lcn_hex' WHERE sid='$edit_sid'";
$lcn_edit_result=mysqli_query($con, $lcn_edit_sql);
if (!$lcn_edit_result) { // add this check.
    die('Error: ' . mysqli_error($con));
    }
    write_log($edit_sid." LCN changed from'".$old_lcn."' to '".$new_lcn."'");
	header("location:../secure_page.php");

?>