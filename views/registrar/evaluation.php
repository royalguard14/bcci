<?php
ob_start();
$pageTitle = 'Enrollies Management'; 
?>
<?php

$semesterMap = [
    1 => "I - 1st Sem",
    2 => "I- 2nd Sem",
    3 => "II - 1st Sem",
    4 => "II - 2nd Sem",
    5 => "III - 1st Sem",
    6 => "III - 2nd Sem",
    7 => "IV - 1st Sem",
    8 => "IV - 2nd Sem",

];

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
    if (isset($_SESSION['error'])) {
      displayToastMessage('error', 'bg-danger', 'Error');
    }
    if (isset($_SESSION['info'])) {
      displayToastMessage('info', 'bg-info', 'Information');
    }
    if (isset($_SESSION['success'])) {
      displayToastMessage('success', 'bg-success', 'Success');
    }
    ?>
    <div class="row">
      <section class="col-lg-12 connectedSortable">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Enrollies Status
            </h3>

          </div>

          <div class="card-body">
 <!-- Search Bar Above the Table -->
<div class="search-container" style="margin-bottom: 20px;">
    <input type="text" id="tableSearch" class="form-control" placeholder="Search by Full Name, Course, etc...">
</div>

<!-- Your Table -->
<table id="enrol" class="table table-bordered table-hover ">
    <thead>
        <tr>
            <th>No.</th>
            <th>Full Name</th>
            <th>Course</th>
            <th>Status</th>
            <th>Semester</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($evaluation as $index => $data) { ?>
        <tr data-widget="expandable-table" aria-expanded="false">
            <td><?php echo $index + 1; ?></td>
            <td><?php echo htmlspecialchars(ucwords($data['fullname'])); ?></td>
            <td><?php echo htmlspecialchars($data['dcode']); ?></td>
            <td><?php echo htmlspecialchars($data['status']); ?></td>
            <td>
                <?php  
                $semesterLabel = isset($semesterMap[$data['semester_id']]) ? $semesterMap[$data['semester_id']] : "Unknown Semester";
                echo htmlspecialchars($semesterLabel . ' | ' . $data['acads_year']); 
                ?> 
            </td>
            <td>
                <?php if ($data['status'] == "Evaluation") : ?>
                    <form action="toPayment" method="POST" style="display:inline;">
                        <input type="hidden" name="ehID" value="<?php echo $data['ehID']; ?>">
                        <button type="submit" class="btn btn-block btn-outline-success btn-xs">Proceed to Payment</button>
                    </form>
                <?php elseif ($data['status'] == "Pending Payment") : ?>
                    <p>Pending Payment</p>
                <?php elseif ($data['status'] == "Paid") : ?>
                    <form action="toPaymentConfirm" method="POST" style="display:inline;">
                        <input type="hidden" name="ehID" value="<?php echo $data['ehID']; ?>">
                        <button type="submit" class="btn btn-block btn-outline-success btn-xs">Confirm Payment</button>
                    </form>
                <?php elseif ($data['status'] == "ENROLLED") : ?>
                    <button class="btn btn-block btn-outline-primary btn-xs" onclick="generateCOE(<?php echo $data['ehID']; ?>)">Generate COE</button>
                    <div id="coeDetails"></div> <!-- This will hold the COE details -->
                <script>
                    function generateCOE(ehID) {
                        $.ajax({
                            url: 'getDetailCOE', 
                            method: 'GET',
                            data: { ehID: ehID },
                            success: function(response) {
                                var printWindow = window.open('', '', 'height=1248,width=816');
printWindow.document.write(`
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Registration</title>
       <style>
        /* General Table Styling */
        table {
            width: 100%;
            border-collapse: collapse; /* Removes extra space between cells */
            font-family: Arial, sans-serif;
            font-size: 12px; /* Compact font size */
        }

        table th, table td {
            border: 1px solid #333; /* Solid black border */
            padding: 4px 8px; /* Minimal padding for tight fit */
            text-align: center;
            vertical-align: middle;
        }

        table th {
            background-color: #f4f4f4; /* Light gray background for headers */
            font-weight: bold;
        }

        table td {
            background-color: #fff; /* White background for clean look */
        }

        /* Alternate row colors for readability */
        table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        /* No extra space around table */
        body {
            margin: 0;
            padding: 0;
        }

        .table-container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }
        
        .logo img {
            width: 100px; /* Adjust logo size */
            height: auto;
        }
    </style>
</head>
<body>
<section class="header">
        <table class="header-table">
            <tr>
                <td class="certificate-title">
                    <h1>Certificate of Registration</h1>
                </td>
                <td class="logo">
                    <img src="assets/butuan-city-collges-logo.png" alt="School Logo">
                </td>
                <td class="school-info" style="text-align: left;    vertical-align: top;
                ">
                <p><strong>Butuan City Colleges, Inc.</strong></p>
                <p>BCCI Annex, Chou Building, Montilla Blvd</p>
                <p>Butuan City</p>
                <p>Phone: +639 53 996 5290</p>
            </td>            </tr>
        </table>
    </section>
`);

















                   
                                printWindow.document.write(response); 
                                printWindow.document.write('</body></html>');
                                printWindow.document.close();
                                printWindow.print();
                            },
                            error: function() {
                                alert('Error fetching COE details');
                            }
                        });
                    }
                </script>
                <?php else : ?>
                    <p>No Action</p>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="expandable-body">
            <td colspan="6">
                <ul>
                    <?php 
                    $subjects = explode(', ', $data['subject_names']);
                    foreach ($subjects as $subject) {
                        echo "<li>" . htmlspecialchars($subject) . "</li>";
                    }
                    ?>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    // Initialize DataTables with a search functionality
    var table = $('#enrol').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,  // Enable search functionality
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

    // Add custom search functionality to filter the table based on the input from the search bar
    $('#tableSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>


        </div>
      </div>
    </section>
  </div>
  <script type="text/javascript">
    function manageSubject(subjects) {
      let parsedSubjects = typeof subjects === "string" ? JSON.parse(subjects) : subjects;
      console.log("Subjects Taken:", parsedSubjects);
      let subjectList = parsedSubjects.map(subject => `Subject ID: ${subject.subjectId}, Schedule IDs: ${subject.scheduleIds.join(', ')}`).join('\n');
      alert("Subjects Taken:\n" + subjectList);
    }
  </script>



  <?php
  $content = ob_get_clean();
  include 'views/master.php';
?>