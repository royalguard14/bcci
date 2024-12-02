<?php
ob_start();
$pageTitle = 'Student Management'; 
function getInitials($string) {
  $words = explode(' ', $string);
  $initials = '';
  foreach ($words as $word) {
    $initials .= strtoupper($word[0]);
  }
  return $initials;
}
?>
<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Learners
        </h3>




      <div class="card-tools">
                  <ul class="nav nav-pills ml-auto">
                    <li class="nav-item">
                      <button type="button" class="btn btn-block btn-outline-primary btn-xs" data-toggle="modal" data-target="#user-modal" >
                         Upload
                     </button>
                 </li>
                 <li class="nav-item">
                    <a href="assets/templates/learners.xlsx" class="btn btn-block btn-outline-secondary btn-xs" download>
                        Download
                    </a>
                </li>
            </ul>
        </div>




      </div>
      <div class="card-body">
        <table id="example2" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th style="text-align: center;">#</th>
              <th style="text-align: center;">LRN</th>
              <th style="text-align: center;">Full Name</th>
              <th style="text-align: center;">Personal Info</th>
              <th style="text-align: center;">Contact Info</th>
              <th style="text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody>
           <?php if(isset($students)): ?>
            <?php $index = 1; ?>
            <?php foreach ($students as $data): ?>
              <tr>
               <td style="text-align: center; vertical-align: middle;"><?php echo $index++; ?></td> 
               <td style="text-align: center; vertical-align: middle;">
                <?php 
                echo htmlspecialchars($data['lrn']);
                ?>
              </td>
                           <td style="text-align: center; vertical-align: middle;">
                <?php 
                echo htmlspecialchars(ucwords(
                  $data['last_name'] . ', ' . 
                  $data['first_name'] . 
                  (isset($data['middle_name']) && !empty($data['middle_name']) ? ' ' . $data['middle_name'][0] . '.' : '')
                ));
                ?>
              </td>
              <td style="text-align: left; vertical-align: middle;">
                <?php 
    $birth_date = $data['birth_date']; // Assuming $data['birth_date'] is in YYYY-MM-DD format
$target_date = date('Y') . '-10-31'; // October 31 of the current year
// Convert to DateTime objects
$birth_date_obj = new DateTime($birth_date);
$target_date_obj = new DateTime($target_date);
// Calculate age difference
$age = $birth_date_obj->diff($target_date_obj)->y;
echo "<strong>Address:</strong> <small>" . htmlspecialchars(ucwords(
  $data['house_street_sitio_purok'] . ", " .  
  $data['barangay'] . ", " .  
  $data['municipality_city'] . ", " .  
  getInitials($data['province']))) . "</small><br>";
echo "<strong>Gender:</strong> <small>" . ($data['sex'] === 'F' ? "Female" : "Male") . "</small><br>";
echo "<strong>Religion:</strong> <small>" . htmlspecialchars(ucwords($data['religion'])) . "</small><br>";
echo "<strong>Mother Tongue:</strong> <small>" . htmlspecialchars(ucwords($data['mother_tongue'])) . "</small><br>";
echo "<strong>Ethnic Group:</strong> <small>" . htmlspecialchars(ucwords($data['ip_ethnic_group'])) . "</small><br>";
echo "<strong>Birth Date:</strong> <small>" . date('F j, Y', strtotime($data['birth_date'])) . "</small><br>";
echo "<strong>Age as of October 31:</strong> <small>$age years old</small><br>";
?>
</td>
<td style="text-align: left; ">
  <?php 
    // Display details
  echo "<strong>Father:</strong> <small>" . htmlspecialchars(ucwords($data['fathers_name'])) . "</small><br>";
  echo "<strong>Mother:</strong> <small>" . htmlspecialchars(ucwords($data['mother_name'])) . "</small><br>";
  echo "<strong>Guardian:</strong> <small>" . htmlspecialchars(ucwords($data['guardian_name'])) . "</small><br>";
  echo "<strong>Relationship:</strong> <small>" . date('F j, Y', strtotime($data['relationship'])) . "</small><br>";
  echo "<strong>Contact No.:</strong> <small>" . htmlspecialchars(ucwords($data['contact_number'])) . "</small>";
  ?>
