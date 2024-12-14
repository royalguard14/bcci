<?php
ob_start();
$pageTitle = 'Registrar Dashboard';
?>




<div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $pendingCount ?></h3>

                <p>Pending Students</p>
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
                <h3><?= $acceptedCount ?></h3>

                <p>Accepted Students</p>
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
                <h3><?= $enrollmentStats['total_enrollments'] ?></h3>

                <p>Total Enrollments</p>
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
                <h3><?= $enrollmentStats['pending_payment'] ?></h3>

                <p>Pending Payments</p>
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



  <!-- Right Column -->
  <section class="col-lg-6 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Recent Payments
        </h3>
      </div>
      <div class="card-body">
        <table class="table table-head-fixed text-nowrap" id="example2">
          <thead>
            <tr>
              <th>No.</th>
              <th>Name</th>
              <th>Amount</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($payment_log)): ?>
              <?php foreach ($payment_log as $index => $data): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($data['fullname']) ?></td>
                  <td>₱<?= number_format($data['amount'], 2) ?></td>
                  <td><?= htmlspecialchars($data['date_pay']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4">No payment records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>



  <!-- Left Column -->
  <section class="col-lg-6 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Enrollment Statistics
        </h3>
      </div>
      <div class="card-body">








  

<div class="row">
    <!-- Existing Graphs: Enrollment and Payment Trends -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Enrollment Trends (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="enrollmentTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Trends (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="paymentTrendsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- New Graphs: Accepted and Pending Students -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Accepted Students (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="acceptedStudentsTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Students (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="pendingStudentsTrendsChart"></canvas>
            </div>
        </div>
    </div>
</div>

















      </div>
    </div>
  </section>


</div>


<!-- AdminLTE App -->
<script src="assets/js/chart.js"></script>
<script>
    // Data for Enrollment Trends Graph (Bar Chart)
    var enrollmentLabels = <?php echo json_encode(array_column($enrollmentTrends, 'month')); ?>;
    var enrollmentData = <?php echo json_encode(array_column($enrollmentTrends, 'enrollments')); ?>;

    // Data for Payment Trends Graph (Bar Chart)
    var paymentLabels = <?php echo json_encode(array_column($paymentTrends, 'month')); ?>;
    var paymentData = <?php echo json_encode(array_column($paymentTrends, 'total_payments')); ?>;

    // Data for Accepted Students Trends (Bar Chart)
    var acceptedLabels = <?php echo json_encode(array_column($acceptedStudentsTrends, 'month')); ?>;
    var acceptedData = <?php echo json_encode(array_column($acceptedStudentsTrends, 'accepted_students')); ?>;

    // Data for Pending Students Trends (Bar Chart)
    var pendingLabels = <?php echo json_encode(array_column($pendingStudentsTrends, 'month')); ?>;
    var pendingData = <?php echo json_encode(array_column($pendingStudentsTrends, 'pending_students')); ?>;

    // Enrollment Trends Chart
    var ctxEnrollment = document.getElementById('enrollmentTrendsChart').getContext('2d');
    var enrollmentChart = new Chart(ctxEnrollment, {
        type: 'bar',
        data: {
            labels: enrollmentLabels,
            datasets: [{
                label: 'Enrollments',
                data: enrollmentData,
                backgroundColor: 'rgba(60, 141, 188, 0.9)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Payment Trends Chart
    var ctxPayment = document.getElementById('paymentTrendsChart').getContext('2d');
    var paymentChart = new Chart(ctxPayment, {
        type: 'bar',
        data: {
            labels: paymentLabels,
            datasets: [{
                label: 'Payments (₱)',
                data: paymentData,
                backgroundColor: 'rgba(210, 214, 222, 0.9)',
                borderColor: 'rgba(210, 214, 222, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Accepted Students Trends Chart
    var ctxAccepted = document.getElementById('acceptedStudentsTrendsChart').getContext('2d');
    var acceptedChart = new Chart(ctxAccepted, {
        type: 'bar',
        data: {
            labels: acceptedLabels,
            datasets: [{
                label: 'Accepted Students',
                data: acceptedData,
                backgroundColor: 'rgba(92, 184, 92, 0.9)',
                borderColor: 'rgba(92, 184, 92, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Pending Students Trends Chart
    var ctxPending = document.getElementById('pendingStudentsTrendsChart').getContext('2d');
    var pendingChart = new Chart(ctxPending, {
        type: 'bar',
        data: {
            labels: pendingLabels,
            datasets: [{
                label: 'Pending Students',
                data: pendingData,
                backgroundColor: 'rgba(255, 159, 64, 0.9)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
include 'views/master.php';
?>
