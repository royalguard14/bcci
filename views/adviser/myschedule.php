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
    /* Basic Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-family: Arial, sans-serif;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 8px 12px;
        text-align: center;
    }

    th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    /* Row styling for all rows */
    td {
        background-color: white; /* Default empty cell background color */
    }

    /* Highlight booked time slots */
    .booked {
        background-color: #f0f0f0; /* Light gray for booked slots */
        color: #d32f2f;
        font-weight: bold;
    }

    /* Optional: Highlight empty slots for clarity */
    td:empty {
        background-color: white; /* White background for empty slots */
    }

    /* Hover effect for rows */
    tr:hover {
        background-color: #f1f1f1;
    }

    /* Style for table container */
    .table-container {
        padding: 20px;
        overflow-x: auto;
    }
</style>




<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          

*Click Subject for Grade Management        </h3>
      </div>
      <div class="card-body">
    <div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Time Slot</th>
                <?php foreach ($daysOfWeek as $day) { ?>
                    <th><?php echo $day; ?></th>
                <?php } ?>
            </tr>
        </thead>
<tbody>
    <?php foreach ($timeSlots as $timeSlot) { ?>
        <tr>
            <td><?php echo $timeSlot; ?></td>
            <?php foreach ($daysOfWeek as $day) { ?>
                <td class="<?php echo isset($adviserScheduleMap[$day][$timeSlot]) ? 'booked' : ''; ?>"
                    <?php
                    // If the schedule exists for this day and time slot, make the cell clickable
                    if (isset($adviserScheduleMap[$day][$timeSlot])) {
                        $scheduleInfo = $adviserScheduleMap[$day][$timeSlot];
                        $scheduleId = is_array($scheduleInfo) ? $scheduleInfo['schedule_id'] : $scheduleInfo;
                        echo "onclick='alertScheduleId($scheduleId)'"; // Add onclick to show the schedule ID
                    }
                    ?>>
                    <?php
                    // Check if the adviser is scheduled for this day and time slot
                    if (isset($adviserScheduleMap[$day][$timeSlot])) {
                        $scheduleInfo = $adviserScheduleMap[$day][$timeSlot];
                        if (is_array($scheduleInfo)) {
                            // Display only the subject name (removing the schedule id)
                            echo $scheduleInfo['subject'] . " ( Section - " . $scheduleInfo['batch_id'].")";
                        } else {
                            echo $scheduleInfo; // Fallback if schedule info is not an array
                        }
                    }
                    ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</tbody>

<script>
// JavaScript function to handle the click event and send AJAX request
function alertScheduleId(scheduleId) {

    
    // AJAX request to get students enrolled in this schedule
    $.ajax({
        url: 'gradestudent', // Your PHP file that handles the AJAX request
        method: 'POST',
        data: {
            schedule_id: scheduleId
        },
        success: function(response) {
            var result = JSON.parse(response);

            // Check if students are found
            if (result.status === 'students_found') {
           

                // Redirect to gradingsubject.php with the schedule_id as a query parameter
                window.location.href = result.redirect_url;
            } else {
                // If no students are found, alert the user
                alert("No students found for this schedule.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching students: " + error);
        }
    });
}
</script>





    </table>
</div>

      </div>
    </div>
  </section>

</div>
<?php
$content = ob_get_clean();
include 'views/master.php';
?>