</td>
<td style="text-align: center; vertical-align: middle;">
<div class="row d-flex justify-content-center">
    <div class="col-12 col-md-6 d-flex">
     <button type="button" class="btn btn-block btn-outline-warning btn-xs flex-fill update-student-btn" 
  data-teacher='<?php echo json_encode([
    "user_id" => $data['user_id'],
    "first_name" => $data['first_name'],
    "last_name" => $data['last_name'],
    "middle_name" => $data['middle_name'],
    "email" => $data['email'],
    "sex" => $data['sex'],
    "birth_date" => $data['birth_date'],
    "mother_tongue" => $data['mother_tongue'],
    "ip_ethnic_group" => $data['ip_ethnic_group'],
    "religion" => $data['religion'],
    "house_street_sitio_purok" => $data['house_street_sitio_purok'],
    "barangay" => $data['barangay'],
    "municipality_city" => $data['municipality_city'],
    "province" => $data['province'],
    "fathers_name" => $data['fathers_name'],
    "mother_name" => $data['mother_name'],
    "guardian_name" => $data['guardian_name'],
    "relationship" => $data['relationship'],
    "contact_number" => $data['contact_number'],
    "role_id" => $data['role_id'],
    ]) ?>'
    data-toggle="modal" data-target="#updateTeacherModal">
    Edit
  </button>
    </div>
    <div class="col-12 col-md-6 d-flex">
    <form action="/BCCI/users/delete" method="POST" style="display:inline;">
    <input type="hidden" name="user_id" value="<?php echo $data['user_id']; ?>">
    <input type="hidden" name="paths" value="students-list">
    <button type="submit" class="btn btn-block btn-outline-danger btn-xs flex-fill">Delete</button>
  </form>
    </div>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</section>
</div>
<!-- Modal for updating teacher details -->
<div class="modal fade" id="updateTeacherModal" tabindex="-1" role="dialog" aria-labelledby="updateTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateTeacherModalLabel">Update Student Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form to update teacher details -->
        <form id="updateTeacherForm" method="POST" action="/BCCI/users/update">
          <input type="hidden" name="user_id" id="teacherUserId">
          <input type="hidden" name="role_id" id="userrole">
          <div class="card card-dark card-outline card-tabs">
            <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Basic</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Profile</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Address</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Others</a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="custom-tabs-one-tabContent">
                <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                  <input type="hidden" class="form-control" name="email" id="teacherEmail" readonly>
                  <!-- Row for name fields (First, Middle, Last) -->
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="teacherFirstName">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="teacherFirstName" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="teacherMiddleName">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" id="teacherMiddleName">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="teacherLastName">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="teacherLastName" required>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="teacherSex">Gender</label>
                        <select class="form-control" name="sex" id="teacherSex">
                          <option value="M">Male</option>
                          <option value="F">Female</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="teacherBirthDate">Birth Date</label>
                        <input type="date" class="form-control" name="birth_date" id="teacherBirthDate" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="teacherMotherTongue">Mother Tongue</label>
                        <input type="text" class="form-control" name="mother_tongue" id="teacherMotherTongue" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="teacherEthnicGroup">Ethnic Group</label>
                        <input type="text" class="form-control" name="ip_ethnic_group" id="teacherEthnicGroup" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="teacherReligion">Religion</label>
                        <input type="text" class="form-control" name="religion" id="teacherReligion" required>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                   <div class="col-md-6">
                    <div class="form-group">
                      <label for="teacherProvince">Father Name</label>
                      <input type="text" class="form-control" name="fathers_name" id="fatherName" >
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="teacherProvince">Mother Name</label>
                      <input type="text" class="form-control" name="mother_name" id="motherName" >
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="teacherHouseStreetSitio">House/Street/Sitio/Purok</label>
                      <input type="text" class="form-control" name="house_street_sitio_purok" id="teacherHouseStreetSitio" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="teacherBarangay">Barangay</label>
                      <input type="text" class="form-control" name="barangay" id="teacherBarangay" required>
                    </div>
                  </div>
                </div>
                <!-- Row for Province -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="teacherProvince">Province</label>
                      <input type="text" class="form-control" name="province" id="teacherProvince" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="teacherMunicipalityCity">Municipality/City</label>
                      <input type="text" class="form-control" name="municipality_city" id="teacherMunicipalityCity" required>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                <div class="row">
                 <div class="col-md-6">
                  <div class="form-group">
                    <label for="teacherProvince">Guardian Name</label>
                    <input type="text" class="form-control" name="guardian_name" id="guardian" >
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="teacherProvince">Relationship</label>
                    <input type="text" class="form-control" name="relationship" id="relationship" >
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="teacherProvince">Contact No.</label>
                  <input type="text" class="form-control" name="contact_number" id="contact" >
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.card -->
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
  </form>
