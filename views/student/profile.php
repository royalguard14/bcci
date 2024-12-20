<?php
ob_start();
$pageTitle = 'Personal Information'; 

// Define the semester map
$semesterMap = [
    1 => "I - 1st Sem",
    2 => "I - 2nd Sem",
    3 => "II - 1st Sem",
    4 => "II - 2nd Sem",
    5 => "III - 1st Sem",
    6 => "III - 2nd Sem",
    7 => "IV - 1st Sem",
    8 => "IV - 2nd Sem",
];

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
  <div class="col-md-3">
    <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        <div class="text-center">
          <img class="profile-user-img img-fluid img-circle"
          src="<?php echo !empty($myInfo['photo_path']) ? $myInfo['photo_path'] : '/BlissES/assets/img/default-profile.png'; ?>"
          alt="User profile picture"
          style="width: 128px; height: 128px;">
      </div>
      <h3 class="profile-username text-center">
        <?= htmlspecialchars(ucwords(strtolower($myInfo['first_name']))) . ' ' . 
        htmlspecialchars(strtoupper(substr(trim($myInfo['middle_name']), 0, 1)) . '.') . ' ' . 
        htmlspecialchars(ucwords(strtolower($myInfo['last_name']))); ?>
    </h3>
    <p class="text-muted text-center">
<?php

// Get the semester label based on the section
$semesterLabel = isset($semesterMap[(int)$myInfo['grade']]) ? $semesterMap[(int)$myInfo['grade']] : "Unknown Semester";

// Output the semester and grade
echo  htmlspecialchars(ucwords(strtolower($myInfo['section']))) . '<br>' . htmlspecialchars(ucwords($semesterLabel));
?>






    	</p>




                      <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#user-modal" >
                         <b>Upload Photo</b>
                     </button>

</div>
</div>
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">About Me</h3>
</div>
<div class="card-body">
    <strong><i class="fas fa-info mr-1"></i> Basic Info</strong>
    <p class="text-muted">
        <?php echo "<strong>Gender:</strong> <small>" . ($myInfo['sex'] === 'F' ? "Female" : "Male") . "</small><br>"; ?>
        <?php echo "<strong>Birth Date:</strong> <small>" . date('F j, Y', strtotime($myInfo['birth_date'])) . "</small><br>"; ?>
    </p>
    <hr>
    <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>
    <p class="text-muted">                 
        <?php echo htmlspecialchars(ucwords(
          $myInfo['house_street_sitio_purok'] . ", " .  
          $myInfo['barangay'] . ", " .  
          $myInfo['municipality_city'] . ", " .  
          $myInfo['province'])) ?></p>
          <hr>

       
    </div>
</div>
</div>
<div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Grades</a></li>
          <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Class Schedule</a></li>
          <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Account Setting</a></li>
      </ul>
  </div>
  <div class="card-body">
    <div class="tab-content">
      <div class="active tab-pane" id="activity">
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <tr>
        <th rowspan="2" style="text-align: center;">Subject</th>
        <th colspan="4" style="text-align: center;">Grades</th>
        <th rowspan="2" style="text-align: center;">Average</th>
    </tr>
    <tr>
        <th style="text-align: center;">Prelim</th>
        <th style="text-align: center;">Midterm</th>
        <th style="text-align: center;">Pre-Finals</th>
        <th style="text-align: center;">Final</th>
    </tr>
    <?php 
    $totalGrades = 0;
    $completedSubjects = 0; // Counter for subjects with complete grades

    // Function to map numeric grades to grade points
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
        if ($grade < 74.50) return 3.25;
        return 'No Grade';
    }

    foreach ($allSubjectInGrade as $subject): 
        $grades = [
            '1st Grading' => isset($gradeMap[$subject['subject_id']][1]) ? $gradeMap[$subject['subject_id']][1] : 'No Grade',
            '2nd Grading' => isset($gradeMap[$subject['subject_id']][2]) ? $gradeMap[$subject['subject_id']][2] : 'No Grade',
            '3rd Grading' => isset($gradeMap[$subject['subject_id']][3]) ? $gradeMap[$subject['subject_id']][3] : 'No Grade',
            '4th Grading' => isset($gradeMap[$subject['subject_id']][4]) ? $gradeMap[$subject['subject_id']][4] : 'No Grade'
        ];

        // Check if all grades are numeric
        $isComplete = is_numeric($grades['1st Grading']) &&
                      is_numeric($grades['2nd Grading']) &&
                      is_numeric($grades['3rd Grading']) &&
                      is_numeric($grades['4th Grading']);

        // Map numeric grades to grade points
        $gradePoints = array_map(function($grade) {
            return is_numeric($grade) ? mapGradeToPoint($grade) : $grade;
        }, $grades);

        $averageGrade = $isComplete 
            ? array_sum(array_map('mapGradeToPoint', $grades)) / 4 
            : 'Incomplete';

        ?>
        <tr>
            <td><?php echo $subject['subject_name']; ?></td>
            <td style="text-align: center;"><?php echo $gradePoints['1st Grading']; ?></td>
            <td style="text-align: center;"><?php echo $gradePoints['2nd Grading']; ?></td>
            <td style="text-align: center;"><?php echo $gradePoints['3rd Grading']; ?></td>
            <td style="text-align: center;"><?php echo $gradePoints['4th Grading']; ?></td>
            <td style="text-align: center;">
                <?php echo is_numeric($averageGrade) ? number_format($averageGrade, 2) : $averageGrade; ?>
            </td>
        </tr>
        <?php 
        if ($isComplete) {
            $totalGrades += $averageGrade;
            $completedSubjects++;
        }
    endforeach; 
    ?>
    <tr>
        <td colspan="5" style="text-align: right;"><strong>Overall Average</strong></td>
        <td style="text-align: center;">
            <?php 
            if ($completedSubjects > 0) {
                $overallAverage = $totalGrades / $completedSubjects;
                echo number_format($overallAverage, 2);
            } else {
                echo 'Incomplete Grades';
            }
            ?>
        </td>
    </tr>
