<?php 
// Get the current page from the URL
$current_page = basename($_SERVER['REQUEST_URI'], ".php");
?>

<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
    
    <!-- Dashboard Item -->
    <li class="nav-item">
      <a href="dashboard" class="nav-link <?= ($current_page == 'dashboard') ? 'active' : ''; ?>">
        <img class="icon-white" src="assets/img/icons/dashboard.png" alt="Custom Icon" >
        <p>Dashboard</p>
      </a>
    </li>





 


    <!-- Check if the user is an admin -->
    <?php if ($_SESSION['role_id'] === 1): ?>

      <li class="nav-header">School Setting</li>
      <li class="nav-item <?= ($current_page == 'campus-profile' || $current_page == 'campus-grades' || $current_page == 'campus-sections' || $current_page == 'campus-subjects') ? 'menu-is-opening menu-open' : ''; ?>">
        <a href="#" class="nav-link <?= ($current_page == 'campus-profile' || $current_page == 'campus-grades' || $current_page == 'campus-sections' || $current_page == 'campus-subjects') ? 'active' : ''; ?>">
          <img class="icon-white" src="assets/img/icons/campus.png" alt="Custom Icon" >
          <p>
            Campus
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="campus-profile" class="nav-link <?= ($current_page == 'campus-profile') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Profile</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="campus-grades" class="nav-link <?= ($current_page == 'campus-grades') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Grade</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="campus-sections" class="nav-link <?= ($current_page == 'campus-sections') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Section</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="campus-subjects" class="nav-link <?= ($current_page == 'campus-subjects') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Subject</p>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-header">Administrative Setting</li>
      <li class="nav-item <?= ($current_page == 'roles' || $current_page == 'permissions' || $current_page == 'accounts') ? 'menu-is-opening menu-open' : ''; ?>">
        <a href="#" class="nav-link <?= ($current_page == 'roles' || $current_page == 'permissions' || $current_page == 'accounts') ? 'active' : ''; ?>">
          <img class="icon-white" src="assets/img/icons/gears.png" alt="Custom Icon" >
          <p>
            Developer
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="roles" class="nav-link <?= ($current_page == 'roles') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Roles</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="permissions" class="nav-link <?= ($current_page == 'permissions') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Permissions</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="accounts" class="nav-link <?= ($current_page == 'accounts') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Accounts</p>
            </a>
          </li>
        </ul>
      </li>
    <?php endif; ?>

  </ul>
</nav>
<!-- /.sidebar-menu -->
