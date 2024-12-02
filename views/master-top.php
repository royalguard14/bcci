<?php 
// Get the current page from the URL
$current_page = basename($_SERVER['REQUEST_URI'], ".php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Top Navigation</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>



<style type="text/css">
  .drawer {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    width: 300px;
    background: #fff;
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
    transform: translateX(100%);
    transition: transform 0.3s;
    padding: 1rem !important;
    z-index: 9999; /* Ensure the drawer is in front of all other elements */
  }
  .drawer.open {
    transform: translateX(0);
  }
  .chat-windows {
    position: fixed;
    bottom: 0;
    right: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 9998; /* Make sure chat windows are below the drawer */
  }
  .direct-chat {
    width: 100%;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  }
  .input-group input {
    border-radius: 20px;
  }
  .input-group-append button {
    border-radius: 20px;
    background-color: #007bff;
    color: white;
  }
</style>












</head>
<body class="hold-transition layout-top-nav">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
      <div class="container">
        <a href="assets/index3.html" class="navbar-brand">
          <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">AdminLTE 3</span>
        </a>
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
          <!-- Left navbar links -->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a href="dashboard" class="nav-link <?= ($current_page == 'dashboard') ? 'active' : ''; ?> ">Home</a>
            </li>
            <li class="nav-item">
              <a href="learners-profile" class="nav-link <?= ($current_page == 'learners-profile') ? 'active' : ''; ?>">Profile</a>
            </li>
            <li class="nav-item dropdown">
              <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle <?= (in_array($current_page, ['learners-attendance', 'learners-enrollment-history', 'learners-academic-history', 'learners-storage'])) ? 'active' : ''; ?>
              ">My Space</a>
              <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="learners-attendance" class="dropdown-item <?= ($current_page == 'learners-attendance') ? 'active' : ''; ?>">Attendance Record</a></li>
                <li class="dropdown-divider"></li>
                <li><a href="learners-enrollment-history" class="dropdown-item <?= ($current_page == 'learners-enrollment-history') ? 'active' : ''; ?>">Enrollement History</a></li>
                <li class="dropdown-divider"></li>
                <li><a href="learners-academic-history" class="dropdown-item <?= ($current_page == 'learners-academic-history') ? 'active' : ''; ?>">Academic Record</a></li>
                <li class="dropdown-divider"></li>
                <li><a href="learners-storage" class="dropdown-item <?= ($current_page == 'learners-storage') ? 'active' : ''; ?>">Storage</a></li>
              </ul>
            </li>
          </ul>
          <!-- SEARCH FORM -->
        </div>
        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <a class="nav-link" data-toggle="dropdown" href="#" id="open-drawer">
            <i class="fas fa-comments"></i>
          </a>
          <li class="nav-item">
            <a href="logout" class="nav-link text-red ">logout</a>
          </li>
          <form class="form-inline ml-0 ml-md-3">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </form>
        </ul>
      </div>
    </nav>
    <!-- /.navbar -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><?php echo isset($pageTitle) ? $pageTitle : 'Management'; ?></h1>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->
      <!-- Main content -->
      <div class="content">
        <div class="container">
          <?php echo isset($content) ? $content : "<div class='alert alert-danger'>No content available.</div>"; ?>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
        Anything you want
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->
  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>
  <template id="chat-window-template">
    <div class="col-md-3">
      <!-- DIRECT CHAT PRIMARY -->
      <div class="card card-primary card-outline direct-chat direct-chat-primary">
        <div class="card-header">
          <h3 class="card-title"></h3>
          <div class="card-tools">
            <!-- <span title="3 New Messages" class="badge bg-primary">3</span> -->
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool close-chat" title="Close Chat">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <!-- Conversations will be dynamically added here -->
          <div class="direct-chat-messages">
            <!-- Dynamic chat messages loop -->
          </div>
          <!--/.direct-chat-messages-->
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <form method="POST" action="#" class="chat-form" id="message-form">
            <div class="input-group">
              <input type="hidden" name="receiver_id" class="receiver-id">
              <input type="text" name="message" class="chat-input form-control" placeholder="Type Message ..." >
              <span class="input-group-append">
                <button type="submit" class="btn btn-primary">Send</button>
              </span>
            </div>
          </form>
        </div>
        <!-- /.card-footer-->
      </div>
      <!--/.direct-chat -->
    </div>
    <!-- /.col -->
  </template>
  <!-- Drawer for Classmates/Teachers -->
  <div id="user-drawer" class="drawer control-sidebar-content">
    <div class="drawer-header">
     
      <div class="row">


<div class="col-6"> <h5>Contact List</h5></div>
<div class="col-6"> <button id="close-drawer" class="close-btn">X</button></div>


        
     
      </div>








    </div>
    <div class="drawer-body">

    </div>
  </div>
  <!-- Pop-up Chat Windows -->
  <div id="chat-windows" class="chat-windows">
    <!-- Dynamically populated chat windows -->
  </div>
  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
      const drawer = document.getElementById('user-drawer');
      const openDrawerBtn = document.getElementById('open-drawer');
      const closeDrawerBtn = document.getElementById('close-drawer');
      const userList = document.getElementById('user-list');
       const adviserList = document.getElementById('teacher-list');
      const chatWindows = document.getElementById('chat-windows');
      const chatWindowTemplate = document.getElementById('chat-window-template');
  // Toggle drawer and fetch data
      openDrawerBtn.addEventListener('click', () => {
        drawer.classList.add('open');
    fetchContacts(); // Fetch the list of available contacts
  });
      closeDrawerBtn.addEventListener('click', () => drawer.classList.remove('open'));
  // Fetch user list using $.ajax
function fetchContacts() {
    $.ajax({
        url: 'fetch-chat-available', // Replace with your server endpoint
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            // Clear existing content
            $('#teacher-list').empty();
            $('#user-list').empty();

            // Remove sections to ensure they are recreated only when necessary
            $('#adviser-section').remove();
            $('#classmate-section').remove();
            $('#parent-section').remove();

            // Handle adviser data
            if (data.adviser) {
                const adviserSection = `
                    <div id="adviser-section">
                        <h5>My Adviser</h5>
                        <ul id="teacher-list"></ul>
                    </div>`;
                $(adviserSection).appendTo('.drawer-body');

                const adviserItem = document.createElement('li');
                adviserItem.textContent = `Adviser: ${data.adviser.name}`;
                adviserItem.dataset.userId = data.adviser.id; // Store the adviser's ID
                adviserItem.addEventListener('click', () => openChatWindow(data.adviser)); // Add click event
                $('#teacher-list').append(adviserItem);
            }

            // Handle classmates data
            if (data.classmates && data.classmates.length > 0) {
                const classmateSection = `
                    <div id="classmate-section">
                        <h5>My Classmates</h5>
                        <ul id="user-list"></ul>
                    </div>`;
                $(classmateSection).appendTo('.drawer-body');

                data.classmates.forEach(user => {
                    const li = document.createElement('li');
                    li.textContent = user.name; // Display the user's name
                    li.dataset.userId = user.id; // Store the user's ID
                    li.addEventListener('click', () => openChatWindow(user)); // Add click event
                    $('#user-list').append(li); // Append to classmates list
                });
            }

            // Handle parents data
            if (data.parents && data.parents.length > 0) {
                const parentSection = `
                    <div id="parent-section">
                        <h5>Parents</h5>
                        <ul id="parent-list"></ul>
                    </div>`;
                $(parentSection).appendTo('.drawer-body');

                data.parents.forEach(parent => {
                    const li = document.createElement('li');
                    li.textContent = parent.name; // Display parent's name
                    li.dataset.userId = parent.id; // Store parent's ID
                    li.addEventListener('click', () => openChatWindow(parent)); // Add click event
                    $('#parent-list').append(li); // Append to parents list
                });
            }

            // Show message if no data for any section
            if (!data.adviser && (!data.classmates || data.classmates.length === 0) && (!data.parents || data.parents.length === 0)) {
                const noDataMessage = `<p>No contacts available</p>`;
                $('.drawer-body').append(noDataMessage);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching contacts:', error);
        }
    });
}




function openChatWindow(user) {
    drawer.classList.remove('open'); // Close the drawer
    let chatWindow = document.querySelector(`.direct-chat[data-user-id="${user.id}"]`);

    // If chat window already open, use it
    if (!chatWindow) {
        chatWindow = chatWindowTemplate.content.cloneNode(true).querySelector('.direct-chat');
        chatWindow.dataset.userId = user.id;
        chatWindow.querySelector('.card-title').textContent = user.name; // Set the chat window title
        chatWindow.querySelector('.receiver-id').value = user.id; // Set receiver ID for the form
        chatWindow.querySelector('.close-chat').addEventListener('click', () => chatWindow.remove());
        chatWindows.appendChild(chatWindow);
    }

    // Fetch previous chat messages
    fetchChatMessages(user.id).then(messages => {
        const messagesContainer = chatWindow.querySelector('.direct-chat-messages');
        messagesContainer.innerHTML = ''; // Clear existing messages

        if (messages.length > 0) {
            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('direct-chat-msg');
                if (msg.sender_id === user.id) {
                    messageDiv.classList.add('right'); // Align the message to the right if it's from the user
                }
                messageDiv.innerHTML = `
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name float-${msg.sender_id === user.id ? 'right' : 'left'}">${msg.sender_name}</span>
                        <span class="direct-chat-timestamp float-${msg.sender_id === user.id ? 'left' : 'right'}">${msg.timestamp}</span>
                    </div>
                    <img class="direct-chat-img" src="${msg.sender_avatar}" alt="Message User Image">
                    <div class="direct-chat-text">${msg.message}</div>
                `;
                messagesContainer.appendChild(messageDiv);
            });
        } else {
            const noMessages = document.createElement('div');
            noMessages.classList.add('direct-chat-msg');
            noMessages.textContent = 'No previous messages.';
            messagesContainer.appendChild(noMessages);
        }

        // Automatically scroll to the bottom of the chatbox
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Start fetching new messages every second
        startFetchingMessagesForChat(user.id);
    });
}












