<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
session_start();

if (!isset($_SESSION['user'])) {
	header("location:index.php");	
}
?>
<!-- Latest compiled and minified CSS of Bootstrap CSS-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>

<body>
<div align="center" width="800px">
<form class="form-horizontal" method="post" action="secure_page.php">
<fieldset>

<!-- Form Name -->
<legend>Select City</legend>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Select City here</label>
  <div class="col-md-5">
    <select id="selectdb" name="selectdb" class="form-control">
      <option value="db_1">Kolkata</option>
      <option value="db_2">Berhampore</option>
      <option value="db_3">Haldia</option>
      <option value="db_4">Bankura</option>
    </select>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Go</button>
  </div>
</div>

</fieldset>
</form>
</div>
</body>
</html>