<?php
ob_start();
$pageTitle = 'Subject Management'; 
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
			<section class="col-lg-5 connectedSortable">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus mr-1"></i>
							Publish New Subject
						</h3>
					</div>
	<form action="campus-subjects/create" method="POST">
    <div class="card-body">
        <div class="row">
            <!-- Subject Name -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="subject-name">Subject Name</label>
                    <input id="subject-name" class="form-control form-control-lg" type="text" name="name" placeholder="Enter subject name" required>
                </div>
            </div>
            <!-- Subject Code -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="subject-code">Subject Code</label>
                    <input id="subject-code" class="form-control form-control-lg" type="text" name="code" placeholder="Enter subject code" required>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Description -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="subject-desc">Description</label>
                    <textarea id="subject-desc" class="form-control form-control-lg" name="description" style="height: 10em; resize: none;" placeholder="Enter subject description"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Units Lecture -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="unit-lec">Units (Lecture)</label>
                    <input id="unit-lec" class="form-control form-control-lg" type="number" name="unit_lec" placeholder="Enter lecture units" min="0">
                </div>
            </div>
            <!-- Units Laboratory -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="unit-lab">Units (Laboratory)</label>
                    <input id="unit-lab" class="form-control form-control-lg" type="number" name="unit_lab" placeholder="Enter laboratory units" min="0">
                </div>
            </div>
        </div>

    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-block bg-gradient-primary">Publish</button>
    </div>
</form>

				</div>
			</section>
			<section class="col-lg-7 connectedSortable">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							

							Sales
						</h3>
					</div>
					<div class="card-body">
						<table id="example" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th style="text-align: center;">#</th>
									<th style="text-align: center;">Name</th>
									<th style="text-align: center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($subjects)): ?>
									<?php $index = 1; ?>
									<?php foreach ($subjects as $data): ?>
										<tr>
											<td style="text-align: center;"><?php echo $index++; ?></td> 
											<td><?php echo $data['name']; ?></td>
											<td>


<div class="btn-group " >
                                                   
                                                        <button 
                                                        type="button" 
                                                        class="btn btn-outline-primary btn-block btn-sm m-1" 
                                                        data-toggle="modal" 
                                                        data-target="#modal-default" 
                                                        onclick="openUpdateModal(
                                                            <?php echo $data['id']; ?>, 
                                                            '<?php echo htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'); ?>', 
                                                            '<?php echo htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8'); ?>', 
                                                            '<?php echo htmlspecialchars($data['code'], ENT_QUOTES, 'UTF-8'); ?>', 
                                                            <?php echo $data['unit_lec'] !== null ? $data['unit_lec'] : 'null'; ?>, 
                                                            <?php echo $data['unit_lab'] !== null ? $data['unit_lab'] : 'null'; ?>, 
                                                            '<?php echo htmlspecialchars($data['pre_req'], ENT_QUOTES, 'UTF-8'); ?>'
                                                            )">
                                                            Update
                                                        </button>
                                              
                                              
                                                        <form action="campus-subjects/delete" method="POST" style="display:inline;">
                                                            <input type="hidden" name="sub_id" value="<?php echo $data['id']; ?>">
                                                            <button type="submit"class="btn btn-outline-danger btn-block btn-sm m-1">Drop</button> <!-- Apply btn-sm here -->
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
                <h4 class="modal-title">Update Subject</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/BCCI/campus-subjects/update" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="modal-sub-id" name="id">
                    
                    <!-- First Row: Subject Name and Code -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal-sub-name">Subject Name:</label>
                                <input type="text" class="form-control" id="modal-sub-name" name="name" placeholder="Enter subject name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal-sub-code">Subject Code:</label>
                                <input type="text" class="form-control" id="modal-sub-code" name="code" placeholder="Enter subject code" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Second Row: Description -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="modal-sub-desc">Description:</label>
                                <textarea class="form-control" id="modal-sub-desc" name="description" style="height: 10em; resize: none;" placeholder="Enter subject description"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Third Row: Units (Lecture and Laboratory) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal-unit-lec">Lecture Units:</label>
                                <input type="number" class="form-control" id="modal-unit-lec" name="unit_lec" min="0" placeholder="Enter lecture units">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal-unit-lab">Laboratory Units:</label>
                                <input type="number" class="form-control" id="modal-unit-lab" name="unit_lab" min="0" placeholder="Enter laboratory units">
                            </div>
                        </div>
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
    function openUpdateModal(subId, subName, subDesc, subCode, unitLec, unitLab, preReq) {
        document.getElementById('modal-sub-id').value = subId;
        document.getElementById('modal-sub-name').value = subName;
        document.getElementById('modal-sub-desc').value = subDesc;
        document.getElementById('modal-sub-code').value = subCode;
        document.getElementById('modal-unit-lec').value = unitLec;
        document.getElementById('modal-unit-lab').value = unitLab;
        document.getElementById('modal-pre-req').value = preReq;
    }
</script>


<script type="text/javascript">

    $('#example').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
</script>