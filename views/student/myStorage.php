<?php
ob_start();
$pageTitle = 'Storage Management'; 
if (isset($_SESSION['error'])) {
  $error_message = $_SESSION['error'];
  unset($_SESSION['error']);
  echo "<script type='text/javascript'>
  document.addEventListener('DOMContentLoaded', function() {
    $('.preloader').hide();
    $(document).Toasts('create', {
      class: 'bg-danger',
      title: 'Error',
      autohide: true,
      delay: 3000,
      body: '" . addslashes($error_message) . "' }
      );
      });
      </script>";
    }
    if (isset($_SESSION['success'])) {
      $success_message = $_SESSION['success'];
      unset($_SESSION['success']);
      echo "<script type='text/javascript'>
      document.addEventListener('DOMContentLoaded', function() {
        $('.preloader').hide();
        $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Success',
          autohide: true,
          delay: 3000,
          body: '" . addslashes($success_message) . "' }
          );
          });
          </script>";
        }
        ?>
        <div class="row">
          <section class="col-lg-4 connectedSortable">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  Upload Document Here
                </h3>
              </div>
              <div class="card-body">
                <form action="uploadDocs" method="POST" enctype="multipart/form-data">
                  <input type="file" id="document" name="document" class="form-control " required>
                 
              </div>
              <div class="card-footer">
                 <button type="submit" class="btn btn-block bg-gradient-primary">Upload</button>
         
                </form>
              </div>
            </div>
          </section>
          <section class="col-lg-8 connectedSortable" style="max-height: 500px; overflow-y: auto;">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Your Documents</h3>
                <!-- Search Box -->
                <input type="text" id="searchInput" class="form-control" placeholder="Search documents..." onkeyup="searchDocuments()" style="margin-top: 10px;">
              </div>
              <div class="card-body">
                <?php
                if (!empty($files)) {
    echo "<div class='row'>";  // Added a row to arrange the cards
    foreach ($files as $file) {
      $filePath = $uploadDir . $file;
      $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        // Truncate long file names
      $displayName = strlen($file) > 20 ? substr($file, 0, 17) . '...' : $file;
        // Determine icon based on file type
      $icon = match ($fileExtension) {
        'pdf' => 'fas fa-file-pdf',
        'doc', 'docx' => 'fas fa-file-word',
        'xls', 'xlsx' => 'fas fa-file-excel',
        'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
        'txt' => 'fas fa-file-alt',
        default => 'fas fa-file',
      };
        echo "<div class='col-md-4 mb-4 file-item'>"; // Create 3 items per row
        echo "<div class='card'>";  // Start card
        // Card title with file name
        echo "<h5 class='card-header' title='" . htmlspecialchars($file) . "'>" . htmlspecialchars($displayName) . "</h5>";
        echo "<div class='card-body text-center'>";  // Card body with text-center for the icon
        // Card icon
        echo "<i class='$icon fa-3x'></i>";
        echo "</div>"; // End card body
        // Card footer with actions
        echo "<div class='card-footer text-center'>";
        echo "<a href='$filePath' download class='btn btn-success btn-sm mr-2'>Download</a>";
        echo "<form action='deleteFile' method='POST' style='display:inline-block;'>
        <input type='hidden' name='file' value='" . htmlspecialchars($file) . "'>
        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
        </form>";
        echo "</div>"; // End card footer
        echo "</div>"; // End card
        echo "</div>"; // End column
      }
    echo "</div>";  // End row
  } else {
    echo "<p>No files uploaded yet.</p>";
  }
  ?>
</div>
</div>
</section>
</div>

<script>
// JavaScript function for search filter
function searchDocuments() {
  var input, filter, cards, card, title, i, txtValue;
  input = document.getElementById('searchInput');
  filter = input.value.toLowerCase();
  cards = document.getElementsByClassName('file-item');
  
  for (i = 0; i < cards.length; i++) {
    card = cards[i];
    title = card.getElementsByClassName('card-header')[0];
    if (title) {
      txtValue = title.textContent || title.innerText;
      if (txtValue.toLowerCase().indexOf(filter) > -1) {
        card.style.display = "";
      } else {
        card.style.display = "none";
      }
    }
  }
}
</script>

<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>
