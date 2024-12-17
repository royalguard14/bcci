<?php
ob_start();
$pageTitle = $this->deanDept; 


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
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $totalStudent ?></h3>
                <p>Total Student</p>
              </div>
              <div class="icon">
                
              </div>

            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $totalInstructor ?></h3>

                <p>Total Faculty</p>
              </div>
              <div class="icon">
                
              </div>

            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $totalGraduate ?></h3>

                <p>Number of Graduates</p>
              </div>
              <div class="icon">
                
              </div>

            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $totalSubject ?></h3>

                <p>Total Subject</p>
              </div>
              <div class="icon">
                
              </div>

            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->

<section class="col-lg-7 connectedSortable">
  <!-- Single Pie Chart Card -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-chart-pie mr-1"></i>
        Students vs Graduates
      </h3>
    </div><!-- /.card-header -->

    <div class="card-body">
      <!-- Pie Chart Container -->
      <div style="position: relative; height: 300px;">
        <canvas id="studentPieChart" height="300"></canvas>
      </div>
    </div><!-- /.card-body -->
  </div><!-- /.card -->
</section>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Replace these values with the data from your backend
const totalStudents = <?= $totalStudent ?>;  // Dynamic value for total students
const totalGraduates = <?= $totalGraduate ?>;  // Dynamic value for total graduates

// PIE CHART
var ctx = document.getElementById('studentPieChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Total Students', 'Total Graduates'],
        datasets: [{
            data: [totalStudents, totalGraduates],
            backgroundColor: ['#36A2EB', '#FF6384']  // Blue for students, Red for graduates
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'  // Position of the legend
            }
        }
    }
});
</script>

          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-5 connectedSortable">

<!-- Bar Chart for Top 5 Subjects -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-chart-bar mr-1"></i>
        Top 5 Subjects with Most Students
      </h3>
    </div><!-- /.card-header -->

    <div class="card-body">
      <!-- Bar Chart Container -->
      <div style="position: relative; height: 300px;">
        <canvas id="topSubjectsChart" height="300"></canvas>
      </div>
    </div><!-- /.card-body -->
  </div><!-- /.card -->
          </section>
          <!-- right col -->
        </div>
 



<?php
$content = ob_get_clean();
include 'views/master.php';
?>


