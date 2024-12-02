<?php
ob_start();
$pageTitle = 'Dashboard'; 
?>
<div class="row">
  <!-- Left Column -->
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Dashboard
        </h3>
      </div>
      <div class="card-body">
        <!-- Dynamic content placeholder for the left column -->
        <p>Welcome to your dashboard!</p>
      </div>
    </div>
  </section>

  <!-- Right Column -->
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Sales
        </h3>
      </div>
      <div class="card-body">
        <!-- Direct Chat Section -->

          <div class="card-body">
            

          </div>

          <!-- Chat Input -->
          <div class="card-footer">

          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>