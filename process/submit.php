<?php
session_start();
$_SESSION['sidcounter']=array();
//starting the connection to db
require_once "../include/connect.php";
include '../include/log.php';
mysqli_select_db($con,'meghbela_lcn_db_kol') or die("No database");
$user=mysqli_real_escape_string($con,$_POST['user']);
$pass=mysqli_real_escape_string($con,$_POST['pass']);
$hashed_pass=md5($pass);
$sql_submit="SELECT user from auth_tb WHERE user='$user' and pass='$hashed_pass'";
$res_submit=mysqli_query($con,$sql_submit);
$res_data=mysqli_fetch_array($res_submit);
if ($res_data) {
	echo "ok";
	$_SESSION['user']=$user;
	$_SESSION['select_db']='';
	//header("location:../db_select.php");
} else {
	echo "Wrong username/password";

}


?>
