<?php
ob_start();
$pageTitle = 'User Management'; 
?>
<div class="row">
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-user-plus mr-1"></i>
          Register New User
        </h3>
      </div>
      <form action="/BCCI/account/create" method="POST">
        <div class="card-body">
                 <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter email" autocomplete="new-username">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="new-password">
                  </div>


                  <div class="form-group">
                    <label for="exampleInputPassword1">User Role</label>
                    <select class="form-control form-control-lg" name="role_id" required>
                      <option selected disabled>Select Role</option>
                      <?php 

                          foreach ($roles as $role) {
        echo "<option value='{$role['role_id']}'>{$role['role_name']}</option>";
    }
                       ?>
                    </select>
                  </div>


        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-block bg-gradient-primary">Create User</button>
        </div>
      </form>
    </div>
  </section>
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-users mr-1"></i>
          User List
        </h3>
      </div>
      <div class="card-body">
       <table id="example3" class="table table-bordered table-hover">
        <thead>
          <tr>
            <th style="text-align: center;">#</th>
            <th style="text-align: center;">Username</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody>
         <?php if(isset($users)): ?>
          <?php $index = 1; ?>
          <?php foreach ($users as $user): ?>
            <tr>
             <td style="text-align: center;"><?php echo $index++; ?></td> 
             <td style="text-align: center;"><?php echo $user['username']; ?></td>
             <td style="text-align: center;"><?php echo $user['isActive'] ? 'Active' : 'Inactive'; ?></td>
<td>
  <button type="button" 
          class="btn btn-block btn-outline-primary btn-xs" 
          data-toggle="modal" 
          data-target="#user-modal"
          onclick="openUserUpdateModal(<?php echo $user['user_id']; ?>, '<?php echo $user['username']; ?>', <?php echo $user['role_id']; ?>, <?php echo $user['isActive']; ?>)">
    Update
  </button>

            <form action="/BCCI/account/delete" method="POST" style="display:inline;">
              <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
              <button type="submit" class="btn btn-block btn-outline-danger btn-xs">Delete</button>
            </form>
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



<div class="modal fade" id="user-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Update User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/BCCI/account/update" method="POST">
        <div class="modal-body">
          <input type="hidden" id="modal-user-id" name="user_id">
          
          <!-- Username Field -->
          <div class="form-group">
            <label for="modal-username">Username:</label>
            <input type="text" class="form-control" id="modal-username" name="username" >
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="modal-password">New Password (leave blank if no change):</label>
            <input type="password" class="form-control" id="modal-password" name="password">
          </div>

          <!-- Role Selection -->
          <div class="form-group">
            <label for="modal-role">Role:</label>
            <select class="form-control" id="modal-role" name="role_id" required>
              <option selected disabled>Select Role</option>
              <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role['role_id']; ?>">
                  <?php echo htmlspecialchars($role['role_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Active Status -->
          <div class="form-group">
            <label for="modal-active-status">Active Status:</label>
            <select class="form-control" id="modal-active-status" name="isActive" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
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
  function openUserUpdateModal(userID, username, roleID, isActive) {
    document.getElementById('modal-user-id').value = userID;
    document.getElementById('modal-username').value = username;
    document.getElementById('modal-password').value = '';  // Reset password field

    // Set the role in the dropdown
    const roleSelect = document.getElementById('modal-role');
    for (let i = 0; i < roleSelect.options.length; i++) {
      if (roleSelect.options[i].value == roleID) {
        roleSelect.options[i].selected = true;
        break;
      }
    }

    // Set the active status in the dropdown
    const activeStatusSelect = document.getElementById('modal-active-status');
    activeStatusSelect.value = isActive;
  }
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