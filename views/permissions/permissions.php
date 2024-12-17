<?php
ob_start();
$pageTitle = 'Permission Management'; 
?>
<div class="row">
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-plus mr-1"></i>
          Register New Permission
        </h3>
      </div>
      <form action="permissions/create" method="POST">
        <div class="card-body">
          <input class="form-control form-control-lg" type="text" placeholder="Permission Name" name="permission_name">
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-block bg-gradient-primary">Grant</button>
        </div>
      </form>
    </div>
  </section>
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-users mr-1"></i>
          Permission List
        </h3>
      </div>
      <div class="card-body">
       <table id="perm" class="table table-bordered table-hover ">
        <thead>
          <tr>
            <th style="text-align: center;">#</th>
            <th style="text-align: center;">Name</th>
            <th style="text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody>
         <?php if(isset($permissions)): ?>
          <?php $index = 1; ?>
          <?php foreach ($permissions as $permission): ?>
            <tr>
             <td style="text-align: center;"><?php echo $index++; ?></td> 
             <td ><?php echo $permission['permission_name']; ?></td>
             <td style="text-align:center;">
              <div class="btn-group">
              <button type="button" 
              class="btn btn-block btn-outline-primary mr-1" 
              data-toggle="modal" 
              data-target="#modal-default"
              onclick="openUpdateModal(<?php echo $permission['permission_id']; ?>, '<?php echo $permission['permission_name']; ?>')">
              Update
            </button>
     
            <form action="permissions/delete" method="POST" style="display:inline;">
              <input type="hidden" name="permission_id" value="<?php echo $permission['permission_id']; ?>">
              <button type="submit" class="btn btn-block btn-outline-danger ml-1">Drop</button>
            </form>
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
<?php
$content = ob_get_clean();
include 'views/master.php';
?>

<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Update Permission</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/BCCI/permissions/update" method="POST">
        <div class="modal-body">
          <input type="hidden" id="modal-permission-id" name="permission_id">
          <div class="form-group">
            <label for="modal-permission-name">Permission Name:</label>
            <input type="text" class="form-control" id="modal-permission-name" name="permission_name" required>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
  function openUpdateModal(permID, permName) {
    document.getElementById('modal-permission-id').value = permID;
    document.getElementById('modal-permission-name').value = permName;
  }
</script>

<script type="text/javascript">
    $('#perm').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": false,
    "info": true,
    "autoWidth": true,
    "responsive": true,
  });
</script>