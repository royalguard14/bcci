<?php
ob_start();
$pageTitle = 'Payment Settings'; 



?>

<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Payment Settings</h3>
      </div>
      <div class="card-body">
<form action="updatePaymentSetting" method="POST">
    <div class="row">
        <!-- Column 1 -->
        <div class="col-6">
            <div class="form-group">
                <label for="unit_fee">Unit Fee</label>
                <input class="form-control" type="number" name="unit_fee" id="unit_fee" value="<?php echo htmlspecialchars($paymentSettings['unit_fee']); ?>" required>
            </div>
            <div class="form-group">
                <label for="handling_fee">Handling Fee</label>
                <input class="form-control" type="number" name="handling_fee" id="handling_fee" value="<?php echo htmlspecialchars($paymentSettings['handling_fee']); ?>" required>
            </div>
            <div class="form-group">
                <label for="laboratory_fee">Laboratory Fee</label>
                <input class="form-control" type="number" name="laboratory_fee" id="laboratory_fee" value="<?php echo htmlspecialchars($paymentSettings['laboratory_fee']); ?>" required>
            </div>
        </div>
        
        <!-- Column 2 -->
        <div class="col-6">
            <div class="form-group">
                <label for="miscellaneous_fee">Miscellaneous Fee</label>
                <input class="form-control" type="number" name="miscellaneous_fee" id="miscellaneous_fee" value="<?php echo htmlspecialchars($paymentSettings['miscellaneous_fee']); ?>" required>
            </div>
            <div class="form-group">
                <label for="other_fee">Other Fee</label>
                <input class="form-control" type="number" name="other_fee" id="other_fee" value="<?php echo htmlspecialchars($paymentSettings['other_fee']); ?>" required>
            </div>
            <div class="form-group">
                <label for="registration_fee">Registration Fee</label>
                <input class="form-control" type="number" name="registration_fee" id="registration_fee" value="<?php echo htmlspecialchars($paymentSettings['registration_fee']); ?>" required>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>



      </div>
    </div>
  </section>
</div>

<?php
$content = ob_get_clean();
include 'views/master.php';
?>
