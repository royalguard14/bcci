<?php
ob_start();
$pageTitle = 'Adviser Dashboard'; 
?>
<div class="row">
	<div class="col-lg-3 col-6">
		<!-- small box -->
		<div class="small-box bg-info">
			<div class="inner">
				<h3><?php echo $maleCount; ?></h3>
				<p>Total Male Enrolled</p>
			</div>
			<div class="icon">
				<i class="ion ion-bag"></i>
			</div>
		</div>
	</div>
	<!-- ./col -->
	<div class="col-lg-3 col-6">
		<!-- small box -->
		<div class="small-box bg-success">
			<div class="inner">
				<h3><?php echo $femaleCount; ?></h3>
				<p>Total Female Enrolled</p>
			</div>
			<div class="icon">
				<i class="ion ion-stats-bars"></i>
			</div>
		</div>
	</div>
	<!-- ./col -->
	<div class="col-lg-3 col-6">
		<!-- small box -->
		<div class="small-box bg-warning">
			<div class="inner">
				<h3><?php echo htmlspecialchars(count($presentStudents)+count($tardyStudents)); ?></h3>
				<p>Total Present Today</p>
			</div>
			<div class="icon">
				<i class="ion ion-person-add"></i>
			</div>
		</div>
	</div>
	<!-- ./col -->
	<div class="col-lg-3 col-6">
		<!-- small box -->
		<div class="small-box bg-danger">
			<div class="inner">
				<h3><?php echo htmlspecialchars(count($absentStudents)+count($exuseStudents)); ?></h3>
				<p>Total Absent Today</p>
			</div>
			<div class="icon">
				<i class="ion ion-pie-graph"></i>
			</div>
		</div>
	</div>
	<!-- ./col -->
</div>
<!-- /.row -->
<div class="row">
	<section class="col-lg-6 connectedSortable">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Top 10 Students Quarterly
				</h3>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Rank</th>
							<th>Name</th>
							<th>Average Grade</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($top10Students as $index => $student): ?>
							<tr>
								<td><?php echo $index + 1; ?></td>
								<td><?php echo htmlspecialchars($student['fullname']); ?></td>
								<td><?php echo number_format($student['average_grade'], 2); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Top Subject Performers Quarterly
				</h3> 
			</div>
			<div class="card-body">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Award</th>
							<th>Top Student</th>
							<th>Grade</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($topStudentsPerSubject as $subject): ?>
							<tr>
								<td>Best in <?php echo htmlspecialchars($subject['subject_name']); ?></td>
								<td><?php echo htmlspecialchars($subject['fullname']); ?></td>
								<td><?php echo number_format($subject['grade'], 2); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
	<section class="col-lg-6 connectedSortable">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Students with Birthdays (This Month)
				</h3>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>Full Name</th>
							<th>Birth Date</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($studentsWithBirthday)): ?>
							<?php foreach ($studentsWithBirthday as $index => $student): ?>
								<tr>
									<td><?php echo $index + 1; ?></td>
									<td><?php echo htmlspecialchars($student['fullname']); ?></td>
									<td><?php echo date('F d, Y', strtotime($student['birth_date'])); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="3" class="text-center">No students with birthdays this month.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					Attendance Today
				</h3>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Attendance Status</th>
							<th>Students</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><strong>Present</strong></td>
							<td>
								<?php if (!empty($presentStudents)): ?>
									<ul class="list-unstyled mb-0">
										<?php foreach ($presentStudents as $student): ?>
											<li><?php echo htmlspecialchars($student['fullname']); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php else: ?>
									<em>No students present</em>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><strong>Absent</strong></td>
							<td>
								<?php if (!empty($absentStudents)): ?>
									<ul class="list-unstyled mb-0">
										<?php foreach ($absentStudents as $student): ?>
											<li><?php echo htmlspecialchars($student['fullname']); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php else: ?>
									<em>No students absent</em>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><strong>Tardy</strong></td>
							<td>
								<?php if (!empty($tardyStudents)): ?>
									<ul class="list-unstyled mb-0">
										<?php foreach ($tardyStudents as $student): ?>
											<li><?php echo htmlspecialchars($student['fullname']); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php else: ?>
									<em>No students tardy</em>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><strong>Excused</strong></td>
							<td>
								<?php if (!empty($exuseStudents)): ?>
									<ul class="list-unstyled mb-0">
										<?php foreach ($exuseStudents as $student): ?>
											<li><?php echo htmlspecialchars($student['fullname']); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php else: ?>
									<em>No students excused</em>
								<?php endif; ?>
							</td>
						</tr>
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