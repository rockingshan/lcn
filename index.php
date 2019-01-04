<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
//starting the connection to db
include("include/connect.php");
mysqli_select_db($con,'meghbela_lcn_db_kol') or die("No database");
//making the search in db
$sql = "SELECT * FROM channel_tb,lcn_tb,package_tb,sid_tb WHERE channel_tb.lcn=lcn_tb.lcn AND channel_tb.sid=package_tb.sid AND package_tb.sid=sid_tb.sid ORDER BY lcn_tb.lcn";

$result = mysqli_query($con,$sql);
if (!$result) { // add this check.
    die('Invalid query: ' . mysqli_error());
}
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
<link rel="manifest" href="/images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="style/login.css">
<!-- <link rel="stylesheet" type="text/css" href="style/main.css" />
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato"  /> -->

<!---  Start New styling -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

<link rel="stylesheet" href="lib/bootstrap-table.css">

<script src="lib/login.js"></script>

<script src="lib/bootstrap-table.js"></script>
<script src="lib/bootstrap-table-toolbar.js"></script>



<title>Meghbela Digital LCN</title>
</head>

<body>

  <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Meghbela LCN</a>
    </div>
    <ul class="nav navbar-nav">
  <li><a href="export.php"><span class="glyphicon glyphicon-save"></span>Download LCN</a></li>
  <li><a href="package_master.php"><span class="glyphicon glyphicon-floppy-save"></span>Export Package Master</a></li>
</ul>
<ul class="nav navbar-nav navbar-right">
     <li><a href="#login-modal" data-toggle="modal" data-target="#login-modal"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
    </ul>
  </div>
</nav>
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    	  <div class="modal-dialog">
				<div class="loginmodal-container">
					<h1>Login for Advanced Features</h1><br>
				  <form class="form-signin" method="post" id="login-form">
            <div id="error">
        <!-- error will be shown here ! -->
        </div>
					<input type="text" name="user" placeholder="Username">
					<input type="password" name="pass" placeholder="Password">
					<!-- <input type="submit" name="login" class="login loginmodal-submit" value="Login"> -->
          <button type="submit" class="btn btn-default" name="btn-login" id="btn-login">
      <span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In
   </button>
				  </form>

				</div>
			</div>
		  </div>

	<div class="container">

<!--initializing the display variable to print the table on webpage -->
<table cellpadding="1" align="center"
data-toggle="table"
data-search="true"
data-advanced-search="true"
data-id-table="advancedTable"
class="table table-striped">
  <thead>
  <tr>
    <th>GENRE</th>
    <th data-sortable="true">LCN</th>
    <th data-field="sid" data-sortable="true">SID</th>
	<th data-field="freq" data-sortable="true">FREQ</th>
    <th data-field="name">CHANNEL NAME</th>
    <th data-field="brn" data-sortable="true">Bronze Digital </th>
    <th data-field="sil" data-sortable="true"> Silver Digital </th>
    <th data-field="gold" data-sortable="true"> Gold Digital </th>
    <th data-field="pla" data-sortable="true"> Platinum Digital </th>
<!--    <th data-field="pw" data-sortable="true"> Power pack </th>-->
    <th> A La Carte Price(&#8377) </th>
  </tr>
</thead>
<tbody>
   <?php
  $display = '';
  //sending the results to an array and printing
  while($row = mysqli_fetch_array($result)) {
  $display .="<tr>
    <td>".$row['genre']."</td>
    <td>".$row['lcn']."</td>
    <td>".$row['sid']."</td>
	<td>".$row['freq']."</td>
    <td><strong>".$row['channel']."</strong></td>
    <td>".$row['bronze']."</td>
    <td>".$row['silver']."</td>
    <td>".$row['gold']."</td>
    <td>".$row['platinum']."</td>
    <td>".$row['price']."</td>

  </tr>";
  }
  echo $display;
 ?>
 </tbody>
 </table>
</div>

<footer class="footer">
<div class="copyright">
  <div class="container">
    <div class="col-md-6">
      <p>&copy; 2012-<?php echo date("Y"); ?> All Rights with Meghbela Digital</p>
    </div>
  </div>
</div>
</footer>
</body>
</html>
