<?php
ob_start();
$pageTitle = 'Register New Student'; 

// Function to display toast messages
function displayToastMessage($session_key, $toast_class, $title) {
    if (isset($_SESSION[$session_key])) {
        $message = $_SESSION[$session_key];
        unset($_SESSION[$session_key]);
        echo "<script type='text/javascript'>

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

if (isset($_SESSION['info'])) {
    displayToastMessage('info', 'bg-info', 'Information');
}

if (isset($_SESSION['success'])) {
    displayToastMessage('success', 'bg-success', 'Success');
}

if (isset($_SESSION['Register_code'])) {
    $message = $_SESSION['Register_code'];
    unset($_SESSION['Register_code']);

    echo "
    <script src='https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js'></script>
    <script type='text/javascript'>
        $(window).on('load', function() {
            $('#modal-sm').modal('show');
            // Generate the QR code
            QRCode.toCanvas(document.getElementById('qrcode'), '$message', {
                width: 150,
                margin: 2
            }, function (error) {
                if (error) console.error(error);
            });
        });
    </script>

    <div class='modal fade' id='modal-sm'>
        <div class='modal-dialog modal-sm'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h4 class='modal-title'>Registration Ticket</h4>
              <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
              </button>
            </div>
            <div class='modal-body text-center'> <!-- Center the content -->
             
              <div class='d-flex justify-content-center'>
                <canvas id='qrcode'></canvas> <!-- QR Code will be rendered here -->

              </div>
              <br>
               <p>Registration Code: <strong>$message</strong></p>
              <p>Please save this QR code and present it to the registrar.</p>
            </div>
            <div class='modal-footer justify-content-between'>
              <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
          </div>
        </div>
    </div>
    ";
}







?>

<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Register New Student</h3>
      </div>
      <div class="card-body">
        <form action="enroll" method="POST" enctype="multipart/form-data">
          <div class="row">
            <!-- Profile Photo -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="photo_path">Profile Photo</label>
                <input type="file" class="form-control" id="photo_path" name="photo_path"  accept="image/png, image/gif, image/jpeg" >
              </div>
            </div>
            <!-- Profile ID -->
     
          </div>

          <div class="row">
            <!-- Name Fields -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name">
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Sex and Birth Date -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="sex">Sex</label>
                <select class="form-control" id="sex" name="sex" required>
                  <option selected disabled>Select Gender</option>
                  <option value="M">Male</option>
                  <option value="F">Female</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="birth_date">Birth Date</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Address Fields -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="house_street_sitio_purok">House/Street/Sitio/Purok</label>
                <input type="text" class="form-control" id="house_street_sitio_purok" name="house_street_sitio_purok" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="barangay">Barangay</label>
                <input type="text" class="form-control" id="barangay" name="barangay" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="municipality_city">Municipality/City</label>
                <input type="text" class="form-control" id="municipality_city" name="municipality_city" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="province">Province</label>
                <input type="text" class="form-control" id="province" name="province" required>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Contact Number -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" required>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Submit Button -->
            <div class="col-md-12 text-right">
              <button type="submit" class="btn btn-primary">Enroll Now!!</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>


<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>
