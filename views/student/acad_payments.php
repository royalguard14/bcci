<?php
ob_start();
$pageTitle = 'Payment Record'; 


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


<style>
    .fee-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 20px;
    }

    .fee-item {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .fee-item .fee-title {
        font-weight: bold;
        color: #2c3e50;
    }

    .fee-item .fee-amount {
        color: #27ae60;
        font-weight: bold;
    }

    .total-container {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding: 10px;
        background-color: #ecf0f1;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-weight: bold;
    }

    .total-container .total-title {
        color: #e74c3c;
    }

    .total-container .total-amount {
        color: #16a085;
    }
</style>





<div class="row">
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Assessment Details
        </h3>
      </div>
      <div class="card-body">
<div class="fee-container">
    <!-- Tuition Fee -->
    <div class="fee-item">
        <span class="fee-title">Tuition Fee (₱<?php echo number_format($unitFee, 2); ?> x <?php echo $totalUnits; ?> units):</span>
        <span class="fee-amount">₱<?php echo number_format($tuitionFee, 2); ?></span>
    </div>

    <!-- Handling Fee -->
    <div class="fee-item">
        <span class="fee-title">Handling Fee:</span>
        <span class="fee-amount">₱<?php echo number_format($handlingFee, 2); ?></span>
    </div>

    <!-- Laboratory Fee -->
    <div class="fee-item">
        <span class="fee-title">Laboratory Fee:</span>
        <span class="fee-amount">₱<?php echo number_format($laboratoryFee, 2); ?></span>
    </div>

    <!-- Miscellaneous Fee -->
    <div class="fee-item">
        <span class="fee-title">Miscellaneous Fee:</span>
        <span class="fee-amount">₱<?php echo number_format($miscellaneousFee, 2); ?></span>
    </div>

    <!-- Other Fee -->
    <div class="fee-item">
        <span class="fee-title">Other Fee:</span>
        <span class="fee-amount">₱<?php echo number_format($otherFee, 2); ?></span>
    </div>

    <!-- Registration Fee -->
    <div class="fee-item">
        <span class="fee-title">Registration Fee:</span>
        <span class="fee-amount">₱<?php echo number_format($registrationFee, 2); ?></span>
    </div>

    <!-- Total Fee (Bottom Box) -->
    <div class="total-container">
        <span class="total-title">Total Fee:</span>
        <span class="total-amount">₱<?php echo number_format($totalPayment, 2); ?></span>
    </div>

    <!-- Total Payment (Bottom Box) -->
    <div class="total-container">
        <span class="total-title">Total Paid:</span>
        <span class="total-amount">₱<?php echo number_format($binayad['bayadna'], 2); ?></span>
    </div>
</div>
      </div>
    </div>
  </section>
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          

          All Payement History
        </h3>
      </div>
      <div class="card-body">
            <table class="table table-head-fixed text-nowrap" id="example2">
                    <thead>
                        <tr>
                            <th>No.</th>
                          
                            <th>Amount</th>
                            <th>Date</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($payment_log)): ?>
                            <?php foreach ($payment_log as $index => $data) { ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                 
                                  <td><?php echo '₱' . number_format($data['amount'], 2); ?></td>

<td>
    <?php 
    $timestamp = strtotime($data['date_pay']);
    echo date('F j, Y', $timestamp) . ' | ' . date('g:ia', $timestamp);
    ?>
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
include 'views/master-top.php';
?>



            