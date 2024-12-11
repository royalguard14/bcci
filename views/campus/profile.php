<?php
ob_start();
$pageTitle = 'Campus info Management'; 

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
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Academic Year
        </h3>
        <div class="card-tools">
          <ul class="nav nav-pills ml-auto">
            <li class="nav-item">
              <button type="button" class="btn btn-block btn-outline-primary btn-xs" data-toggle="modal" data-target="#schoolyearModal" >
                New Record
              </button>
            </li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <table id="example2" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th style="text-align: center;">#</th>
              <th style="text-align: center;">Year</th>
              <th style="text-align: center;">End</th>
              <th style="text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if(isset($academicYear)): ?>
              <?php $index = 1; ?>
              <?php foreach ($academicYear as $data): ?>
                <tr>
                  <td style="text-align: center;"><?php echo $index++; ?></td> 
                  <td style="text-align: center;"><?php echo $data['start']; ?></td>
                  <td style="text-align: center;"><?php echo $data['end']; ?></td>
    
                <td>
                  <form action="campus-school-year/delete" method="POST" style="display:inline;">
                    <input type="hidden" name="sy_id" value="<?php echo $data['id']; ?>">
                    <button type="submit" class="btn btn-block btn-outline-danger btn-xs">Drop</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<section class="col-lg-7 connectedSortable">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        Campus Information
      </h3>
    </div>
    <div class="card-body">
      <form method="POST" action="campus-info/update">
        
<?php foreach ($campusInfo as $info): ?>
  <div class="form-group">
    <label for="campus-info-<?= $info['id']; ?>"><?= $info['name']; ?>:</label>

    <?php if ($info['id'] == 5): ?>
      <!-- If ID is 5, show the select dropdown with academic years -->
      <select class="form-control" name="campus_info[<?= $info['id']; ?>]" id="campus-info-<?= $info['id']; ?>" required>
        <option value="" <?= (empty($info['function']) ? 'selected' : ''); ?>>Select academic year</option>
        <?php
          // Loop through and create options for each academic year
          foreach ($academicYear as $year): ?>
            <option value="<?= $year['id']; ?>" 
              <?= ($info['function'] == $year['id']) ? 'selected' : ''; ?>>
              <?= $year['start'] . ' - ' . $year['end']; ?>
            </option>
          <?php endforeach; ?>
      </select>

    <?php elseif ($info['id'] == 3): ?>
      <!-- If ID is 3, show checkboxes for operating days -->
      <?php
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        $selectedDays = explode(",", $info['function']); // Split the stored days into an array
      ?>
      <div id="campus-info-<?= $info['id']; ?>">
        <div class="row">
        <?php foreach ($days as $day): ?>
          <div class="col-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" 
                   name="campus_info[<?= $info['id']; ?>][]" 
                   id="campus-info-<?= $info['id']; ?>-<?= $day; ?>" 
                   value="<?= $day; ?>" 
                   <?= in_array($day, $selectedDays) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="campus-info-<?= $info['id']; ?>-<?= $day; ?>">
              <?= $day; ?>
            </label>
          </div>
          </div>
        <?php endforeach; ?>
      </div>
      </div>


    <?php else: ?>
      <!-- Default input for other IDs -->
      <input type="text" class="form-control" name="campus_info[<?= $info['id']; ?>]" 
             id="campus-info-<?= $info['id']; ?>" value="<?= htmlspecialchars($info['function']); ?>" required>
    <?php endif; ?>

  </div>
<?php endforeach; ?>




      </div>
      <div class="card-footer d-flex justify-content-center">
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
  </div>
</section>


</div>
<div class="modal fade" id="schoolyearModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Record New School Year</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="campus-school-year/create">
        <div class="modal-body">
          <div class="form-group">
            <label for="start_school_year">Start</label>
            <input
            type="number"
            name="start_school_year"
            id="start_school_year"
            class="form-control"
            required
            />
          </div>
          <div class="form-group">
            <label for="end_school_year">End</label>
            <input
            type="number"
            name="end_school_year"
            id="end_school_year"
            class="form-control"
            readonly
            />
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit"  class="btn btn-primary">Record</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  // Get references to input elements
  const startYearInput = document.getElementById('start_school_year');
  const endYearInput = document.getElementById('end_school_year');
  // Get the current year
  const currentYear = new Date().getFullYear();
  // Set the minimum value for the "Start" year dynamically
  startYearInput.min = currentYear;
  // Event listener for when the "Start" year is changed
  startYearInput.addEventListener('input', function () {
    const startYear = parseInt(startYearInput.value, 10);
    // Set the "End" year to be one year after the "Start" year
    if (!isNaN(startYear)) {
      endYearInput.value = startYear + 1;
    } else {
      endYearInput.value = ''; // Clear "End" year if input is invalid
    }
  });
</script>
<?php
$content = ob_get_clean();
include 'views/master.php';
?>