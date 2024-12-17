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


<div class="row">

  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          

          Sales
        </h3>
      </div>
      <div class="card-body">
        
<table class="table table-head-fixed text-nowrap" id="example3">
    <thead>
        <tr>
            <th>No.</th>
            <th>Subject</th>
            <th>Section</th>
            <th>Schedule</th>
            <th>Adviser</th>
        </tr>
    </thead>
    <tbody>
        <?php if (isset($finalSchedules)): ?>
            <?php foreach ($finalSchedules as $index => $data) { ?>
                <tr>
                    <td style="vertical-align: middle;"><?php echo $index + 1; ?></td>
                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($data['subject_name']); ?></td>
                    <td style="vertical-align: middle;"><?php echo "Batch " . htmlspecialchars($data['batch']); ?></td>  <!-- Output Batch/Section -->

                    <td>
                        <?php if (isset($data['schedule']) && !empty($data['schedule'])): ?>
                                                           <?php
                                    $schedules = explode(',', $data['schedule']); // Convert the schedule string into an array
                                    foreach ($schedules as $schedule) {
                                        echo '<span>• ' . htmlspecialchars(trim($schedule)) . '</span><br>';
                                    }
                                ?>
                          
                        <?php else: ?>
                            <p>No schedule available</p>  <!-- Fallback message if schedule is not available -->
                        <?php endif; ?>
                    </td>

                 <!-- Adviser Dropdown -->
<td>
    <select name="adviser[<?php echo $data['subject_id'] . '_' . $data['batch']; ?>]" 
            class="form-control adviser-select" 
            data-schedule-ids="<?php echo htmlspecialchars(json_encode($data['schedule_ids'])); ?>">
        <option value="">Select Adviser</option>
        <?php foreach ($advisers as $adviser) { ?>
            <option value="<?php echo $adviser['id']; ?>" 
                <?php echo (isset($data['adviser']) && $data['adviser'] == $adviser['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($adviser['name']); ?>
            </option>
        <?php } ?>
    </select>
</td>

                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">NO SCHEDULE AVAILABLE</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


      </div>
    </div>
  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Delegate the event to a parent element that exists in the DOM
    document.body.addEventListener("change", function (event) {
        // Check if the change event is coming from a select with the class 'adviser-select'
        if (event.target && event.target.classList.contains("adviser-select")) {
            const select = event.target;
            const selectedAdviser = select.value; // Selected adviser ID (can be "")
            const scheduleIds = JSON.parse(select.dataset.scheduleIds); // Array of schedule IDs

            // If "Select Adviser" is chosen, send adviser_id as null
            const adviserId = selectedAdviser === "" ? null : selectedAdviser;

            // Send AJAX request to the backend
            fetch("updateAdviser", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    schedule_ids: scheduleIds,
                    adviser_id: adviserId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Success',
                        body: 'Adviser updated successfully!',
                        autohide: true,
                        delay: 3000
                    });
                } else {
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'Error',
                        body: data.error || 'Failed to update adviser.',
                        autohide: true,
                        delay: 3000
                    });
                }
            })
            .catch(error => {
                
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Error',
                    body: 'Duplicate schedules found',
                    autohide: true,
                    delay: 3000
                });
            });
        }
    });
});

</script>


<?php
$content = ob_get_clean();
include 'views/master.php';
?>


