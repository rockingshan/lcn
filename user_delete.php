<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");
	//redirect to index page if not logged in
}
if ((isset($_SESSION['user'])) && $_SESSION['is_admin'] == 0) {
    header("location:secure_page.php"); //check if user is admin
}
include("include/connect.php");
$delete_user=$_GET['user_id'];
    $sql1 = "DELETE FROM `auth_tb` WHERE `user_id`='$delete_user'";
    $er_res = mysqli_query($auth, $sql1); 
    if (!$er_res) {// add this check.
        die('Invalid query: ' . mysqli_error($con));
    }
header("location:secure_page.php");
?>
    