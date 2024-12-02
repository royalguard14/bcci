<?php
ob_start();
$pageTitle = 'Roles Management'; 
?>
<div class="row">
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          

          Sales
        </h3>
      </div>
      <div class="card-body">
        content
      </div>
    </div>
  </section>
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          

          Sales
        </h3>
      </div>
      <div class="card-body">
        content
      </div>
    </div>
  </section>
</div>
<?php
$content = ob_get_clean();
include 'views/master.php';
?>