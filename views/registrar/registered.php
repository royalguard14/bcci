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
						<table id="example4" class="table table-bordered table-hover">
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
											<td style="text-align: center;"><?php echo htmlspecialchars($data['username']) ?></td> 

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
													<input type="hidden" name="user_id" value="<?php echo $data['id']; ?>">
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
												<table id="example3" class="table table-bordered table-hover">
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
	
													<button type="submit" class="btn btn-block btn-outline-info btn-xs view-student-btn" 
							 data-info='<?php echo json_encode($data); ?>'

                >View</button>
											
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


	<script type="text/javascript">
		    $('#example4').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
	</script>


<script type="text/javascript">
    // Open the modal with student data
    $(document).on('click', '.view-student-btn', function () {
        // Get the student's data from the clicked button's data-info attribute
        var studentData = $(this).data('info'); // Retrieve the student's data
        console.log(studentData);

        // Convert birth date from MM/DD/YYYY to Month Day, Year format
        var birthDate = studentData.birth_date;
        var formattedBirthDate = formatDate(birthDate);

        // Populate the form fields in the modal
        $('#last_name').val(studentData.last_name);
        $('#first_name').val(studentData.first_name);
        $('#middle_name').val(studentData.middle_name || ''); // Default to empty if no middle name
        $('#sex').val(studentData.sex);
        
        // Set the formatted birthdate in a separate span or div (for display purpose)
        $('#formatted_birth_date').text(formattedBirthDate);

        // Set the actual date value for form submission (in YYYY-MM-DD format)
        $('#birth_date').val(studentData.birth_date); 

        $('#house_street_sitio_purok').val(studentData.house_street_sitio_purok);
        $('#barangay').val(studentData.barangay);
        $('#municipality_city').val(studentData.municipality_city);
        $('#province').val(studentData.province);
        $('#contact_number').val(studentData.contact_number);

        // Open the modal
        $('#studentview').modal('show');
    });

    // Function to convert MM/DD/YYYY to "Month Day, Year" format
    function formatDate(date) {
        var months = [
            "January", "February", "March", "April", "May", "June", "July", 
            "August", "September", "October", "November", "December"
        ];
        var parts = date.split('/');
        var month = months[parseInt(parts[0], 10) - 1]; // Get month name
        var day = parts[1]; // Day
        var year = parts[2]; // Year
        return month + ' ' + day + ', ' + year; // Return formatted date
    }
</script>


<div class="modal fade" id="studentview">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Student Information</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Name Fields -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Sex and Birth Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sex">Sex</label>
                            <select class="form-control" id="sex" name="sex" disabled>
                                <option selected disabled>Select Gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birth_date">Birth Date</label>
                            <span id="formatted_birth_date" class="form-control"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Address Fields -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="house_street_sitio_purok">House/Street/Sitio/Purok</label>
                            <input type="text" class="form-control" id="house_street_sitio_purok" name="house_street_sitio_purok" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <input type="text" class="form-control" id="barangay" name="barangay" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="municipality_city">Municipality/City</label>
                            <input type="text" class="form-control" id="municipality_city" name="municipality_city" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" class="form-control" id="province" name="province" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Contact Number -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