// Function to fetch new messages every second for an open chat window
function fetchNewMessages(userId) {
    $.ajax({
        url: 'fetch-message',  // Replace with the correct endpoint
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ user_id: userId }),  // Send the user_id to fetch new messages
        dataType: 'json',
        success: function (data) {
            const chatWindow = document.querySelector(`.direct-chat[data-user-id="${userId}"]`);
            const messagesContainer = chatWindow.querySelector('.direct-chat-messages');

            // Clear existing messages to avoid duplication
            messagesContainer.innerHTML = '';

            if (data.length > 0) {
                data.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('direct-chat-msg');
                    if (msg.sender_id === userId) {
                        messageDiv.classList.add('right'); // Align the message to the right if it's from the user
                    }
                    messageDiv.innerHTML = `
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-${msg.sender_id === userId ? 'right' : 'left'}">${msg.sender_name}</span>
                            <span class="direct-chat-timestamp float-${msg.sender_id === userId ? 'left' : 'right'}">${msg.timestamp}</span>
                        </div>
                        <img class="direct-chat-img" src="${msg.sender_avatar}" alt="Message User Image">
                        <div class="direct-chat-text">${msg.message}</div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                });
            } else {
                const noMessages = document.createElement('div');
                noMessages.classList.add('direct-chat-msg');
                noMessages.textContent = 'No new messages.';
                messagesContainer.appendChild(noMessages);
            }

            // Automatically scroll to the bottom of the chatbox
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        },
        error: function (xhr, status, error) {
            console.error('Error fetching messages:', error);
        }
    });
}

// Call the function every second for each open chat window
function startFetchingMessagesForChat(userId) {
    setInterval(function () {
        fetchNewMessages(userId);
    }, 1000); // 1000 milliseconds = 1 second
}











  // Fetch previous chat messages using $.ajax
  function fetchChatMessages(userId) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: 'fetch-message',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ user_id: userId }),
        dataType: 'json',
        success: function (data) {
          resolve(data);
        },
        error: function (xhr, status, error) {
          console.error('Error fetching messages:', error);
          reject(error);
        }
      });
    });
  }


  $(document).on("submit", "#message-form", function (e) {
  e.preventDefault(); // Prevent the default form submission
  // Retrieve the input values
  const receiverId = $('.receiver-id').val(); // Get the value of the hidden input
  const messageContent = $('.chat-input').val(); // Get the value of the message input
  // Optional: Check if message content is empty before proceeding
  if (!messageContent.trim()) {
    alert("Message cannot be empty!");
    return;
  }
  sendMessage(receiverId, messageContent);
});



  // Send a message using $.ajax
  function sendMessage(userId, messageContent) {
    const chatWindow = document.querySelector(`.direct-chat[data-user-id="${userId}"]`);
    const messagesContainer = chatWindow.querySelector('.direct-chat-messages');
    $.ajax({
      url: 'sendMessage', // Replace with the correct endpoint for sending the message
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        user_id: userId,
        message: messageContent,
      }),
      dataType: 'json',
      success: function (data) {

        if (data.status === 'success') {
          const messageContent = $('.chat-input').val(); 
          // Add the sent message to the chat window
          const messageDiv = document.createElement('div');
          messageDiv.classList.add('direct-chat-msg', 'right'); // Align the sent message to the right
          messageDiv.innerHTML = `
          <div class="direct-chat-infos clearfix">
          <span class="direct-chat-name float-right">${data.sender_name}</span>
          <span class="direct-chat-timestamp float-left">${data.timestamp}</span>
          </div>
          <img class="direct-chat-img" src="${data.sender_avatar}" alt="Message User Image">
          <div class="direct-chat-text">${messageContent}</div>
          `;
          messagesContainer.appendChild(messageDiv);
          messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to bottom
          $('.chat-input').val('');
        } else {
          console.error('Error sending message');
        }
      },
    });
  }
});
</script>

</body>
</html>