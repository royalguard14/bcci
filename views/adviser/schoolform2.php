<?php
ob_start();
$pageTitle = 'School Form 2 - Attendance'; 
?>
<style type="text/css">
/* Hide default radio buttons */
input[type="radio"] {
	display: none;
}
/* Container for the radio button and label */
.radio-container {
	display: inline-flex;
	align-items: center;
	cursor: pointer;
	margin-right: 15px;
	position: relative;
	font-size: 14px;
}
/* Custom radio button appearance */
.custom-radio {
	width: 20px;
	height: 20px;
	border: 2px solid #007bff;
	border-radius: 50%;
	display: inline-block;
	margin-right: 8px;
	position: relative;
	background-color: white;
	transition: 0.3s;
}
/* Checked state for the custom radio button */
input[type="radio"]:checked + .custom-radio {
	background-color: #007bff;
	border-color: #007bff;
}
input[type="radio"]:checked + .custom-radio::before {
	content: "";
	width: 10px;
	height: 10px;
	background-color: white;
	border-radius: 50%;
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
}
</style>
<div class="row">
	<section class="col-lg-12 connectedSortable">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Attendance Form - Advisory Class
				</h3>
				<div class="card-tools">
					<!-- Search bar for filtering -->
					<div class="form-group">
						<input 
						type="text" 
						id="searchInput" 
						class="form-control" 
						placeholder="Search by LRN, Full Name, or Status"
						>
					</div>
				</div>
			</div>
			<div class="card-body">
				<!-- Form to mark attendance -->
				<!-- Table to display students with checkboxes for attendance -->
				<table id="attendanceTable" class="table table-bordered table-striped table-head-fixed text-nowrap">
					
					<thead>
						<tr>
							<th>#</th>
							<th>LRN</th>
							<th>Full Name</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($attendanceData as $index => $student) : ?>
							<tr>
								<td><?= $index + 1 ?></td>
								<td><?= $student['lrn'] ?></td>
								<td><?= $student['fullname'] ?></td>
								<td>
									<label class="radio-container">
										<input type="radio" name="attendance[<?= $student['profile_id'] ?>][status]" value="P" data-profile-id="<?= $student['profile_id'] ?>" data-eh-id="<?= $student['ehID'] ?>" <?= $student['status'] == 'P' ? 'checked' : '' ?>>
										<span class="custom-radio"></span> Present
									</label>
									<label class="radio-container">
										<input type="radio" name="attendance[<?= $student['profile_id'] ?>][status]" value="A" data-profile-id="<?= $student['profile_id'] ?>" data-eh-id="<?= $student['ehID'] ?>" <?= $student['status'] == 'A' ? 'checked' : '' ?>>
										<span class="custom-radio"></span> Absent
									</label>
									<label class="radio-container">
										<input type="radio" name="attendance[<?= $student['profile_id'] ?>][status]" value="T" data-profile-id="<?= $student['profile_id'] ?>" data-eh-id="<?= $student['ehID'] ?>" <?= $student['status'] == 'T' ? 'checked' : '' ?>>
										<span class="custom-radio"></span> Tardy
									</label>
									<label class="radio-container">
										<input type="radio" name="attendance[<?= $student['profile_id'] ?>][status]" value="E" data-profile-id="<?= $student['profile_id'] ?>" data-eh-id="<?= $student['ehID'] ?>" <?= $student['status'] == 'E' ? 'checked' : '' ?>>
										<span class="custom-radio"></span> Excused
									</label>
									<!-- Remarks field -->
									<input type="text" name="attendance[<?= $student['profile_id'] ?>][remarks]" value="<?= $student['remarks'] ?>" placeholder="Remarks" class="form-control remark-input" data-profile-id="<?= $student['profile_id'] ?>" data-eh-id="<?= $student['ehID'] ?>" <?= $student['status'] == 'E' ? '' : 'style="display:none;"' ?> />
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</section>
</div>
<script type="text/javascript">
	document.getElementById('searchInput').addEventListener('input', function () {
		const searchTerm = this.value.toLowerCase();
		const rows = document.querySelectorAll('#attendanceTable tbody tr');
		rows.forEach(row => {
			const lrn = row.cells[1].textContent.toLowerCase();
			const fullName = row.cells[2].textContent.toLowerCase();
			const status = row.cells[3].textContent.toLowerCase();
    // Check if the search term matches any column in the row
			if (lrn.includes(searchTerm) || fullName.includes(searchTerm) || status.includes(searchTerm)) {
      row.style.display = ''; // Show the row
  } else {
      row.style.display = 'none'; // Hide the row
  }
});
	});
</script>
<script>
	$(document).ready(function () {
    // When any radio button is clicked
		$('input[type="radio"][name^="attendance"]').on('change', function () {
        var profileId = $(this).data('profile-id');  // Get the profile_id
        var status = $(this).val();  // Get the status value (P, A, T, E)
        var ehId = $(this).data('eh-id');
        // Show or hide the remarks input based on the status selected
        var remarksInput = $(this).closest('td').find('.remark-input');
        if (status === 'E') {
        	remarksInput.show();
        } else {
        	remarksInput.hide();
        }
        // Send the data via AJAX
        $.ajax({
            url: 'attendance/submit',  // URL to handle the update
            type: 'POST',
            data: {
            	profile_id: profileId,
            	status: status,
            	eid: ehId
            },
            success: function (response) {
            	console.log('Attendance status updated');
                // You can also handle the response here (e.g., show a success message)
            },
            error: function () {
            	console.log('Error updating attendance');
            }
        });
    });
		$('input[type="text"][name^="attendance"][name$="[remarks]"]').on('input', function () {
        var profileId = $(this).data('profile-id');  // Get the profile_id
        var remarks = $(this).val();  // Get the remark value
        var ehId = $(this).data('eh-id');  // Get the eh_id for the student
        // Send the updated remark via AJAX to the server
        $.ajax({
            url: 'attendance/updateRemark',  // URL to handle the update for remarks
            type: 'POST',
            data: {
            	profile_id: profileId,
            	remarks: remarks,
            	eid: ehId
            },
            success: function (response) {
            	console.log('Remark updated');
                // Optionally, you can show a success message or handle the response
            },
            error: function () {
            	console.log('Error updating remark');
            }
        });
    });
	});
</script>
<?php
$content = ob_get_clean();
include 'views/master.php';
?>