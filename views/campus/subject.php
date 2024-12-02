<?php
ob_start();
$pageTitle = 'Subject Management'; 
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
							Publish New Subject
						</h3>
					</div>
					<form action="campus-subjects/create" method="POST">
						<div class="card-body">
							<div class="form-group">
								<label for="exampleInputEmail1">Subject Name</label>
								<input class="form-control form-control-lg" type="text" name="sub_name">
							</div>
							<div class="form-group">
								<label for="exampleInputEmail1">Description</label>
								<textarea class="form-control form-control-lg" name="sub_desc" style="height: 10em; resize: none;"></textarea>
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
						<table id="example2" class="table table-bordered table-hover">
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
											<td style="text-align: center;"><?php echo $data['name']; ?></td>
											<td>
												<div class="row d-flex justify-content-center">
													<div class="col-md-6">
														<button type="button" 
														class="btn btn-outline-primary btn-block btn-sm" 
														data-toggle="modal" 
														data-target="#modal-default"
														onclick="openUpdateModal(<?php echo $data['id']; ?>, '<?php echo $data['name']; ?>', '<?php echo $data['description']; ?>')">
														Update
													</button>
												</div>
												<div class="col-md-6">
													<form action="campus-subjects/delete" method="POST" style="display:inline;">
														<input type="hidden" name="sub_id" value="<?php echo $data['id']; ?>">
														<button type="submit"class="btn btn-outline-danger btn-block btn-sm">Drop</button> <!-- Apply btn-sm here -->
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
					<h4 class="modal-title">Update Subject</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="/BCCI/campus-subjects/update" method="POST">
					<div class="modal-body">
						<input type="hidden" id="modal-sub-id" name="sub_id">
						<div class="form-group">
							<label for="modal-role-name">Subject Name:</label>
							<input type="text" class="form-control" id="modal-sub-name" name="sub_name" required>
						</div>
						<textarea class="form-control form-control-lg" name="sub_desc" style="height: 10em; resize: none;" id="modal-sub-desc" ></textarea>
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
		function openUpdateModal(subId, subName, subDesc) {
			document.getElementById('modal-sub-id').value = subId;
			document.getElementById('modal-sub-name').value = subName;
			document.getElementById('modal-sub-desc').value = subDesc;
		}
	</script>