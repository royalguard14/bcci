<?php
ob_start();
$pageTitle = $this->deanDept; 


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



<style>
    /* General table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        background-color: #f9f9f9;
    }

    /* Table header styling */
    table thead tr {
        background-color: #343a40;
        color: #ffffff;
        text-transform: uppercase;
    }

    table th, table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    /* Alternate row color */
    table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tbody tr:hover {
        background-color: #e9ecef;
    }

    /* Input field styling */
    input.grade-input {
        width: 100%;
        padding: 8px 10px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box; /* Ensures padding doesn't affect width */
    }

    input.grade-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        input.grade-input {
            font-size: 12px;
        }
    }
    .grade-input {
    width: 80px; /* Adjust the width */
    text-align: center;
}
    /* Grey background for readonly inputs */
    input[readonly] {
        background-color: #e0e0e0; /* Light grey */
        cursor: not-allowed; /* Shows a 'not-allowed' cursor */
    }

</style>





<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          

        <?php echo htmlspecialchars($subjectName); ?>
        </h3>
      </div>
      <div class="card-body">
       <?php if (!empty($studentsEnrolled)): ?>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th style="text-align: center;">Prelim</th>
                <th style="text-align: center;">Midterm</th>
                <th style="text-align: center;">Pre-final</th>
                <th style="text-align: center;">Final</th>
            </tr>
        </thead>
      <tbody>
    <?php foreach ($studentsEnrolled as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars($student['fullname']); ?></td>
            <td>
                <input 
                       <?php echo ($this->campusDataCurrentTerm != 1 && $this->campusDataCurrentTerm != 5) ? 'readonly' : ''; ?>
                        type="number" 
                       class="grade-input" 
                       data-user-id="<?php echo $student['user_id']; ?>" 
                       data-eh-id="<?php echo $student['eh_id']; ?>" 
                       data-term="1"
                       step="0.01"
                       value="<?php echo $student['grades'][1] != 0 ? $student['grades'][1] : ''; ?>">
            </td>
            <td>
                <input 
<?php echo ($this->campusDataCurrentTerm != 2 && $this->campusDataCurrentTerm != 5) ? 'readonly' : ''; ?>
                type="number" 
                       class="grade-input" 
                       data-user-id="<?php echo $student['user_id']; ?>" 
                       data-eh-id="<?php echo $student['eh_id']; ?>" 
                       data-term="2"
                       step="0.01"
                       value="<?php echo $student['grades'][2] != 0 ? $student['grades'][2] : ''; ?>">
            </td>
            <td>
                <input 
<?php echo ($this->campusDataCurrentTerm != 3 && $this->campusDataCurrentTerm != 5) ? 'readonly' : ''; ?>
                type="number" 
                       class="grade-input" 
                       data-user-id="<?php echo $student['user_id']; ?>" 
                       data-eh-id="<?php echo $student['eh_id']; ?>" 
                       data-term="3"
                       step="0.01"
                       value="<?php echo $student['grades'][3] != 0 ? $student['grades'][3] : ''; ?>">
            </td>
            <td>
                <input 
<?php echo ($this->campusDataCurrentTerm != 4 && $this->campusDataCurrentTerm != 5) ? 'readonly' : ''; ?>
                type="number" 
                       class="grade-input" 
                       data-user-id="<?php echo $student['user_id']; ?>" 
                       data-eh-id="<?php echo $student['eh_id']; ?>" 
                       data-term="4"
                       step="0.01"
                       value="<?php echo $student['grades'][4] != 0 ? $student['grades'][4] : ''; ?>">
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

    </table>
<?php else: ?>
    <p>No students enrolled in this schedule.</p>
<?php endif; ?>


<script>
$(document).ready(function() {
    // When a grade input changes
    $('.grade-input').on('change', function() {
        let userId = $(this).data('user-id'); // User ID
        let ehId = $(this).data('eh-id');     // Enrollment History ID
        let term = $(this).data('term');      // Term ID (1, 2, 3, or 4)
        let grade = $(this).val();            // New grade value

        // Send the data to the server using AJAX
        $.ajax({
            url: 'updategrade', // The server-side script to handle updates
            type: 'POST',
            data: {
                user_id: userId,
                eh_id: ehId,
                term_id: term,
                grade: grade
            },
            success: function(response) {
                alert('Grade updated successfully!');
            },
            error: function(xhr, status, error) {
                alert('Failed to update grade. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });
});
</script>

      </div>
    </div>
  </section>

</div>
<?php
$content = ob_get_clean();
include 'views/master.php';
?>


