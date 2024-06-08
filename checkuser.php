<?php  
//check.php  
require_once "include/connect.php";
if(isset($_POST["user_name"]))
{
 $username = mysqli_real_escape_string($auth, $_POST["user_name"]);
 $query = "SELECT * FROM auth_tb WHERE user = '".$username."'";
 $result = mysqli_query($auth, $query);
 echo mysqli_num_rows($result);
}
?>