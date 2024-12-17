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

<section class="col-lg-5 connectedSortable">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Set New Instructor</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="addInstructors">
                <!-- Instructor Dropdown -->
                <select name="instructor_id" id="instructorSelect" class="form-control">
                    <option disabled selected>Select Instructor</option>
                    <?php if (isset($instructors_unassign) && !empty($instructors_unassign)): ?>
                    
                        <?php foreach ($instructors_unassign as $index => $data): ?>
                            <option value="<?php echo $data['user_id']; ?>"><?php echo htmlspecialchars($data['fullname']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No unassigned instructors available</option>
                    <?php endif; ?>
                </select>
        </div>
        <div class="card-footer">
            <button class="form-control btn btn-success" type="submit">Register</button>
        </div>
        </form>
    </div>
</section>





  <section class="col-lg-7 connectedSortable">
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
                            <th>Name</th>
                            <th>Action</th>
                      
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($instructors)): ?>
                            <?php foreach ($instructors as $index => $data) { ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($data['fullname']); ?></td>
         		<td>            
         			<form action="remove-teacher-from-dept" method="POST" style="display:inline;">
              <input type="hidden" name="user_id" value="<?php echo $data['user_id']; ?>">
              <button type="submit" class="btn btn-block btn-outline-danger ml-1">Remove</button>
            </form>
        </td>
                                 
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No payment records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
      </div>
    </div>
  </section>
</div>

<?php
$content = ob_get_clean();
include 'views/master.php';
?>



            