</div>
</div>
</div>
<script type="text/javascript">
 // Open the modal with teacher data
  $(document).on('click', '.update-student-btn', function() {
    // Get the teacher's data from the clicked button's data-teacher attribute
    var teacherData = $(this).data('teacher'); // Retrieve the teacher's data from the 
    console.log(teacherData);
    // Populate the form fields in the modal
    $('#teacherUserId').val(teacherData.user_id);
    $('#teacherFirstName').val(teacherData.first_name);
    $('#teacherLastName').val(teacherData.last_name);
    $('#teacherMiddleName').val(teacherData.middle_name);
    $('#teacherEmail').val(teacherData.email);
    $('#teacherSex').val(teacherData.sex);
    $('#teacherBirthDate').val(teacherData.birth_date);
    $('#teacherMotherTongue').val(teacherData.mother_tongue);
    $('#teacherEthnicGroup').val(teacherData.ip_ethnic_group);
    $('#teacherReligion').val(teacherData.religion);
    $('#teacherHouseStreetSitio').val(teacherData.house_street_sitio_purok);
    $('#teacherBarangay').val(teacherData.barangay);
    $('#teacherMunicipalityCity').val(teacherData.municipality_city);
    $('#teacherProvince').val(teacherData.province);
    $('#motherName').val(teacherData.mother_name);
    $('#fatherName').val(teacherData.fathers_name);
    $('#guardian').val(teacherData.guardian_name);
    $('#relationship').val(teacherData.relationship);
    $('#contact').val(teacherData.contact_number);
     $('#userrole').val(teacherData.role_id);
    // Open the modal
    $('#updateTeacherModal').modal('show');
  });
</script>


<div class="modal fade" id="user-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Upload Teachers</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <form id="upload-excel-form" method="POST" action="student-list/upload" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="form-group">
        <label for="student_excel">Upload Excel File</label>
        <input type="file" id="student_excel" name="student_excel" class="form-control" accept=".xlsx, .xls" required>
    </div>
    <!-- Progress Bar -->
    <div class="progress" style="height: 20px;">
        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
<div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="button" id="upload-btn" class="btn btn-primary">Upload Excel</button>
</div>
</form>
</div>
</div>
</div>





<script type="text/javascript">
    document.getElementById('upload-btn').addEventListener('click', function () {
        const form = document.getElementById('upload-excel-form');
        const fileInput = document.getElementById('student_excel');
        const progressBar = document.getElementById('progress-bar');
        if (fileInput.files.length === 0) {
            alert('Please select a file to upload.');
            return;
        }
        const formData = new FormData(form);
    // Create an XMLHttpRequest object
        const xhr = new XMLHttpRequest();
    // Configure the request
        xhr.open('POST', form.action, true);
    // Update the progress bar
        xhr.upload.addEventListener('progress', function (event) {
            if (event.lengthComputable) {
                const percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressBar.setAttribute('aria-valuenow', percentComplete);
                progressBar.innerHTML = percentComplete + '%';
            }
        });
    // Handle the response
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Upload successful!');
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', '0');
                progressBar.innerHTML = '0%';
                $('#user-modal').modal('hide');

                form.reset();
                //location.reload();
            } else {
                alert('An error occurred while uploading the file.');
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', '0');
                progressBar.innerHTML = '0%';
            }
        };
    // Handle errors
        xhr.onerror = function () {
            alert('An error occurred while uploading the file.');
            progressBar.style.width = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
            progressBar.innerHTML = '0%';
        };
    // Send the request
        xhr.send(formData);
    });
</script>









<?php
$content = ob_get_clean();
include 'views/master.php';
?>