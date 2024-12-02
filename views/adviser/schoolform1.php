<?php
ob_start();
$pageTitle = 'School Form 1 - School Register'; 
?>

<style type="text/css">
  #example2 {
    border-collapse: collapse;
    width: 100%;
}

#example2 thead th {
    position: sticky;
    top: 0;
    background-color: #f9f9f9;
    z-index: 1;
}

</style>

<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
  

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

<div style="overflow-y: auto; max-height: 500px; border: 1px solid #ddd;">
    <!-- <table  class="table table-bordered table-hover"> -->
         <table class="table table-head-fixed text-nowrap" id="example2">
        <thead>
            <tr>
                <th colspan="9"></th>
                <th colspan="4" style="text-align: center;">ADDRESS</th>
                <th colspan="2" style="text-align: center;">PARENTS</th>
                <th colspan="2" style="text-align: center;">Guardian</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>No.</th>
                <th>LRN</th>
                <th>Full Name</th>
                <th>Sex</th>
                <th>Date of Birth</th>
                <th>Age</th>
                <th>Mother Tongue</th>
                <th>Ethnic Group</th>
                <th>Religion</th>
                <th>House #/ Street/ Sitio/ Purok</th>
                <th>Barangay</th>
                <th>Municipality/ City</th>
                <th>Province</th>
                <th>Father's Name (Last Name, First Name, Middle Name)</th>
                <th>Mother's Maiden Name (Last Name, First Name, Middle Name)</th>
                <th>Name</th>
                <th>Relationship</th>
                <th>Contact No.</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($advisoryClass as $index => $student) { ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $student['lrn']; ?></td>
                    <td><?php echo $student['full_name']; ?></td>
                    <td><?php echo $student['sex']; ?></td>
                    <td><?php echo $student['birth_date']; ?></td>
                    <td><?php echo $student['age']; ?></td>
                    <td><?php echo $student['mother_tongue']; ?></td>
                    <td><?php echo $student['ethnic_group']; ?></td>
                    <td><?php echo $student['religion']; ?></td>
                    <td><?php echo $student['house_street_sitio_purok']; ?></td>
                    <td><?php echo $student['barangay']; ?></td>
                    <td><?php echo $student['municipality_city']; ?></td>
                    <td><?php echo $student['province']; ?></td>
                    <td><?php echo $student['fathers_name']; ?></td>
                    <td><?php echo $student['mother_name']; ?></td>
                    <td><?php echo $student['guardian_name']; ?></td>
                    <td><?php echo $student['relationship']; ?></td>
                    <td><?php echo $student['contact_number']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

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
include 'views/master.php';
?>