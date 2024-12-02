<?php
ob_start();
$pageTitle = 'Roles Management'; 
?>
<div class="row">
  <section class="col-lg-5 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-plus mr-1"></i>
          Register New Role
        </h3>
      </div>
      <form action="roles/create" method="POST">
        <div class="card-body">
          <input class="form-control form-control-lg" type="text" placeholder="Role Name" name="role_name">
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-block bg-gradient-primary">Register</button>
        </div>
      </form>
    </div>
  </section>
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-users mr-1"></i>
          Role List
        </h3>
      </div>
      <div class="card-body">
       <table id="example2" class="table table-bordered table-hover">
        <thead>
          <tr>
            <th style="text-align: center;">#</th>
            <th style="text-align: center;">Name</th>
            <th colspan="4" style="text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody>
         <?php if(isset($roles)): ?>
          <?php $index = 1; ?>
          <?php foreach ($roles as $role): ?>
            <tr>
             <td style="text-align: center;"><?php echo $index++; ?></td> 
             <td style="text-align: center;"><?php echo $role['role_name']; ?></td>
             <td>
              <button type="button" 
              class="btn btn-block btn-outline-primary btn-xs" 
              data-toggle="modal" 
              data-target="#modal-default"
              onclick="openUpdateModal(<?php echo $role['role_id']; ?>, '<?php echo $role['role_name']; ?>')">
              Update
            </button>
          </td>
          <td>
            <button type="button" class="btn btn-block btn-outline-primary btn-xs" onclick="showUsersModal(<?php echo $role['role_id']; ?>)">
              Accounts
            </button>
          </td>
          <td>

              <button type="button" 
              class="btn btn-block btn-outline-primary btn-xs btn-permission"
              onclick="openPermissions('<?php echo $role['role_id']; ?>')"
              >
              Permission
            </button>

        


          </td>
          <td>
            <form action="roles/delete" method="POST" style="display:inline;">
              <input type="hidden" name="role_id" value="<?php echo $role['role_id']; ?>">
              <button type="submit" class="btn btn-block btn-outline-danger btn-xs">Drop</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
  <tfoot>
    <tr>
     <th style="text-align: center;">#</th>
     <th style="text-align: center;">Name</th>
     <th colspan="4" style="text-align: center;">Action</th>
   </tr>
 </tfoot>
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
        <h4 class="modal-title">Update Role</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/BCCI/roles/update" method="POST">
        <div class="modal-body">
          <input type="hidden" id="modal-role-id" name="role_id">
          <div class="form-group">
            <label for="modal-role-name">Role Name:</label>
            <input type="text" class="form-control" id="modal-role-name" name="role_name" required>
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
<div class="modal fade" id="usersModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="usersModalLabel">Users with Role</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="usersList"></ul>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->



<div class="modal fade" id="usersModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="usersModalLabel">Users with Role</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="usersList"></ul>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->




<div class="modal fade" id="permissionsModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="usersModalLabel">Permissions</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form id="permissionsForm">
          <input type="hidden" id="perm_role_id">
          <div id="permissionsList"></div>
        </form>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
  function openUpdateModal(roleId, roleName) {
    document.getElementById('modal-role-id').value = roleId;
    document.getElementById('modal-role-name').value = roleName;
  }
</script>
<script>
  function showUsersModal(roleId) {
    fetch('roles/getUsersByRoleId', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'role_id=' + roleId
    })
    .then(response => response.json())
    .then(data => {
      const usersList = document.getElementById('usersList');
      usersList.innerHTML = '';

      // Check if the data contains an error or is an empty array
      if (data.error || data.length === 0) {
        usersList.innerHTML = `<h2>No Assigned Account Yet!</h2>`;
      } else {
        data.forEach(user => {
          const listItem = document.createElement('li');
          listItem.textContent = user.full_name;
          usersList.appendChild(listItem);
        });
      }

      // Show the modal
      const usersModal = new bootstrap.Modal(document.getElementById('usersModal'));
      usersModal.show();
    })
    .catch(error => {
      console.error('Error:', error);
      const usersList = document.getElementById('usersList');
      usersList.innerHTML = `<li>Error fetching users. Please try again later.</li>`;
    });
  }
</script>


<script type="text/javascript">
  function openPermissions(roleId) {
    $('#perm_role_id').val(roleId);
    
    $.ajax({
      url: 'role-permission',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({role_id: roleId}),
      dataType: 'json',
      success: function(response) {
        console.log('Response:', response);
        
        if (response.success) {
          var permissionsList = $('#permissionsList');
          permissionsList.empty();

          response.permissions.forEach(function(permission) {
            if (permission && permission.permission_id && permission.permission_name) { 
              var isChecked = response.assigned_permissions.includes(permission.permission_id.toString()) ? 'checked' : '';
              permissionsList.append(`
                <div class="form-check">
                  <input class="form-check-input permission-checkbox" type="checkbox" value="${permission.permission_id}" id="perm${permission.permission_id}" ${isChecked}>
                  <label class="form-check-label" for="perm${permission.permission_id}">${permission.permission_name}  (${permission.permission_id})</label>
                </div>
              `);
            }
          });

          // Add event listener for check/uncheck actions
          $('.permission-checkbox').change(function() {
            updateRolePermissions(roleId);
          });

          $('#permissionsModal').modal('show');
        } else {
          showToast('Error', 'Failed to load permissions.');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        showToast('Error', 'An error occurred while fetching permissions.');
      }
    });
  }

  // Function to update permissions in the database
  function updateRolePermissions(roleId) {
    // Collect all checked permissions
    var selectedPermissions = [];
    $('.permission-checkbox:checked').each(function() {
      selectedPermissions.push($(this).val());
    });

    // Send AJAX request to update permissions in the roles table
    $.ajax({
      url: 'update-role-permissions',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ role_id: roleId, permissions: selectedPermissions }),
      success: function(response) {
           showToast('Success', 'Permissions updated successfully.');
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        showToast('Error', 'An error occurred while updating permissions.');
      }
    });
  }

  // Function to display toast notifications
  function showToast(title, message) {
    $(document).Toasts('create', {
      title: title,
      body: message,
      autohide: true,
      delay: 2000,
      class: title === 'Success' ? 'bg-success' : 'bg-danger'
    });
  }
</script>

