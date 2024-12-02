<?php
ob_start();
$pageTitle = 'School Form 10'; 
?>
<style type="text/css">
    td, th {
        text-align: center;
        font-weight: bold;
    }


    td:first-child {
        text-align: left; 
        font-weight: normal; 
    }


        th:first-child {
        text-align: left;
  
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Grades Report</h3>
                <div class="card-tools">
            <input 
                type="text" 
                id="searchBox" 
                class="form-control" 
                placeholder="Search subject..." 
                onkeyup="filterSubjects()" 
            />
        </div>
    </div>
    <div class="card-body">
          <table class="table table-bordered table-striped table-head-fixed text-nowrap table-hover" id="subjectsTable">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>1st Grading</th>
                    <th>2nd Grading</th>
                    <th>3rd Grading</th>
                    <th>4th Grading</th>
                    <th>General Average</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($subject['first_grading'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($subject['second_grading'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($subject['third_grading'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($subject['fourth_grading'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($subject['general_average'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($subject['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>




<script>
    function filterSubjects() {
        const input = document.getElementById('searchBox');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('subjectsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
            const subjectCell = rows[i].getElementsByTagName('td')[0];
            if (subjectCell) {
                const subjectText = subjectCell.textContent || subjectCell.innerText;
                if (subjectText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    }
</script>









<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>