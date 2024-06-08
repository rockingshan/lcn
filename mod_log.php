<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php
session_start();

if (!isset($_SESSION['user'])) {
	header("location:index.php");
}

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

<script src="lib/login.js"></script>

<script src="lib/bootstrap-table.js"></script>

</head>

<body>
  <div class="container">
    <table cellpadding="1" align="center"
    data-toggle="table"
    data-search="true"
    class="table table-striped">
    <tbody>
<?php
$file = file("./log/".$_SESSION['city'].".txt");
$file = array_reverse($file);
foreach($file as $f){
    echo "<tr><td>".$f."</td></tr>";
}
// $f = fopen($file, "r") or exit("Unable to open file!");
// // read file line by line until the end of file (feof)
// while(!feof($f))
// {
//   echo "<tr><td>".fgets($f)."</td></tr>";
// }
//
// fclose($f);
 ?>
</tbody>
</table>
</div>


</body>
</html>
