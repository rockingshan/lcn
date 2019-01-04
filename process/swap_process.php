<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");	//redirect to index page if not logged in
}
//starting the connection to db
require_once "../include/connect.php";
include '../include/log.php';
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

$swap_sid= $_POST['swap_sid'];
$old_sid= $_POST['old_sid'];

$query_old="SELECT * from channel_tb WHERE sid=$old_sid";
$old_result=mysqli_query($con, $query_old);
$old_data=mysqli_fetch_array($old_result);

$query_new="SELECT * from channel_tb WHERE sid=$swap_sid";
$new_result=mysqli_query($con, $query_new);
$new_data=mysqli_fetch_array($new_result);



$query_old_new="UPDATE channel_tb SET lcn='".$new_data['lcn']."',lcnhex='".$new_data['lcnhex']."' WHERE sid='".$old_data['sid']."'";
$query_new_old="UPDATE channel_tb SET lcn='".$old_data['lcn']."',lcnhex='".$old_data['lcnhex']."' WHERE sid='".$new_data['sid']."'";

mysqli_query($con,$query_old_new);
mysqli_query($con,$query_new_old);
$_SESSION['sidcounter'][]=$old_sid;
$_SESSION['sidcounter'][]=$swap_sid;
write_log($old_data['sid']." lcn changed to ".$new_data['lcn']);
write_log($new_data['sid']." lcn changed to ".$old_data['lcn']);
header("location:../secure_page.php");


?>