<?php
session_start();
if (isset($_SESSION['user'])) {
  if (isset($_SESSION['is_admin'])) {
    if ($_SESSION['is_admin'] == 0) {
      header("Location: login.php");
    }
  }
  
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>LCN Admin Dashboard - Manage Users</title>
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
  <body>  
    <!-- Left column -->
    <div class="templatemo-flex-row">
      <div class="templatemo-sidebar">
        <header class="templatemo-site-header">
          <div class="square"></div>
          <h1>LCN Admin</h1>
        </header>
        <div class="profile-photo-container">
          <img src="images/profile-photo.jpg" alt="Profile Photo" class="img-responsive">  
          <div class="profile-photo-overlay"></div>
        </div>      
        <!-- Search box -->
        <form class="templatemo-search-form" role="search">
          <div class="input-group">
              <button type="submit" class="fa fa-search"></button>
              <input type="text" class="form-control" placeholder="Search" name="srch-term" id="srch-term">           
          </div>
        </form>
        <div class="mobile-menu-icon">
            <i class="fa fa-bars"></i>
          </div>
        <nav class="templatemo-left-nav">          
          <ul>
            <!-- <li><a href="index.php"><i class="fa fa-home fa-fw"></i>Dashboard</a></li> -->

<!--             <li><a href="data-visualization.html"><i class="fa fa-database fa-fw"></i>Data Visualization</a></li>
            <li><a href="maps.html"><i class="fa fa-map-marker fa-fw"></i>Maps</a></li> -->
            <li><a href="#" class="active"><i class="fa fa-users fa-fw"></i>Manage Users</a></li>
            <li><a href="preferences.html"><i class="fa fa-sliders fa-fw"></i>Preferences</a></li>
            <li><a href="data-visualization.html"><i class="fa fa-bar-chart fa-fw"></i>Charts</a></li>
            <li><a href="logout.php"><i class="fa fa-eject fa-fw"></i>Sign Out</a></li>
          </ul>  
        </nav>
      </div>
      <!-- Main content --> 
      <div class="templatemo-content col-1 light-gray-bg">
       <!--  <div class="templatemo-top-nav-container">
          <div class="row">
            <nav class="templatemo-top-nav col-lg-12 col-md-12">
              <ul class="text-uppercase">
                <li><a href="" class="active">Admin panel</a></li>
                <li><a href="">Dashboard</a></li>
                <li><a href="">Overview</a></li>
                <li><a href="login.php">Sign in form</a></li>
              </ul>  
            </nav> 
          </div>
        </div> -->
        <div class="templatemo-content-container">
          <div class="templatemo-content-widget no-padding">
            <div class="panel panel-default table-responsive">
              <table class="table table-striped table-bordered templatemo-user-table">
                <thead>
                  <tr>
                    <td><a href="" class="white-text templatemo-sort-by"># <span class="caret"></span></a></td>
                    <td><a href="" class="white-text templatemo-sort-by">First Name <span class="caret"></span></a></td>
                    <td><a href="" class="white-text templatemo-sort-by">Last Name <span class="caret"></span></a></td>
                    <td><a href="" class="white-text templatemo-sort-by">User Name <span class="caret"></span></a></td>
                    <td><a href="" class="white-text templatemo-sort-by">Email <span class="caret"></span></a></td>
                    <td>Edit</td>
                    <td>Action</td>
                    <td>Delete</td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1.</td>
                    <td>John</td>
                    <td>Smith</td>
                    <td>@jS</td>
                    <td>js@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>
                  <tr>
                    <td>2.</td>
                    <td>Bill</td>
                    <td>Jones</td>
                    <td>@bJ</td>
                    <td>bj@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>
                  <tr>
                    <td>3.</td>
                    <td>Mary</td>
                    <td>James</td>
                    <td>@mJ</td>
                    <td>mj@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>
                  <tr>
                    <td>4.</td>
                    <td>Steve</td>
                    <td>Bride</td>
                    <td>@sB</td>
                    <td>sb@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>
                  <tr>
                    <td>5.</td>
                    <td>Paul</td>
                    <td>Richard</td>
                    <td>@pR</td>
                    <td>pr@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>  
                   <tr>
                    <td>6.</td>
                    <td>Will</td>
                    <td>Brad</td>
                    <td>@wb</td>
                    <td>wb@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>  
                   <tr>
                    <td>7.</td>
                    <td>Steven</td>
                    <td>Eric</td>
                    <td>@sE</td>
                    <td>se@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>  
                   <tr>
                    <td>8.</td>
                    <td>Landi</td>
                    <td>Susan</td>
                    <td>@lS</td>
                    <td>ls@company.com</td>
                    <td><a href="" class="templatemo-edit-btn">Edit</a></td>
                    <td><a href="" class="templatemo-link">Action</a></td>
                    <td><a href="" class="templatemo-link">Delete</a></td>
                  </tr>                    
                </tbody>
              </table>    
            </div>                          
          </div>          
         
          <footer class="text-right">
            <p>Copyright &copy; 2084 Company Name 
            | Design: Template Mo</p>
          </footer>         
        </div>
      </div>
    </div>
    
    <!-- JS -->
    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>      <!-- jQuery -->
    <script type="text/javascript" src="js/templatemo-script.js"></script>      <!-- Templatemo Script -->
    <script>
      $(document).ready(function(){
        // Content widget with background image
        var imageUrl = $('img.content-bg-img').attr('src');
        $('.templatemo-content-img-bg').css('background-image', 'url(' + imageUrl + ')');
        $('img.content-bg-img').hide();        
      });
    </script>
  </body>
</html>