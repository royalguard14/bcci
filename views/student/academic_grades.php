<?php
ob_start();
$pageTitle = 'Academic Record'; 
function mapGradeToPoint($grade) {
    if ($grade >= 97.50 && $grade <= 100) return 1.00;
    if ($grade >= 94.50 && $grade < 97.50) return 1.25;
    if ($grade >= 91.50 && $grade < 94.50) return 1.50;
    if ($grade >= 88.50 && $grade < 91.50) return 1.75;
    if ($grade >= 85.50 && $grade < 88.50) return 2.00;
    if ($grade >= 82.50 && $grade < 85.50) return 2.25;
    if ($grade >= 79.50 && $grade < 82.50) return 2.50;
    if ($grade >= 76.50 && $grade < 79.50) return 2.75;
    if ($grade >= 74.50 && $grade < 76.50) return 3.00;
    if ($grade < 74.50) return 5.00;
    return 'No Grade';
}
?>
<?php
// Function to display toast messages
function displayToastMessage($session_key, $toast_class, $title) {
    if (isset($_SESSION[$session_key])) {
        $message = $_SESSION[$session_key];
        unset($_SESSION[$session_key]);
        echo "<script type='text/javascript'>
        document.querySelector('.preloader').style.display = 'none';
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: '$toast_class',
                title: '$title',
                autohide: true,
                delay: 3000,
                body: '" . addslashes($message) . "'
                });
                });
                </script>";
            }
        }
// Check if 'error' session is set and call the function
        if (isset($_SESSION['error'])) {
            displayToastMessage('error', 'bg-danger', 'Error');
        }
// Check if 'info' session is set and call the function
        if (isset($_SESSION['info'])) {
            displayToastMessage('info', 'bg-info', 'Information');
        }
// Check if 'success' session is set and call the function
        if (isset($_SESSION['success'])) {
            displayToastMessage('success', 'bg-success', 'Success');
        }
        ?>
        <div class="row">
          <section class="col-lg-12 connectedSortable">
            <div class="card">
              <div class="card-header" style="<?php echo $isQualifiedForGraduation ? 'background-color: green;' : ''; ?>">
                <h3 class="card-title"><strong>
                    <?php if ($isQualifiedForGraduation): ?>
                     You are qualified to graduate!
                 <?php else: ?>
                    You are not yet qualified to graduate. Please review your grades.
                <?php endif; ?>
            </h3></strong>
        </div>
        <div class="card-body">
            <table id="example3" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($subjectGrades)): ?>
                        <?php foreach ($subjectGrades as $index => $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($data['subject_name']); ?></td>
                                <td>
                                    <?php 
                                    if ($data['average_grade'] === 'No grade yet') {
                                        echo $data['average_grade'];
                                    } else {
                        echo number_format(mapGradeToPoint($data['average_grade']), 2); // Format to 2 decimal places
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No Enrollment records found.</td>
        </tr>
    <?php endif; ?>
</tbody>
</table>
</div>
</div>
</section>
</div>
<script type="text/javascript">
    $('#gradetable').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
  });
</script>
<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>