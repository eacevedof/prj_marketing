<?php
/**
 * @var \App\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="#">
  <title><?=$pagetitle?></title>

  <!-- Favicon -->
  <link rel="icon" href="#" type="image/x-icon"/>

  <!-- Bootstrap css -->
  <link href="/themes/valex/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!--  Right-sidemenu css -->
  <link href="/themes/valex/assets/plugins/sidebar/sidebar.css" rel="stylesheet">

  <!--  Custom Scroll bar-->
  <link href="/themes/valex/assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet"/>
  <!--- Style css --->
  <link href="/themes/valex/assets/css/style.css" rel="stylesheet">
  <link href="/themes/valex/assets/css/boxed.css" rel="stylesheet">
  <link href="/themes/valex/assets/css/dark-boxed.css" rel="stylesheet">
  <!--- Dark-mode css --->
  <link href="/themes/valex/assets/css/style-dark.css" rel="stylesheet">
  <!---Skinmodes css-->
  <link href="/themes/valex/assets/css/skin-modes.css" rel="stylesheet" />
  <!--- Animations css-->
  <link href="/themes/valex/assets/css/animate.css" rel="stylesheet">

</head>
<body class="error-page1 main-body bg-light text-dark">

<!-- Page -->
<div class="page">
  <div class="col-md-6 col-lg-6 col-xl-5">
    <div class="login d-flex align-items-center py-2">
      <!-- Demo content-->
      <div class="main-signup-header">
        <h2>Welcome back!</h2>
        <h5 class="fw-semibold mb-4">Please sign in to continue.</h5>
        <form action="#">
          <div class="form-group">
            <label>Email</label> <input class="form-control" placeholder="Enter your email" type="text">
          </div>
          <div class="form-group">
            <label>Password</label> <input class="form-control" placeholder="Enter your password" type="password">
          </div>
          <button class="btn btn-main-primary btn-block">Sign In</button>
        </form>
      </div>
    </div>
  </div><!-- End -->
</div>
<!-- End Page -->
</body>
</html>