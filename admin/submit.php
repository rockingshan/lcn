<?php
session_start();
//$_SESSION['errMsg'] = '';
//starting the connection to db
require_once "../include/connect.php";
$user=mysqli_real_escape_string($con,$_POST['user']);
$pass=mysqli_real_escape_string($con,$_POST['pass']);
$hashed_pass=md5($pass);
$sql_submit="SELECT * from auth_tb WHERE user='$user' and pass='$hashed_pass'";
$res_submit=mysqli_query($auth,$sql_submit);
$res_data=mysqli_fetch_array($res_submit);
if ($res_data) {
	if ($res_data[6] == 1) {
		if ($res_data[7] == 1) { //Check if is_active set to true
			$_SESSION['user'] = $user;
			$_SESSION['user_id'] = $res_data[0];
			$_SESSION['is_admin'] = $res_data[6];
			header("Location: index.php");
			exit(); //Redirect user to home page;
		}
		elseif ($res_data[4] == 0) { //check if is_active set to false
			$_SESSION['errMsg'] = "Your account is suspended. Contact your Administrator";
			header("Location: login.php");
		}
		
	}else {
		$_SESSION['errMsg'] = "You need to be an Administrator to login here";
		header("Location: login.php");
	}
	
}
 else {
	$_SESSION['errMsg'] = "Wrong username/password"; 
	header("Location: login.php"); 

}
?>
