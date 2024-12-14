<?php 
// Get the current page from the URL
$current_page = basename($_SERVER['REQUEST_URI'], ".php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Butuan City College, Inc</title>
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style type="text/css">
    body {
      background-color: maroon;
      color: white;
    }

    .navbar {
      background-color: maroon !important;
    }

    .navbar-brand, .navbar-nav .nav-link {
      color: yellow !important;
    }

   .navbar-nav .nav-link.active {
  color: black !important;
  background-color: yellow !important;
  border-radius: 15px !important; /* Adjust the value for the desired roundness */
  padding: 5px 15px; /* Adjust padding to ensure the rounded corners look good */
}


    .navbar-nav .nav-item:hover .nav-link {
      background-color: yellow !important;
      color: maroon !important;
       border-radius: 15px !important; /* Adjust the value for the desired roundness */
  padding: 5px 15px;
    }

    .content-wrapper {
      background-color: #f4f6f9;
      color: maroon;
    }

    .content-header h1 {
      color: maroon;
    }

    .card-header {
      background-color: yellow;
      color: maroon;
    }

    .card-body {
      background-color: white;
      color: maroon;
    }

    .footer {
      background-color: maroon;
      color: yellow;
    }

    .btn-primary {
      background-color: maroon;
      border-color: maroon;
    }

    .btn-primary:hover {
      background-color: yellow;
      border-color: yellow;
      color: maroon;
    }

    .control-sidebar-dark {
      background-color: maroon;
      color: yellow;
    }

    .drawer {
      position: fixed;
      right: 0;
      top: 0;
      height: 100%;
      width: 300px;
      background: #fff;
      box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
      transform: translateX(100%);
      transition: transform 0.3s;
      padding: 1rem !important;
      z-index: 9999;
    }

    .drawer.open {
      transform: translateX(0);
    }

    .chat-windows {
      position: fixed;
      bottom: 0;
      right: 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
      z-index: 9998;
    }

    .direct-chat {
      width: 100%;
      background: #fff;
      border: 1px solid #ddd;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }

    .input-group input {
      border-radius: 20px;
    }

    .input-group-append button {
      border-radius: 20px;
      background-color: maroon;
      color: white;
    }

  </style>

</head>
<body class="hold-transition layout-top-nav layout-footer-fixed">

  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
      <div class="container">
        <a href="assets/index3.html" class="navbar-brand">
          <img src="assets/butuan-city-collges-logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Butuan City College, Inc</span>
        </a>
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
          <!-- Left navbar links -->
          <ul class="navbar-nav">
          </ul>
        </div>
        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <!-- Home Link -->
          <li class="nav-item">
            <a href="home" class="nav-link <?= ($current_page == 'home') ? 'active' : ''; ?>">Home</a>
          </li>

          <!-- Contact Us Link -->
          <li class="nav-item">
            <a href="contact" class="nav-link <?= ($current_page == 'contact') ? 'active' : ''; ?>">Contact Us</a>
          </li>

          <?php if (!isset($_SESSION['log_in'])): ?>
          <!-- Guest Links -->
          <li class="nav-item">
            <a href="register" class="nav-link <?= ($current_page == 'register') ? 'active' : ''; ?>">Enroll Now!</a>
          </li>
          <li class="nav-item">
            <a href="login" class="nav-link <?= ($current_page == 'login') ? 'active' : ''; ?>">Login</a>
          </li>
          <?php else: ?>
          <?php if ($this->acads_report <= 0): ?>
          <!-- Academic Setup for Logged-in Users Without Records -->
          <li class="nav-item">
            <a href="acad_setting" class="nav-link <?= ($current_page == 'acad_setting') ? 'active' : ''; ?>">Academic Setup</a>
          </li>
          <?php else: ?>
          <?php if ($this->myEnrollmentStatus <= 0 && $this->campusDataEnrollmentStatus == 1): ?>
          <li class="nav-item">
            <a href="addsubject" class="nav-link <?= ($current_page == 'addsubject') ? 'active' : ''; ?>">Choose Subject</a>
          </li>
          <?php endif; ?>
          <li class="nav-item">
            <a href="profile" class="nav-link <?= ($current_page == 'profile') ? 'active' : ''; ?>">My Profile</a>
          </li>
          <?php endif; ?>

          <!-- Logout Link -->
          <li class="nav-item">
            <a href="logout" class="nav-link <?= ($current_page == 'logout') ? 'active' : ''; ?>">Logout</a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><?php echo isset($pageTitle) ? $pageTitle : 'Management'; ?></h1>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <div class="content">
        <div class="container">
          <?php echo isset($content) ? $content : "<div class='alert alert-danger'>No content available.</div>"; ?>
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
        Anything you want
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>
  </div><!-- ./wrapper -->

  <!-- jQuery -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/js/widget.js"></script>
  <script src="assets/js/qrcode.js"></script>
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>

  <!-- ChartJS -->
  <script src="assets/plugins/chart.js/Chart.min.js"></script>

</body>
</html>
