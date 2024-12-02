<?php
ob_start();
$pageTitle = 'Grade Level Management'; 
?>
<div class="row">
	<section class="col-lg-5 connectedSortable">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-plus mr-1"></i>
					Enroll Grade
				</h3>
			</div>
			<form action="campus-grades/create" method="POST">
				<div class="card-body">
					<input class="form-control form-control-lg" type="text" placeholder="Grade Level" name="grade_name">
				</div>
				<div class="card-footer">
					<button type="submit" class="btn btn-block bg-gradient-primary">Enroll</button>
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
							<th colspan="4" style="text-align: center;">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($grade_level)): ?>
							<?php $index = 1; ?>
							<?php foreach ($grade_level as $gl): ?>
								<tr>
									<td style="text-align: center;"><?php echo $index++; ?></td> 
									<td style="text-align: center;"><?php echo $gl['level']; ?></td>
									<td>
										<button type="button" 
										class="btn btn-block btn-outline-primary btn-xs" 
										data-toggle="modal" 
										data-target="#modal-default"
										onclick="openUpdateModal(<?php echo $gl['id']; ?>, '<?php echo $gl['level']; ?>')">
										Update
									</button>
								</td>
								<td>
									<form action="campus-grades/delete" method="POST" style="display:inline;">
										<input type="hidden" name="grade_id" value="<?php echo $gl['id']; ?>">
										<button type="submit" class="btn btn-block btn-outline-danger btn-xs">Drop</button>
									</form>
								</td>



								<td>




              <button type="button" 
             class="btn btn-block btn-outline-info btn-xs"
              onclick="openSections('<?php echo $gl['id']; ?>')"
              >
              Sections
            </button>




								</td>

																<td>
									
							
       <button type="button" 
             class="btn btn-block btn-outline-warning btn-xs"
              onclick="openSubjects('<?php echo $gl['id']; ?>')"
              >
              Subjects
            </button>





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
				<h4 class="modal-title">Update Grade Level</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/BCCI/campus-grades/update" method="POST">
				<div class="modal-body">
					<input type="hidden" id="modal-grade-id" name="grade_id">
					<div class="form-group">
						<label for="modal-role-name">Grade Level:</label>
						<input type="text" class="form-control" id="modal-grade-name" name="grade_name" required>
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
	function openUpdateModal(gradeId, gradeName) {
		document.getElementById('modal-grade-id').value = gradeId;
		document.getElementById('modal-grade-name').value = gradeName;
	}
</script>


<div class="modal fade" id="sectionsModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="usersModalLabel">Manage Sections</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form id="subjectsForm">
          <input type="hidden" id="sub_grade_id">
          <div id="sectionList"></div>
        </form>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<div class="modal fade" id="subjectsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Manage Subjects</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="subjectsForm">
                    <input type="hidden" id="sub_grade_id">
                    <div id="subjectList"></div>
                </form>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
  function openSections(glid) {
    $('#sub_grade_id').val(glid);
    
    $.ajax({
      url: 'grade-sections',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ glId: glid }),
      dataType: 'json',
      success: function(response) {
        console.log('Response:', response);

        if (response.success) {
          var sectionList = $('#sectionList');
          sectionList.empty();

          response.sections.forEach(function(section) {
            if (section && section.id && section.section_name) { 
              var isChecked = response.assigned_section.includes(section.id.toString()) ? 'checked' : '';
              sectionList.append(`
                <div class="form-check">
                  <input class="form-check-input permission-checkbox" type="checkbox" value="${section.id}" id="perm${section.id}" ${isChecked}>
                  <label class="form-check-label" for="perm${section.id}">${section.section_name}</label>
                </div>
              `);
            }
          });

          // Event listener for check/uncheck actions
          $('.permission-checkbox').change(function() {
            updateGradeLevelSection(glid);
          });

          $('#sectionsModal').modal('show');
        } else {
          showToast('Error', 'Failed to load sections.');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        showToast('Error', 'An error occurred while fetching sections.');
      }
    });
  }

  // Function to update selected sections in the database
  function updateGradeLevelSection(glid) {
    var selectedSections = [];
    $('.permission-checkbox:checked').each(function() {
      selectedSections.push($(this).val());
    });

    $.ajax({
      url: 'update-grade-sections',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ glid: glid, sections: selectedSections }),
      success: function(response) {
     showToast('Success', 'Sections updated successfully.');
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        showToast('Error', 'An error occurred while updating sections.');
      }
    });
  }

  // Function to display toast notifications
  function showToast(title, message) {
    $(document).Toasts('create', {
      title: title,
      body: message,
      autohide: true,
      delay: 2000
    });
  }
</script>

<script type="text/javascript">
	function openSubjects(glid) {
    $('#sub_grade_id').val(glid);
    
    $.ajax({
        url: 'grade-subjects',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ glId: glid }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var subjectList = $('#subjectList');
                subjectList.empty();

                response.subjects.forEach(function(subject) {
                    var isChecked = response.assigned_subjects.includes(subject.id.toString()) ? 'checked' : '';
                    subjectList.append(`
                        <div class="form-check">
                            <input class="form-check-input subject-checkbox" type="checkbox" value="${subject.id}" id="subject${subject.id}" ${isChecked}>
                            <label class="form-check-label" for="subject${subject.id}">${subject.name}</label>
                        </div>
                    `);
                });

                $('.subject-checkbox').change(function() {
                    updateGradeLevelSubject(glid);
                });

                $('#subjectsModal').modal('show');
            } else {
                showToast('Error', 'Failed to load subjects.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showToast('Error', 'An error occurred while fetching subjects.');
        }
    });
}

function updateGradeLevelSubject(glid) {
    var selectedSubjects = [];
    $('.subject-checkbox:checked').each(function() {
        selectedSubjects.push($(this).val());
    });

    $.ajax({
        url: 'update-grade-subjects',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ glid: glid, subjects: selectedSubjects }),
        success: function(response) {
        	 showToast('Success', response.message);
       
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showToast('Error', 'An error occurred while updating subjects.');
        }
    });
}


</script>
