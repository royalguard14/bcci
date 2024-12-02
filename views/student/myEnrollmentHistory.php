<?php
ob_start();
$pageTitle = 'Enrollment History'; 
?>
<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Enrollment Log
        </h3>


      </div>
      <div class="card-body">
        <table id="example2" class="table table-bordered table-striped table-head-fixed text-nowrap table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Grade</th>
              <th>Section</th>
              <th>Adviser</th>
              <th>School Year</th>
              <th>Enrolled Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myEHistory as $index => $data): ?>
              <tr>
                <td><?php echo $index + 1; ?></td>
                
                <td><?php echo htmlspecialchars($data['level']); ?></td>
                <td><?php echo htmlspecialchars($data['section_name']); ?></td>
                <td><?php echo htmlspecialchars($data['adviser']); ?></td>
                <td><?php echo htmlspecialchars($data['sy']); ?></td>
                <td><?php echo date('F d, Y', strtotime($data['date'])); ?></td>
    
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
     </div>
   </div>
 </section>
</div>











<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>