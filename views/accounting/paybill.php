<?php
ob_start();
$pageTitle = 'Accounting Manager'; 


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
          

Pending Payment        </h3>
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
        <?php if (isset($payee) && count($payee) > 0): ?>
            <?php foreach ($payee as $index => $data) { ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars(ucwords($data['fullname'])); ?></td>
                    <td>
                        <!-- Action: Proceed to payment -->
              <button type="button" class="btn btn-primary btn-xs" onclick="payEnrollmentFee('<?php echo $data['ehID']; ?>')">
    Proceed to Payment
</button>

                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No payees found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


      </div>
    </div>
  </section>
</div>


<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="bayadnapo" method="POST">
                    <input type="hidden" name="ehID" id="ehID">
          

                    <div class="form-group">
                        <label for="amount">Enter Payment Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function payEnrollmentFee(ehID) {
 
       
        $('#ehID').val(ehID); // Set the value of the hidden field for ehID
 

        // Show the modal after populating the hidden fields
        $('#paymentModal').modal('show');
    }

    // Event listener for the modal trigger button
    $('#paymentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var ehID = button.data('ehid'); // Extract the ehID
  
    });
</script>



<?php
$content = ob_get_clean();
include 'views/master.php';
?>



<table class="table table-head-fixed text-nowrap" id="example2">

  <thead>
    <tr>
      <th>No.</th>
      <th>Program</th>
      <th>Manage</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($department as $index => $data) { ?>
      <tr>
        <td><?php echo $index + 1; ?></td>
        <td><?php echo htmlspecialchars($data['code']); ?></td>
        <td>

        </td>

      </tr>
    <?php } ?>
  </tbody>
</table>