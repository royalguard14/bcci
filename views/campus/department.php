<?php
ob_start();
$pageTitle = 'Department Management'; 

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


<style type="text/css">
    #subjectList {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem; /* Adjust spacing between columns */
}




</style>

<div class="row">
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          
Enroll new Department
        </h3>
      </div>
      <form action="campus-department/create" method="POST">
      <div class="card-body">

 <input class="form-control form-control-lg" type="text" placeholder="Department Name" name="department_name">
      </div>
      <div class="card-footer">
         <button type="submit" class="btn btn-block bg-gradient-primary">Register</button>
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
                 <table class="table table-head-fixed text-nowrap" id="example2">

        <thead>
            <tr>
                <th>No.</th>
                <th>Program</th>
                <th>Manage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($department as $index => $department) { ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                     <td><?php echo htmlspecialchars($department['code']); ?></td>
                     <td>





      <div class="btn-group">

                    <button type="button" class="btn btn-info">Subjects</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="openPermissions('<?php echo $department['id']; ?>', '1')">1<sup>st</sup> Semester</a>
                      <a class="dropdown-item" href="#" onclick="openPermissions('<?php echo $department['id']; ?>', '2')">2<sup>nd</sup> Semester</a>
                      <a class="dropdown-item" href="#" onclick="openPermissions('<?php echo $department['id']; ?>', '3')">3<sup>rd</sup> Semester</a>
                      <a class="dropdown-item" href="#" onclick="openPermissions('<?php echo $department['id']; ?>', '4')">4<sup>th</sup> Semester</a>
                    </div>

                  </div>
                    <div class="btn-group">
                                            <button type="button" class="btn btn-default">Academic</button>
                    <button type="button" class="btn btn-default dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="openRooms('<?php echo $department['id']; ?>')"> Rooms</a>
                      <a class="dropdown-item" href="#" onclick="openPermissions('<?php echo $department['id']; ?>', '2')"> Instructor</a>
               
                    </div>
                    </div>
     <div class="btn-group">
   <button type="button" class="btn btn-warning">Data</button>
                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#">Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#">Delete</a>
                    </div>

      
                  </div>    


                     </td>
                    
                </tr>
            <?php } ?>
        </tbody>
    </table>
      </div>
    </div>
  </section>
</div>



      <div class="modal fade" id="permissionsModal">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Available Subject</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

            <div class="modal-tool">
                        <div class="form-group">
            <input type="text" id="searchBox" class="form-control" placeholder="Search subjects...">
        </div>
            </div>
            </div>
            <div class="modal-body">
                       <form id="permissionsForm">
          <input type="hidden" id="perm_program_id">
            <div id="subjectList" style="max-height: 400px; overflow-y: auto;"></div>
        </form>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->




<div class="modal fade" id="roomsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Available Rooms</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-tool">
                <div class="form-group">
                    <input type="text" id="searchBox" class="form-control" placeholder="Search rooms...">
                </div>
            </div>
            <div class="modal-body">
                <form id="roomform">
                    <input type="hidden" id="room_id">
                    <div id="roomlist" style="max-height: 400px; overflow-y: auto;"></div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>





<script type="text/javascript">
function openRooms(id) {
    $.ajax({
        url: 'campus-department/rooms',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id }),
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);

            if (response.success) {
                var roomList = $('#roomlist');
                roomList.empty();

                // Create a container for the grid
                var columnContainer = $('<div class="row"></div>');

                response.rooms.forEach(function(room) {
                    if (room && room.id && room.type && room.location !== undefined) {
                        var isChecked = response.assigned_rooms.includes(room.id.toString()) ? 'checked' : '';
                        columnContainer.append(`
                            <div class="col-md-6"> <!-- Adjust column size as needed -->
                                <div class="form-check">
                                    <input class="form-check-input room-checkbox" type="checkbox" value="${room.id}" id="room${room.id}" ${isChecked}>
                                    <label class="form-check-label" for="room${room.id}">
                                        ${room.type} - Location: ${room.location}
                                    </label>
                                </div>
                            </div>
                        `);
                    }
                });

                roomList.append(columnContainer);

                // Add event listener for check/uncheck actions
                $('.room-checkbox').change(function() {
                    updateDepartmentRoomIds(id);
                });

                $('#roomsModal').modal('show');
            } else {
                showToast('Error', 'Failed to load rooms.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showToast('Error', 'An error occurred while fetching rooms.');
        }
    });
}

function updateDepartmentRoomIds(departmentId) {
    var selectedRooms = [];
    $('.room-checkbox:checked').each(function() {
        selectedRooms.push($(this).val());
    });

    $.ajax({
        url: 'campus-department/update-rooms',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id: departmentId,
            room_ids: selectedRooms.join(',')
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Success', 'Rooms updated successfully.');
            } else {
                showToast('Error', 'Failed to update rooms.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showToast('Error', 'An error occurred while updating rooms.');
        }
    });
}
</script>








<script type="text/javascript">
function openPermissions(id, sem) {
    $.ajax({
        url: 'campus-department/subject',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id, sem: sem }),
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);

            if (response.success) {
                var subjectList = $('#subjectList');
                subjectList.empty();

                // Create a container for the grid
                var columnContainer = $('<div class="row"></div>');

                response.subjects.forEach(function(subject) {
                    if (subject && subject.id && subject.name) {
                        var isChecked = response.assigned_subject.includes(subject.id.toString()) ? 'checked' : '';
                        columnContainer.append(`
                            <div class="col-md-6"> <!-- Adjust column size as needed -->
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" value="${subject.id}" id="perm${subject.id}" ${isChecked}>
                                    <label class="form-check-label" for="perm${subject.id}">${subject.name} - ${subject.code}</label>
                                </div>
                            </div>
                        `);
                    }
                });

                subjectList.append(columnContainer);

                // Add event listener for check/uncheck actions
                $('.permission-checkbox').change(function() {
                    updateDepartmentSubjectIds(id, sem);
                });

                $('#permissionsModal').modal('show');
            } else {
                showToast('Error', 'Failed to load permissions.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showToast('Error', 'An error occurred while fetching permissions.');
        }
    });
}




// Function to update department subject IDs in the database
function updateDepartmentSubjectIds(departmentId, semid) {
    // Collect all checked subject IDs
    var selectedSubjectIds = [];
    $('.permission-checkbox:checked').each(function() {
        selectedSubjectIds.push($(this).val());
    });

    // Send AJAX request to update department's subjects
    $.ajax({
        url: 'update-department-subject-ids', // Updated URL for department subject update
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ department_id: departmentId, subject_ids: selectedSubjectIds, semid: semid }),
        success: function(response) {
            showToast('Success', 'Department subjects updated successfully.');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showToast('Error', 'An error occurred while updating department subjects.');
        }
    });
}

  // Function to display toast notifications
  function showToast(title, message) {
    $(document).Toasts('create', {
      title: title,
      body: message,
      autohide: true,
      delay: 2000,
      class: title === 'Success' ? 'bg-success' : 'bg-danger'
    });
  }











</script>

<script type="text/javascript">
    $(document).ready(function () {
    // Search functionality
    $('#searchBox').on('input', function () {
        var searchValue = $(this).val().toLowerCase();
        $('#subjectList .form-check').each(function () {
            var subjectName = $(this).text().toLowerCase();
            if (subjectName.includes(searchValue)) {
                $(this).show(); // Show matching subjects
            } else {
                $(this).hide(); // Hide non-matching subjects
            }
        });
    });
});

</script>


<?php
$content = ob_get_clean();
include 'views/master.php';
?>