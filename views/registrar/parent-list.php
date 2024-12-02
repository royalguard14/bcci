<?php
ob_start();


$pageTitle = 'Registrar Management'; 
    // Function to get initials
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
<section class="col-lg-5 connectedSortable">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-plus mr-1"></i>
                Add New Parent
            </h3>
        </div>

        <div class="card-body">
            <!-- Add Parent Form -->
            <form id="add-parent-form" method="POST" action="parents-add" enctype="multipart/form-data">
                <!-- Individual Parent -->
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control">
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="birth_date">Birth Date</label>
                            <input type="date" id="birth_date" name="birth_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="sex">Gender</label>
                            <select name="sex" id="sex" class="form-control" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="con_no">Contact Number</label>
                    <input type="text" id="con_no" name="con_no" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Add Parent</button>
            </form>
        </div>
    </div>
</section>


<section class="col-lg-7 connectedSortable">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">


        Parent's List
      </h3>
    </div>
    <div class="card-body">
      <table id="example3" class="table table-bordered table-hover">
        <thead>
          <tr>
            <th style="text-align: center;">#</th>
            <th style="text-align: center;">Full Name</th>
            <th style="text-align: center;">Information</th>
            <th  style="text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody>
         <?php if(isset($parents)): ?>
          <?php $index = 1; ?>
          <?php foreach ($parents as $data): ?>
            <tr>
             <td style="text-align: center; vertical-align: middle;"><?php echo $index++; ?></td> 
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
    // Display details
              echo "<strong>Address:</strong> <small>" . htmlspecialchars(ucwords(
                $data['house_street_sitio_purok'] . ", " .  
                $data['barangay'] . ", " .  
                $data['municipality_city'] . ", " .  
                getInitials($data['province']))) . "</small><br>";
       
              echo "<strong>Contact Number:</strong> <small>" . htmlspecialchars(ucwords($data['contact_number'])) . "</small><br>";
             
              ?>
            </td>
            <td style="text-align: center; vertical-align: middle;">





<div class="row d-flex justify-content-center">
    <div class="col-12 col-md-6 d-flex">

              <button type="button" class="btn btn-block btn-outline-warning btn-xs update-teacher-btn" 
              data-teacher='<?php echo json_encode([
                "user_id" => $data['user_id'],
                "first_name" => $data['first_name'],
                "last_name" => $data['last_name'],
                "middle_name" => $data['middle_name'],
                "email" => $data['email'],
                "sex" => $data['sex'],
                "birth_date" => $data['birth_date'],
                "contact_number" => $data['contact_number'],
                "religion" => $data['religion'],
                "house_street_sitio_purok" => $data['house_street_sitio_purok'],
                "barangay" => $data['barangay'],
                "municipality_city" => $data['municipality_city'],
                "province" => $data['province'],
                "role_id" => $data['role_id'],

                ]) ?>'
                data-toggle="modal" data-target="#updateTeacherModal">
                Update
              </button>
    </div>
        <div class="col-12 col-md-6 d-flex">
          
 
             <form action="/BCCI/users/delete" method="POST" style="display:inline;">
              <input type="hidden" name="user_id" value="<?php echo $data['user_id']; ?>">
              <input type="hidden" name="paths" value="parents-list">
              <button type="submit" class="btn btn-block btn-outline-danger btn-xs">Delete</button>
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
</section>
</div>








<!-- Modal for updating teacher details -->
<div class="modal fade" id="updateTeacherModal" tabindex="-1" role="dialog" aria-labelledby="updateTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateTeacherModalLabel">Update Parent Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form to update teacher details -->
        <form id="updateTeacherForm" method="POST" action="parents-update">
          <input type="hidden" name="user_id" id="teacherUserId">
          <input type="hidden" name="role_id" id="userrole">
          
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

          <!-- Row for Email, Gender, and Birth Date -->
          <div class="row">

            <input type="hidden" class="form-control" name="email" id="teacherEmail" readonly>

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
          <hr>
          <!-- Row for Mother Tongue, Ethnic Group, and Religion -->
        <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="teacherEthnicGroup">Contact No.</label>
                <input type="text" class="form-control" name="conh_no" id="conh_no" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="teacherReligion">Religion</label>
                <input type="text" class="form-control" name="religion" id="teacherReligion" required>
              </div>
            </div>
        </div>
          <hr>
          <!-- Row for Address Details (House/Street, Barangay, Municipality/City) -->
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
  $(document).on('click', '.update-teacher-btn', function() {
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

    $('#conh_no').val(teacherData.contact_number);
    $('#teacherReligion').val(teacherData.religion);


    $('#teacherHouseStreetSitio').val(teacherData.house_street_sitio_purok);
    $('#teacherBarangay').val(teacherData.barangay);
    $('#teacherMunicipalityCity').val(teacherData.municipality_city);
    $('#teacherProvince').val(teacherData.province);
    $('#userrole').val(teacherData.role_id);
    


    // Open the modal
    $('#updateTeacherModal').modal('show');
  });

</script>

<script type="text/javascript">
  
    $('#example3').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
</script>

<?php
$content = ob_get_clean();
include 'views/master.php';
?>