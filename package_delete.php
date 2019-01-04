<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("location:index.php");   //redirect to index page if not logged in
}
//starting the connection to db
include("include/connect.php");
include 'include/log.php';
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

$delete_sid=$_GET['sid'];
    $sql1 = "DELETE FROM `channel_tb` WHERE `sid`='$delete_sid'";
    $sql2 = "DELETE FROM `sid_tb` WHERE `sid`='$delete_sid'";
    $sql3 = "DELETE FROM `package_tb` WHERE `sid`='$delete_sid'";
    $er_res = mysqli_query($con, $sql1);
    $er_res = mysqli_query($con, $sql2);
    $er_res = mysqli_query($con, $sql3);
    
    if (!$er_res) {// add this check.
        die('Invalid query: ' . mysqli_error($con));
    }
    $_SESSION['sidcounter'][]=$new_sid;
    write_log($delete_sid." deleted");
header("location:secure_page.php");


?>
    