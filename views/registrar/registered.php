<?php
ob_start();
$pageTitle = 'Enrollment Management'; 
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
			<section class="col-lg-6 connectedSortable">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">


							Pending Student Account
						</h3>
					</div>
					<div class="card-body">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th style="text-align: center;">#</th>
									<th style="text-align: center;">Name</th>
									<th style="text-align: center;">Date Register</th>
									<th style="text-align: center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($pending_students)): ?>
									<?php $index = 1; ?>
									<?php foreach ($pending_students as $data): ?>
										<tr>
											<td style="text-align: center;"><?php echo $index++; ?></td> 
											<td style="text-align: center;">
												<?php 
												echo htmlspecialchars(
													ucwords(
														$data['last_name'] . ', ' . 
														$data['first_name'] . ' ' . 
														(isset($data['middle_name']) && !empty($data['middle_name']) ? substr($data['middle_name'], 0, 1) . '.' : '')
													)
												); 
												?>
											</td>
											<td style="text-align: center;"><?php echo $data['date_register']; ?></td>

											<td>
												<form action="pending_student_procced" method="POST" style="display:inline;">
													<input type="hidden" name="sy_id" value="<?php echo $data['id']; ?>">
													<button type="submit" class="btn btn-block btn-outline-info btn-xs">Confirm</button>
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
			<section class="col-lg-6 connectedSortable">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">


							Confirm Student Account
						</h3>
					</div>
					<div class="card-body">
												<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th style="text-align: center;">#</th>
									<th style="text-align: center;">Name</th>
									<th style="text-align: center;">Date Register</th>
									<th style="text-align: center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($accepted_students)): ?>
									<?php $index = 1; ?>
									<?php foreach ($accepted_students as $data): ?>
										<tr>
											<td style="text-align: center;"><?php echo $index++; ?></td> 
<td style="text-align: center;">
    <?php 
    echo htmlspecialchars(
        !empty($data['last_name']) && !empty($data['first_name']) 
            ? ucwords(
                $data['last_name'] . ', ' . 
                $data['first_name'] . ' ' . 
                (isset($data['middle_name']) && !empty($data['middle_name']) ? substr($data['middle_name'], 0, 1) . '.' : '')
            ) 
            : 'N/A'
    ); 
    ?>
</td>

											<td style="text-align: center;"><?php echo $data['date_register']; ?></td>

											<td>
												<form action="pending_student_procced" method="POST" style="display:inline;">
													<input type="hidden" name="sy_id" value="<?php echo $data['id']; ?>">
													<button type="submit" class="btn btn-block btn-outline-info btn-xs">Confirm</button>
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