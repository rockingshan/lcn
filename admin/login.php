<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">  
	    <title>LCN Admin - Login</title>
        <meta name="description" content="">
        <meta name="author" content="templatemo">
        <!-- 
        LCN Admin Template
        https://templatemo.com/tm-455-visual-admin
        -->
	    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,700' rel='stylesheet' type='text/css'>
	    <link href="css/font-awesome.min.css" rel="stylesheet">
	    <link href="css/bootstrap.min.css" rel="stylesheet">
	    <link href="css/templatemo-style.css" rel="stylesheet">
	    
	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
	</head>
	<body class="light-gray-bg">
		<div class="templatemo-content-widget templatemo-login-widget white-bg">
			<header class="text-center">
	          <div class="square"></div>
	          <h1>LCN Admin</h1>
	        </header>
	        <form action="submit.php" method="post" class="templatemo-login-form">
	        	<div class="form-group">
	        		<div class="input-group">
		        		<div class="input-group-addon"><i class="fa fa-user fa-fw"></i></div>	        		
		              	<input type="text" class="form-control" name="user" placeholder="username">           
		          	</div>	
	        	</div>
	        	<div class="form-group">
	        		<div class="input-group">
		        		<div class="input-group-addon"><i class="fa fa-key fa-fw"></i></div>	        		
		              	<input type="password" class="form-control" name="pass" placeholder="******">           
		          	</div>	
	        	</div>	          	
	          	<div class="invalid-feedback">
				  <div id=”errMsg” class="invalid-feedback">
				  <?php if(isset($_SESSION['errMsg']) && !empty($_SESSION['errMsg'])) { 
					  echo $_SESSION['errMsg']; 
					  unset($_SESSION['errMsg']);
					  } ?>
				</div>
							    				    
				</div>
				<div class="form-group">
					<button type="submit" class="templatemo-blue-button width-100">Login</button>
				</div>
	        </form>
		</div>
		
	</body>
</html>