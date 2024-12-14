<?php
ob_start();
$pageTitle = 'Enrollies Management'; 
?>
<?php
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
    if (isset($_SESSION['error'])) {
      displayToastMessage('error', 'bg-danger', 'Error');
    }
    if (isset($_SESSION['info'])) {
      displayToastMessage('info', 'bg-info', 'Information');
    }
    if (isset($_SESSION['success'])) {
      displayToastMessage('success', 'bg-success', 'Success');
    }
    ?>
    <div class="row">
      <section class="col-lg-12 connectedSortable">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Enrollies Status
            </h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Full Name</th>
                  <th>Course</th>
                  <th>Status</th>
                  <th>Semester</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($evaluation as $index => $data) { ?>
                  <tr data-widget="expandable-table" aria-expanded="false">
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars(ucwords($data['fullname'])); ?></td>
                    <td><?php echo htmlspecialchars($data['dcode']); ?></td>
                    <td><?php echo htmlspecialchars($data['status']); ?></td>
                    <td><?php  echo htmlspecialchars($data['semester_id'] . ' | ' . $data['acads_year']); ?> </td>
                <td>
<?php if ($data['status'] == "Evaluation") : ?>
    <!-- Form for status 'Evaluation', allows user to proceed to payment -->
    <form action="toPayment" method="POST" style="display:inline;">
        <input type="hidden" name="ehID" value="<?php echo $data['ehID']; ?>">
        <button type="submit" class="btn btn-block btn-outline-success btn-xs">Proceed to Payment</button>
    </form>

<?php elseif ($data['status'] == "Pending Payment") : ?>
    <!-- Message when the status is 'Pending Payment' -->
    <p>Pending Payment</p>

<?php elseif ($data['status'] == "Paid") : ?>
    <!-- Message when the status is 'Pending Payment' -->
     <form action="toPaymentConfirm" method="POST" style="display:inline;">
        <input type="hidden" name="ehID" value="<?php echo $data['ehID']; ?>">
        <button type="submit" class="btn btn-block btn-outline-success btn-xs">Confirm Payment</button>
    </form>

<?php elseif ($data['status'] == "ENROLLED") : ?>

    <!-- Button to generate Certificate of Employment -->
   <button class="btn btn-block btn-outline-primary btn-xs" onclick="generateCOE(<?php echo $data['ehID']; ?>)">Generate COE</button>
    <div id="coeDetails"></div> <!-- This will hold the COE details -->


<script>
function generateCOE(ehID) {
    // Perform an AJAX request to fetch the COE details
    $.ajax({
        url: 'getDetailCOE', // URL to fetch the COE details
        method: 'GET',
        data: { ehID: ehID },
        success: function(response) {
            // Assuming the response contains the full COE layout
            $('#coeDetails').html(response); // Insert the COE details into the div
            
            // Call print function
            printCOE();
        },
        error: function() {
            alert('Error fetching COE details');
        }
    });
}

function printCOE() {
    var content = document.getElementById("coeDetails").innerHTML;
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Certificate of Employment</title>');
    printWindow.document.write('<style>body{font-family: Arial, sans-serif; padding: 20px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>


    
<?php else : ?>
    <!-- Message for any other status -->
    <p>No Action</p>
<?php endif; ?>

</td>

                  </tr>
                  <tr class="expandable-body">
                    <td colspan="6">
                     <ul>
                      <?php 
                      $subjects = explode(', ', $data['subject_names']);
                      foreach ($subjects as $subject) {
                        echo "<li>" . htmlspecialchars($subject) . "</li>";
                      }
                      ?>
                    </ul>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
  <script type="text/javascript">
    function manageSubject(subjects) {
      let parsedSubjects = typeof subjects === "string" ? JSON.parse(subjects) : subjects;
      console.log("Subjects Taken:", parsedSubjects);
      let subjectList = parsedSubjects.map(subject => `Subject ID: ${subject.subjectId}, Schedule IDs: ${subject.scheduleIds.join(', ')}`).join('\n');
      alert("Subjects Taken:\n" + subjectList);
    }
  </script>
  <?php
  $content = ob_get_clean();
  include 'views/master.php';
?>