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
   <link rel="stylesheet" href="assets/css/bccicolor.css">
  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <script src="assets/plugins/sweetalert2-theme-bootstrap-4/sw.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

 
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


          <?php if (!isset($_SESSION['log_in'])): ?>
                      <!-- Home Link -->
          <li class="nav-item">
            <a href="home" class="nav-link <?= ($current_page == 'home') ? 'active' : ''; ?>">Home</a>
          </li>

          <!-- Contact Us Link -->
          <li class="nav-item">
            <a href="contact" class="nav-link <?= ($current_page == 'contact') ? 'active' : ''; ?>">Contact Us</a>
          </li>
          <!-- Guest Links -->
          <li class="nav-item">
            <a href="register" class="nav-link <?= ($current_page == 'register') ? 'active' : ''; ?>">Enroll Now!</a>
          </li>
          <li class="nav-item">
            <a href="login" class="nav-link <?= ($current_page == 'login') ? 'active' : ''; ?>">Login</a>
          </li>
          <?php else: ?>
          <?php if ($this->acads_report <= 0 &&  $this->myRoleID == 4): ?>
          <!-- Academic Setup for Logged-in Users Without Records -->
          <li class="nav-item">
            <a href="acad_setting" class="nav-link <?= ($current_page == 'acad_setting') ? 'active' : ''; ?>">Academic Setup</a>
          </li>
          <?php else: ?>
          <?php if ($this->myEnrollmentStatus <= 0 && $this->campusDataEnrollmentStatus == 1 && $this->myRoleID == 4): ?>
          <li class="nav-item">
            <a href="addsubject" class="nav-link <?= ($current_page == 'addsubject') ? 'active' : ''; ?>">Choose Subject</a>
          </li>
          <?php endif; ?>
           <?php if ($this->myRoleID == 4): ?>
          <li class="nav-item">
            <a href="profile" class="nav-link <?= ($current_page == 'profile') ? 'active' : ''; ?>">My Profile</a>
          </li>

                   <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Records</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="enrollment-history" class="dropdown-item">Enrollment History </a></li>
              <li class="dropdown-divider"></li>

              <li><a href="academic-record" class="dropdown-item">Academic Records</a></li>
              <li class="dropdown-divider"></li>
               <li><a href="academic-payment" class="dropdown-item">Payment</a></li>
         
            </ul>

          </li>

        <li class="nav-item">
            <a href="documents" class="nav-link <?= ($current_page == 'documents') ? 'active' : ''; ?>">Documents</a>
          </li>


  <?php endif; ?>
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
<!-- jQuery and jQuery UI -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE App -->
<script src="assets/js/adminlte.js"></script>

<!-- ChartJS -->
<script src="assets/plugins/chart.js/Chart.min.js"></script>

<!-- Sparkline -->
<script src="assets/plugins/sparklines/sparkline.js"></script>

<!-- JQVMap -->
<script src="assets/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

<!-- jQuery Knob Chart -->
<script src="assets/plugins/jquery-knob/jquery.knob.min.js"></script>

<!-- daterangepicker -->
<script src="assets/plugins/moment/moment.min.js"></script>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Summernote -->
<script src="assets/plugins/summernote/summernote-bs4.min.js"></script>

<!-- overlayScrollbars -->
<script src="assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- SweetAlert2 -->
<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="assets/plugins/jszip/jszip.min.js"></script>
<script src="assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- Widget and QRCode -->
<script src="assets/js/widget.js"></script>
<script src="assets/js/qrcode.js"></script>

<!-- Initialize DataTables -->
<script type="text/javascript">
  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });

  $('#example3').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });
</script>

</body>
</html>
