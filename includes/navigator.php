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


<?php if ($_SESSION['role_id'] === 2): ?>
       <li class="nav-item">
            <a href="pending_student" class="nav-link <?= ($current_page == 'pending_student') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>
                New Registered
                <span class="badge badge-info right">2</span>
              </p>
            </a>
          </li>

<script>
$(document).ready(function() {
    // Function to fetch and update the count
    function updatePendingStudentCount() {
        $.ajax({
            url: 'pending_student_count', // Replace with the correct endpoint for your AJAX handler
            method: 'GET', // You can also use POST if needed
            dataType: 'json',
            success: function(response) {
                const badge = $('span.badge-info.right');
                
                if (response.count === 0) {
                    badge.hide(); // Hide the badge if the count is 0
                } else {
                    badge.text(response.count).show(); // Update and show the badge if the count is not 0
                }
            },
            error: function() {
                console.error('Failed to fetch pending student count.');
            }
        });
    }

    // Call the function every second
    setInterval(updatePendingStudentCount, 1000);

    // Optionally, call the function immediately when the page loads
    updatePendingStudentCount();
});

</script>

  <?php endif; ?>


    <!-- Check if the user is an admin -->
    <?php if ($_SESSION['role_id'] === 1): ?>

      <li class="nav-header">School Setting</li>
      <li class="nav-item <?= ($current_page == 'campus-profile' || $current_page == 'campus-department' || $current_page == 'campus-sections' || $current_page == 'campus-subjects') ? 'menu-is-opening menu-open' : ''; ?>">
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
            <a href="campus-department" class="nav-link <?= ($current_page == 'campus-department') ? 'active' : ''; ?>">
              <i class="far fa-circle nav-icon"></i>
              <p>Department</p>
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
