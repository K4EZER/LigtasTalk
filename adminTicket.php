<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Chat System</title>
  <link rel="stylesheet" href="css/ticketStyle.css">
  <link rel="stylesheet" href="css/adminStyle.css">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div>
      <div class="LogoContainer">
        <img class="logo image" src="images/Logo.jpg" alt="LigtasTalk Logo">
        <h2 class="logo title">LigtasTalk</h2>
      </div>
      <div class="menu">
        <a href="adminDashboard.html"><h4>Dashboard</h4></a>
        <h4>Active Tickets</h4>
        <ul>
          <li>Login Issues <span class="badge">3</span></li>
          <li>Payment Problem <span class="badge">1</span></li>
          <li>Feature Request <span class="badge">3</span></li>
          <li>Bug Report <span class="badge">5</span></li>
        </ul>
        <h4>Suggestions</h4>
        <ul>
          <li>Ideas <span class="badge">20</span></li>
        </ul>
      </div>
    </div>
    <div class="bottom">
      <div class="profile">
        <div class="profile-pic"></div>
        <div class="username">ADMIN</div>
      </div>
      <div class="usern-container">
        <input type="checkbox" id="ellipsisToggle" class="ellipsis-checkbox">
        <label for="ellipsisToggle" class="ellipsis">⋮</label>
        <div class="user-menu">
          <a href="editprofile.html">Edit Profile</a>
          <a href="#">Logout</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Chat Area -->
  <div class="chat-container">
    <!-- Header -->
    <div class="chat-header">
      <h2>Ticket #12345</h2>
      <p>Issue: Cannot login to account</p>
    </div>

    <!-- Messages -->
    <div class="messages">
      <div class="message user">
        Hello, I can’t log in to my account.
        <span class="time">10:15 AM</span>
      </div>
      <div class="message staff">
        Hi! I’ll help you with that. Can you confirm your email?
        <span class="time">10:16 AM</span>
      </div>
      <div class="message user">
        Yes, it’s johndoe@example.com
        <span class="time">10:17 AM</span>
      </div>
      <div class="message staff">
        Thanks! I’ll reset your access now.
        <span class="time">10:18 AM</span>
      </div>
    </div>

    <!-- Input -->
    <div class="chat-input">
      <input type="text" placeholder="Type your message..." />
      <button>Send</button>
    </div>
  </div>

  <!-- Right Sidebar -->
  <div class="right-sidebar">
    <h3>Participants</h3>
    <ul>
      <li>
        <div class="user-icon staff"></div>
        <span>Admin (You)</span>
      </li>
      <li>
        <div class="user-icon user"></div>
        <span>John Doe</span>
      </li>
    </ul>
  </div>
</body>
</html>
