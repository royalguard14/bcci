<?php
ob_start();
$pageTitle = 'Attendance Record'; 
?>
<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Attendance Log
        </h3>

        <div class="card-tools">
                  <div style="margin-bottom: 10px;">
          <input 
              type="text" 
              id="searchInput" 
              class="form-control" 
              placeholder="Search..."
              onkeyup="filterTable()" 
              style="width: 100%; max-width: 300px;"
          >
        </div>
        </div>
      </div>
      <div class="card-body">
        <table id="example2" class="table table-bordered table-striped table-head-fixed text-nowrap table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Grade</th>
              <th>Section</th>
              <th>School Year</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myAttendance as $index => $data): ?>
              <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo date('F d, Y', strtotime($data['date'])); ?></td>
                <td><?php echo htmlspecialchars($data['level']); ?></td>
                <td><?php echo htmlspecialchars($data['section_name']); ?></td>
                <td><?php echo htmlspecialchars($data['sy']); ?></td>
                <td>
                  <?php 
                  $statusMap = [
                    'P' => 'Present',
                    'A' => 'Absent',
                    'E' => 'Excuse',
                    'T' => 'Tardy'
                  ];
                  if ($data['status'] == 'E') {
                    echo $statusMap[$data['status']] . ' - '. htmlspecialchars($data['remarks']) ?? 'Unknown'; 
                  } else {
                    echo $statusMap[$data['status']] ?? 'Unknown'; 
                  }
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
     </div>
   </div>
 </section>
</div>


<script>
    function filterTable() {
        const searchInput = document.getElementById("searchInput").value.toLowerCase();
        const table = document.getElementById("example2");
        const rows = table.getElementsByTagName("tr");

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];

            // Skip rows in the thead
            if (row.closest("thead")) {
                continue;
            }

            const cells = row.getElementsByTagName("td");
            let rowText = "";

            // Combine text content of all cells in the row
            for (let j = 0; j < cells.length; j++) {
                rowText += cells[j].textContent.toLowerCase();
            }

            // Show or hide row based on search
            row.style.display = rowText.includes(searchInput) ? "" : "none";
        }
    }
</script>
<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>