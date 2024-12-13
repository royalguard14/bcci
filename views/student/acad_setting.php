<?php
ob_start();
$pageTitle = 'Course Management'; 


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
          Select your Course
        </h3>
      </div>
      <div class="card-body">
  <div class="row">
        <?php foreach ($departments as $department): ?>
            <div class="col-md-4 mb-4 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center"><?= htmlspecialchars($department['course_name']); ?></h5>
                     
                     
                    </div>
                    <div class="card-footer">   <button type="button" class="btn btn-primary w-100 select-course-btn" 
                                data-course-id="<?= htmlspecialchars($department['id']); ?>" 
                                data-course-name="<?= htmlspecialchars($department['course_name']); ?>">
                            Select Course
                        </button></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
      </div>
    </div>
  </section>
</div>


<script>
    $(document).ready(function () {
        $('.select-course-btn').click(function () {
            var course_id = $(this).data('course-id');
            var course_name = $(this).data('course-name');

            Swal.fire({
                title: "Confirm Course Selection",
                text: `Are you sure you want to select "${course_name}" as your course?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Confirm"
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to update the course
                    $.ajax({
                        url: 'updatemycourse', // Change to your actual endpoint
                        type: 'POST',
                        data: { course_id: course_id },
                        success: function (response) {
                 			 location.reload();
                        },
                    
                    });
                }
            });
        });
    });
</script>


<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>