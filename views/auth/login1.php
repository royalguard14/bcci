<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in (v2)</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/css/adminlte.min.css">
  
  <style>
    body {
      background-color: maroon;
    }
    .login-box {
      width: 400px;
    }
    .card {
      background-color: yellow;
    }
    .card-header a {
      color: maroon;
      font-weight: bold;
    }
    .card-body {
      color: maroon;
    }
    .form-control {
      border-color: maroon;
      color: maroon;
    }
    .form-control:focus {
      border-color: yellow;
      box-shadow: 0 0 0 0.2rem rgba(255, 255, 0, 0.25);
    }
    .input-group-text {
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
    .login-box-msg {
      font-weight: bold;
      font-size: 16px;
    }
  </style>
</head>
<body class="hold-transition login-page" style="background-color:maroon">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="assets/index2.html" class="h1"><b>BCCI</b></a>
    </div>
    <div class="card-body">

     <?php if (!empty($_SESSION['login_error'])): ?>
      <p class="login-box-msg text-danger"><?php echo htmlspecialchars($_SESSION['login_error']); ?></p>
      <?php unset($_SESSION['login_error']); ?>
     <?php endif; ?>

      <form action="login/submit" method="POST">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Email / USN" name="username" autocomplete="new-username" >
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" autocomplete="new-password" >
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/js/adminlte.min.js"></script>
</body>
</html>