</table>


    </div>
    <div class="tab-pane" id="timeline">

<table class="schedule-table" border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <?php  

echo "<style>
    table.schedule-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 1em; /* Default font size */
        text-align: center;
    }
    table.schedule-table th, table.schedule-table td {
        border: 1px solid #ddd; /* Light grey grid lines */
        padding: 10px;
    }
    table.schedule-table th {
        background-color: #f4f4f4;
        color: #333;
        font-weight: bold;
    }
    table.schedule-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    table.schedule-table tr:hover {
        background-color: #f1f1f1;
    }
    table.schedule-table td {
        vertical-align: middle;
    }
    table.schedule-table th:first-child,
    table.schedule-table td:first-child {
        background-color: #f8f8f8;
        font-weight: bold;
    }
    .empty-cell {
        background-color: #f4f4f4;
        color: #aaa;
    }

    /* Make text size responsive */
    @media (max-width: 1200px) {
        table.schedule-table {
            font-size: 0.9em; /* Slightly smaller font for medium screens */
        }
    }

    @media (max-width: 992px) {
        table.schedule-table {
            font-size: 0.8em; /* Smaller font for smaller screens */
        }
    }

    @media (max-width: 768px) {
        table.schedule-table {
            font-size: 0.7em; /* Even smaller font for mobile screens */
        }
    }

    @media (max-width: 576px) {
        table.schedule-table {
            font-size: 0.6em; /* Smallest font size for very small screens */
        }
    }
</style>";

         ?>
<?php 

echo "<tr><th>Time</th>";
foreach ($days as $day) {
    echo "<th>$day</th>";
}
echo "</tr>";
 ?>
    </thead>
    <tbody>
        <?php
 foreach ($timeSlots as $timeSlot) {
    echo "<tr>";
    echo "<td>$timeSlot</td>";
    foreach ($days as $day) {
        if (isset($scheduleMap[$day][$timeSlot])) {
            echo "<td>" . implode("<br>", $scheduleMap[$day][$timeSlot]) . "</td>";
        } else {
            echo "<td class='empty-cell'>-</td>";
        }
    }
    echo "</tr>";
}        ?>
    </tbody>
</table>

    </div>
    <div class="tab-pane" id="settings">
        <form class="form-horizontal" method="POST" action="updateuserpass" autocomplete="off">
          <div class="form-group row">
            <label for="inputUsername" class="col-sm-2 col-form-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($myInfo['username']); ?>" readonly>
          </div>
      </div>
      <div class="form-group row">
        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" placeholder="Email" readonly value="<?php echo htmlspecialchars($myInfo['email']); ?>">
      </div>
  </div>
  <input type="password" style="display:none" autocomplete="new-password">
  <div class="form-group row">
      <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
      <div class="col-sm-10">
        <input 
        type="password" 
        class="form-control" 
        name="passwd" 
        id="inputPassword" 
        autocomplete="new-password" 
        placeholder="Leave blank if you do not want to change the password"
        >
    </div>
</div>
<div class="form-group row">
    <div class="offset-sm-2 col-sm-10">
      <button type="submit" class="btn btn-danger">Submit</button>
  </div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>


<div class="modal fade" id="user-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Upload Photo</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="upload-profile-form" method="POST" action="uploadprofile" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label for="profile_pic">Upload Profile Picture</label>
            <input type="file" id="profile_pic" name="profile_pic" class="form-control" accept=".jpg, .jpeg, .png" required>
          </div>
          <!-- Progress Bar -->
          <div class="progress" style="height: 20px;">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" id="upload-btn" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>



<script type="text/javascript">
$(document).ready(function() {
    $('#upload-profile-form').on('submit', function(event) {
        event.preventDefault();
        
        var formData = new FormData(this);

        $.ajax({
            url: 'uploadprofile', // The route for the uploadprofile method
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percent = (e.loaded / e.total) * 100;
                        $('#progress-bar').css('width', percent + '%');
                        $('#progress-bar').attr('aria-valuenow', percent);
                    }
                });
                return xhr;
            },
            success: function(response) {
        if (response.success) {
       

Swal.fire({
  position: "center",
  icon: "success", // Corrected the typo ("succres" -> "success")
  title: "Your work has been saved",
  showConfirmButton: false,
  timer: 1500
}).then(() => { // Use a callback function here
  var newSrc = response.newPhotoPath + '?t=' + new Date().getTime(); // Add timestamp to avoid caching
  $('.profile-user-img').attr('src', newSrc);

  // Hide the modal
  $('#user-modal').modal('hide');
});








    } else {
        alert(response.message || 'Error updating profile picture.');
    }

            },
            error: function() {
                alert('Error uploading file.');
            }
        });
    });
});

</script>
<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>