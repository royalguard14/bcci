<?php
ob_start();
$pageTitle = 'Payment History';  

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
            <div class="card-body">
                <table class="table table-head-fixed text-nowrap" id="example2">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Date</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($payment_log)): ?>
                            <?php foreach ($payment_log as $index => $data) { ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($data['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($data['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($data['date_pay']); ?></td>
                                 
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
