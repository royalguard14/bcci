<?php
ob_start();
$pageTitle = 'Grades Management'; 
?>
<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          CLASS RECORD
        </h3>
      </div>
      <div class="card-body">
        <table border="1" cellpadding="5" cellspacing="0" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Student Name</th>
              <?php foreach ($allSubjectInGrade as $subject): ?>
                <th><?= htmlspecialchars($subject['subject_name']) ?></th>
              <?php endforeach; ?>
              <th>Average</th> <!-- Add this column for the average -->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($advisoryClass as $student): ?>
              <tr>
                <td><?= htmlspecialchars($student['fullname']) ?></td>
                <?php foreach ($allSubjectInGrade as $subject): ?>
                  <td>
                    <input 
                      type="number" 
                      name="grades[<?= $student['profile_id'] ?>][<?= $subject['subject_id'] ?>]" 
                      data-user-id="<?= $student['profile_id'] ?>"
                      data-subject-id="<?= $subject['subject_id'] ?>"
                      data-eh-id="<?= $student['eh_id'] ?>" 
                      step="0.01" 
                      value="<?= isset($gradeMap[$student['profile_id']][$subject['subject_id']]) 
                          ? htmlspecialchars($gradeMap[$student['profile_id']][$subject['subject_id']]) 
                          : '00.00' ?>"
                      placeholder="00.00"
                      class="form-control"
                    >
                  </td>
                <?php endforeach; ?>
                <td style="text-align: center;font-weight: bold;" class="average" data-user-id="<?= $student['profile_id'] ?>">00.00</td> 
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>


<script>
$(document).ready(function() {
    // Listen for changes in the grade input fields, whether typed or using increment/decrement
    $('input[type="number"]').on('input', function() {
        var userId = $(this).data('user-id');
        var subjectId = $(this).data('subject-id');
        var grade = $(this).val();
        var ehId = $(this).data('eh-id'); // Ensure this data attribute is added to the input for eh_id
        
        // Validate grade format
        if (isNaN(grade) || grade < 0 || grade > 100) {
            alert('Please enter a valid grade between 0 and 100');
            return;
        }

        // Send the updated grade via AJAX
        $.ajax({
            url: 'class-record-update-add', 
            type: 'POST',
            data: {
                user_id: userId,
                subject_id: subjectId,
                grade: grade,
                eh_id: ehId
            },
            success: function(response) {
                console.log('Grade updated successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error updating grade: ' + error);
            }
        });
    });
});
</script>


<script type="text/javascript">
    
$(document).ready(function() {
    // Function to calculate and update the average for a student
    function calculateAverage(userId) {
        var totalGrades = 0;
        var subjectCount = 0;
        
        // Loop through each grade input for this student and calculate the total sum
        $('input[data-user-id="' + userId + '"]').each(function() {
            var grade = parseFloat($(this).val());
            if (!isNaN(grade)) {
                totalGrades += grade;
                subjectCount++;
            }
        });

        // Calculate the average
        var average = (subjectCount > 0) ? totalGrades / subjectCount : 0;

        // Update the average in the table
        $('td[data-user-id="' + userId + '"].average').text(average.toFixed(2));
    }

    // Listen for changes in the grade input fields
    $('input[type="number"]').on('input', function() {
        var userId = $(this).data('user-id');
        var subjectId = $(this).data('subject-id');
        var grade = $(this).val();
        var ehId = $(this).data('eh-id'); // Ensure this data attribute is added to the input for eh_id
        
        // Validate grade format
        if (isNaN(grade) || grade < 0 || grade > 100) {
            alert('Please enter a valid grade between 0 and 100');
            return;
        }

        // Send the updated grade via AJAX
        $.ajax({
            url: 'class-record-update-add', 
            type: 'POST',
            data: {
                user_id: userId,
                subject_id: subjectId,
                grade: grade,
                eh_id: ehId
            },
            success: function(response) {
                console.log('Grade updated successfully');
                // After the grade is updated, recalculate the average
                calculateAverage(userId);
            },
            error: function(xhr, status, error) {
                console.error('Error updating grade: ' + error);
            }
        });
    });

    // On page load, calculate the average for all students
    $('tr').each(function() {
        var userId = $(this).find('input[type="number"]').data('user-id');
        if (userId) {
            calculateAverage(userId);
        }
    });
});

</script>

<?php
$content = ob_get_clean();
include 'views/master.php';
?>
