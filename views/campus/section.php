<?php
ob_start();
$pageTitle = 'Section Management'; 
?>
<?php
if (isset($_SESSION['error'])) {
	$error_message = $_SESSION['error'];
	unset($_SESSION['error']);
	echo "<script type='text/javascript'>
	document.querySelector('.preloader').style.display = 'none';
	document.addEventListener('DOMContentLoaded', function() {
		$(document).Toasts('create', {
			class: 'bg-danger',
			title: 'Error',
			autohide: true,
			delay: 3000,
			body: '" . addslashes($error_message) . "'
			});
			});
			</script>";
		}
		?>
		<div class="row">
			<section class="col-lg-5 connectedSortable">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus mr-1"></i>
							Add new Section
						</h3>
					</div>
					<form action="campus-sections/create" method="POST">
						<div class="card-body">
							<div class="form-group">
								<label for="exampleInputEmail1">Section Name</label>
								<input class="form-control form-control-lg" type="text" name="section_name">
							</div>
							<div class="form-group">
								<label for="exampleInputPassword1">Session</label>
								<select class="form-control form-control-lg" name="section_sched" required>
									<option selected disabled>Select Session</option>
									<option value="Morning">Morning</option>
									<option value="Afternoon">Afternoon</option>
									<option value="Whole Day">Whole Day</option>
								</select>
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
							Section List
						</h3>
					</div>
					<div class="card-body">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th style="text-align: center;">#</th>
									<th style="text-align: center;">Name</th>
									<th style="text-align: center;">Adviser</th>
									<th style="text-align: center;">Schedule</th>
									<th style="text-align: center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($sections)): ?>
									<?php $index = 1; ?>
									<?php foreach ($sections as $data): ?>
										<tr>
											<td style="text-align: center;"><?php echo $index++; ?></td> 
											<td style="text-align: center;"><?php echo $data['section_name']; ?></td>
											<td style="text-align: center;">
												<form action="campus-sections/update-adviser" method="POST">
													<input type="hidden" name="section_id" value="<?php echo $data['id']; ?>">
													<select class="form-control" name="adviser_id" required onchange="this.form.submit()">
														<option selected disabled>Select Adviser</option>
														<?php foreach ($teachers as $teacher): ?>
															<option value="<?php echo $teacher['user_id']; ?>"
																<?php echo $teacher['user_id'] == $data['adviser_id'] ? 'selected' : ''; ?>>
																<?php echo htmlspecialchars($teacher['name']); ?>
															</option>
														<?php endforeach; ?>
													</select>
												</form>
											</td>
											<td style="text-align: center;"><?php echo $data['daytime']; ?></td>
											<td>
												<div class="row d-flex justify-content-center">
													<div class="col-12 col-md-6 d-flex">
														<button type="button" 
														class="btn btn-outline-primary btn-xs flex-fill" 
														data-toggle="modal" 
														data-target="#modal-default"
														onclick="openUpdateModal(<?php echo $data['id']; ?>, '<?php echo $data['section_name']; ?>', '<?php echo $data['daytime']; ?>')">
														Update
													</button>
												</div>
												<div class="col-12 col-md-6 d-flex">
													<form action="campus-sections/delete" method="POST" style="display:inline;">
														<input type="hidden" name="section_id" value="<?php echo $data['id']; ?>">
														<button type="submit" class="btn btn-outline-danger btn-xs flex-fill">Drop</button>
													</form>
												</div>
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
					<h4 class="modal-title">Update Section</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="/BCCI/campus-sections/update" method="POST">
					<div class="modal-body">
						<input type="hidden" id="modal-section-id" name="sec_id">
						<div class="form-group">
							<label for="modal-role-name">Section:</label>
							<input type="text" class="form-control" id="modal-section-name" name="sec_name" required>
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">Session</label>
							<select class="form-control form-control-lg" name="sec_sched" id="modal-section-session" required>
								<option selected disabled>Select Session</option>
								<option value="Morning">Morning</option>
								<option value="Afternoon">Afternoon</option>
								<option value="Whole Day">Whole Day</option>
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
		function openUpdateModal(secId, secName, sessions) {
			document.getElementById('modal-section-id').value = secId;
			document.getElementById('modal-section-name').value = secName;
			document.getElementById('modal-section-session').value = sessions;
		}
	</script>