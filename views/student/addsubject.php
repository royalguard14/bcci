<?php
ob_start();
$pageTitle = 'Add Subjects';

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

// Display session messages
if (isset($_SESSION['error'])) {
    displayToastMessage('error', 'bg-danger', 'Error');
}
if (isset($_SESSION['info'])) {
    displayToastMessage('info', 'bg-info', 'Information');
}
if (isset($_SESSION['success'])) {
    displayToastMessage('success', 'bg-success', 'Success');
}

// Helper function to get prerequisite names
function getPreReqNames($preReqIds, $db) {
    if (empty($preReqIds)) {
        return 'None';
    }

    $preReqArray = explode(',', $preReqIds);
    $placeholders = implode(',', array_fill(0, count($preReqArray), '?'));

    $stmt = $db->prepare("SELECT code as name FROM subjects WHERE id IN ($placeholders)");
    $stmt->execute($preReqArray);
    $preReqNames = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return implode(', ', $preReqNames);
}

?>

<style type="text/css">
    .highlight-conflict {
    background-color: #f8d7da; /* Light red background for conflict */
    color: #721c24; /* Dark red text color */
}

</style>

<div class="row">
    <section class="col-lg-12 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Select Your Subjects</h3>

                <div class="card-tools">
             <button type="button" class="btn btn-block btn-outline-info btn-flat">Enroll Now!!!</button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($detailedSubjects)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Unit</th>
                           
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detailedSubjects as $subject): ?>

                                <tr class="schedule-row" data-schedule-id="<?= htmlspecialchars($subject['id']); ?>">
                                    <td class="subject-cell"><?= htmlspecialchars($subject['code']); ?></td>
                                    <td><?= htmlspecialchars($subject['name']); ?></td>
                                    <td><?= htmlspecialchars($subject['unit_lec'] + $subject['unit_lab']); ?></td>
                    
                                    <td>
                                        <div class="form-group">
                                            <!-- Single Dropdown for Batch Selection -->
                                            <select class="form-control batch-select" data-subject-id="<?= htmlspecialchars($subject['id']); ?>">
                                                <option value="">Select Batch</option>
                                                <?php
                                                // Display available batches for the subject
                                                if (isset($schedules[$subject['id']]) && !empty($schedules[$subject['id']])) {
                                                    foreach ($schedules[$subject['id']] as $batchIndex => $batch) {
                                                        echo "<option value='{$batchIndex}'>Batch " . htmlspecialchars($batchIndex) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="schedule-list-container">
                                            <!-- Schedule List (hidden initially) -->
                                            <ul id="schedule-list-<?= htmlspecialchars($subject['id']); ?>" class="list-group" style="display: none;">
                                                <!-- List items will be added dynamically -->
                                            </ul>
                                        </div>
                                    </td>
                                
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No subjects to enroll in for this semester.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
// Define selectedSchedules globally so it's accessible throughout the script
var selectedSchedules = {}; // Object to store selected schedule IDs by subject

$(document).ready(function() {

    // On batch selection, fetch schedules and trigger conflict check automatically
    $('.batch-select').change(function() {
        var subjectId = $(this).data('subject-id');
        var batchIndex = $(this).val();
        
        console.log("Subject ID: " + subjectId + ", Batch Index: " + batchIndex); // Debugging line

        // Clear previously selected schedules for this subject
        selectedSchedules[subjectId] = [];

        // If batch is selected
        if (batchIndex) {
            // Send AJAX request to fetch schedules
            $.ajax({
                url: 'fetchSubject',  // Your endpoint to fetch schedules
                type: 'GET',
                data: {
                    subject_id: subjectId,
                    batch_index: batchIndex
                },
                success: function(response) {
                    // Log the response for debugging
                    console.log(response);

                    // Assuming response is a JSON array of schedules
                    var schedules = JSON.parse(response);
                    
                    // Populate the schedule list
                    var scheduleList = $('#schedule-list-' + subjectId);
                    scheduleList.empty(); // Clear any existing items

                    // Append new schedule items and add their IDs to the selectedSchedules object for this subject
                    schedules.forEach(function(schedule) {
                        var listItem = '<li class="list-group-item" data-schedule-id="' + schedule.id + '">' +
                            'Day: ' + schedule.day + ', ' +
                            'Time: ' + schedule.time_slot + ', ' +
                            'Type: ' + schedule.session_type +
                            '</li>';
                        scheduleList.append(listItem);

                        // Add schedule ID to selectedSchedules for this subject
                        selectedSchedules[subjectId].push(schedule.id);
                    });

                    // Show the schedule list
                    scheduleList.show();

                    // Now trigger the conflict check automatically
                    checkScheduleConflicts();
                },
                error: function() {
                    alert('Error fetching schedule data');
                }
            });
        } else {
            // Hide the schedule list if no batch is selected
            $('#schedule-list-' + subjectId).hide();
        }

        // Log the updated selected schedule IDs for this subject to the console
        console.log("Selected Schedule IDs for Subject " + subjectId + ": " + selectedSchedules[subjectId].join(','));
    });

    // Function to check for schedule conflicts
    function checkScheduleConflicts() {
        var allSelectedSchedules = [];

        // Collect all selected schedule IDs from each subject
        for (var subjectId in selectedSchedules) {
            allSelectedSchedules = allSelectedSchedules.concat(selectedSchedules[subjectId]);
        }

        if (allSelectedSchedules.length > 0) {
            // Send AJAX request to check for conflicts
            $.ajax({
                url: 'checkScheduleConflict', // Replace with the actual endpoint URL
                type: 'POST',
                data: {
                    schedule_ids: allSelectedSchedules
                },
                success: function(response) {
                    // Remove previous conflict highlights
                    $('.highlight-conflict').removeClass('highlight-conflict');
                    
                    if (response.conflict) {
                        // Loop through conflict details
                        response.details.forEach(function(conflict) {
                            var conflictIds = conflict.conflict_ids.split(',');

                            // Highlight list items with conflicting IDs
                            conflictIds.forEach(function(id) {
                                $(`.list-group-item[data-schedule-id="${id}"]`).addClass('highlight-conflict');
                            });
                        });
                    } 
                },
                error: function() {
                    alert('Error checking for schedule conflicts. Please try again.');
                }
            });
        } else {
            alert("No schedules selected.");
        }
    }

    // Handle "Enroll Now" button click
 $('.btn-outline-info').click(function() {
    var selectedSubjectIds = Object.keys(selectedSchedules); // Get all subject IDs
    var selectedData = [];

    // Gather schedule IDs for each subject
    for (var subjectId in selectedSchedules) {
        if (selectedSchedules[subjectId].length > 0) {
            selectedData.push({
                subjectId: subjectId,
                scheduleIds: selectedSchedules[subjectId]
            });
        }
    }



    $.ajax({
        url: 'enrollSubjects',  // Endpoint to process enrollment
        type: 'POST',
        data: {
            enrollmentData: JSON.stringify(selectedData) // Send data as a string if necessary
        },
        success: function(response) {
            var parsedResponse = JSON.parse(response);
            if (parsedResponse.success) {
Swal.fire({
  position: "center",
  icon: "success",
  title: "Your work has been saved",
  showConfirmButton: false,
  timer: 1500
}).then(function() {
  // After 1500ms, redirect to home
  window.location.href = './home';
});
            } else {
                // Enrollment failed, show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.error || 'Something went wrong!',
                    footer: '<a href="#">Why do I have this issue?</a>'
                });
            }
        }
     
    });
});

});

</script>


<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>
