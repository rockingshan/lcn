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
$sql_submit="SELECT * from auth_tb WHERE user='$user' and pass='$hashed_pass'";
$res_submit=mysqli_query($con,$sql_submit);
$res_data=mysqli_fetch_array($res_submit);
if ($res_data) {
	if ($res_data[7] == 1) { //Check if is_active set to true
		echo "ok";  // ok string returned to login.js for succesful submit
		$_SESSION['user']=$user;
		$_SESSION['user_id']=$res_data[0];
		$_SESSION['select_db']='';	
		$_SESSION['is_admin'] = $res_data[6];
	}
	elseif ($res_data[4] == 0) { //check if is_active set to false
		echo "Your account is suspended. Contact your Administrator";
	}
}
 else {
	echo "Wrong username/password";  

}


?>
