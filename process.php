<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");	//redirect to index page if not logged in
}
//starting the connection to db
include("include/connect.php");


echo $_POST['new_lcn'];


?>