<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("location:index.php");
	//redirect to index page if not logged in
}
if ((isset($_SESSION['user'])) && $_SESSION['is_admin'] == 0) {
    header("location:secure_page.php"); //check if user is admin
}
require_once "include/connect.php";
$is_admin = "";
$is_active = "";

$user_sql = "SELECT * FROM auth_tb";
$res_submit=mysqli_query($auth,$user_sql) or die(mysqli_error($auth));
?>
<link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
<link rel="manifest" href="/images/manifest.json">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="lib/jquery.modal.min.js" type="text/javascript" charset="utf-8"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

<link rel="stylesheet" href="lib/bootstrap-table.css">
   </style>

<script src="lib/login.js"></script>

<script src="lib/bootstrap-table.min.js"></script>
<!-- <script src="lib/bootstrap-table-toolbar.js"></script> -->
<script>
function confirmAction(){
      var confirmed = confirm("Are you sure? This will remove this entry forever.");
      return confirmed;
}
</script>


<link rel="stylesheet" href="lib/jquery.modal.min.css">
<title>View/Modify user</title>


</head>

<body>
	<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Meghbela LCN</a>
    </div>
		<ul class="nav navbar-nav">
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-floppy-save"></span> Download LCN
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="export.php">Download LCN file</a></li>
          <li><a href="create_master.php">Download Master LCN file</a></li>
        </ul>
      </li>
			<li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-floppy-save"></span> Download Package details
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="export_package.php?par=bronze">Export Bronze package</a></li>
          <li><a href="export_package.php?par=silver">Export Silver package</a></li>
          <li><a href="export_package.php?par=gold">Export Gold package</a></li>
					<li><a href="export_package.php?par=platinum">Export Platinum package</a></li>
<!--					<li><a href="export_package.php?par=power">Export Power package</a></li>-->
					<li><a href="package_master.php">Export Package Master</a></li>
        </ul>
      </li>
      <li><a href="submit_data.php">Get BAT Submit Data</a></li>
      <li><a href="channel_add.php" rel="modal:open">Add new Channel</a></li>
      <li><a href="mod_log.php">Modification logs</a></li>
      <?php
      if ($_SESSION['is_admin'] == 1) {
        echo "<li><a href=\"adduser.php\" class=\"text-info\" rel=\"modal:open\">Add User</a></li>";
        echo "<li><a href=\"viewmodifyuser.php\" class=\"text-info\" >View/Modify User</a></li>";
      }
      ?>
    </ul>

    <ul class="nav navbar-nav navbar-right">
			<li><a href="city_select.php"><strong><span class="glyphicon glyphicon-send"></span> <?php echo $_SESSION['city'] ?></strong></a></li>
      <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
    </ul>
  </div>
</nav>

<div class="container-fluid">

<!--initializing the display variable to print the table on webpage -->
<table cellpadding="1" align="center"
data-toggle="table"
data-search="true"
data-id-table="Table"
class="table table-striped">
<thead>
<tr>
	<th>Username</th>
	<th>First name</th>
	<th>Last Name</th>
	<th>Email</th>
	<th>Administrator</th>
    <th>Active</th>
    <th>Edit Details</th>
	<th>Assign City</th>
	<th>Delete</th>
</tr>
</thead>
<tbody>
<tr>
<?php
$display = '';
//sending the results to an array and printing
while($row = mysqli_fetch_assoc($res_submit)) {
    ($row['is_admin'] == 1) ? $is_admin = "YES" : $is_admin = "";
    ($row['is_active'] == 1) ? $is_active = "YES" : $is_active = "";
$display .="
	<td>".$row['user']."</td>
	<td>".$row['first_name']."</td>
	<td>".$row['last_name']."</td>
    <td>".$row['email']."</td>
    <td>".$is_admin."</td>
    <td>".$is_active."</td>
    <td><a href=\"modifyuser.php?user_id=".$row['user_id']."\" data-toggle=\"tooltip\" title=\"Modify User\" rel=\"modal:open\"><span class=\"glyphicon glyphicon-pencil\"></span></a></td>
    <td><a href=\"assigncity.php?user_id=".$row['user_id']."\" data-toggle=\"tooltip\" title=\"Assign Location\" rel=\"modal:open\"><span class=\"glyphicon glyphicon-transfer\"></span></a></td>
    <td><a href=\"user_delete.php?user_id=".$row['user_id']."\" data-toggle=\"tooltip\" title=\"Delete User\" onclick=\"return confirmAction()\"><span class=\"glyphicon glyphicon-remove\" style=\"color:red\"></span></a></td>
	</tr>";
}

print $display;
?>
</tbody>
</table>
</div>

<div class="navbar navbar-inverse navbar-fixed-bottom">
    <footer class="footer">
        <div class="copyright">
            <div class="container">
                <div class="col-md-6">
                    <p class="text-warning">&copy; 2012-<?php echo date("Y"); ?> All Rights with Meghbela Digital</p>
                </div>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
