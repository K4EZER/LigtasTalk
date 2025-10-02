<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Ticket Chat</title>
  <link rel="stylesheet" href="css/userTicketStyle.css">
  <link rel="stylesheet" href="css/userStyle.css">
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
        <label for="toggleTicket" class="createTicket">Create a Ticket</label>
        <a href="userHome.html"><h4>Home</h4></a>
        <label for="ticketDropdown" class="dropdown-label">
          <h4>Your Tickets ▼</h4>
        </label>
        <input type="checkbox" id="ticketDropdown" class="dropdown-checkbox">
        <div class="dropdown-menu">
          <a href="userTicket.html">Ticket 1</a>
          <a href="#">Ticket 2</a>
          <a href="#">Ticket 3</a>
          <a href="#">Ticket 4</a>
        </div>
        <h4>Suggestions</h4>
        <ul>
          <a href="suggestions.html">
            <li>All <span class="badge">20</span></li>
          </a>
        </ul>
      </div>
    </div>
    <div class="bottom">
      <div class="profile">
        <div class="profile-pic"></div>
        <div class="username">USER</div>
      </div>
      <div class="usern-container">
        <input type="checkbox" id="ellipsisToggle" class="ellipsis-checkbox">
        <label for="ellipsisToggle" class="ellipsis">⋮</label>
        <div class="user-menu">
          <a href="editprofile.html">Edit Profile</a>
          <a href="logout.php">Logout</a>
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
        <span>John Doe (You)</span>
      </li>
      <li>
        <div class="user-icon user"></div>
        <span>Admin</span>
      </li>
    </ul>
  </div>
      <input type="checkbox" id="toggleTicket" hidden>
    <!-- Ticket Overlay -->
    <div class="ticket-content">
      <div class="tickets">
        <h1>Create a Ticket</h1>
        <form>
          <label for="title">Title</label>
          <input type="text" id="title" placeholder="Enter ticket title">

          <label for="category">Category</label>
          <select id="category">
            <option>Harassment</option>
            <option>Bullying</option>
            <option>Misconduct</option>
            <option>Other</option>
          </select>

          <label for="details">Details</label>
          <textarea id="details" placeholder="Describe your issue..."></textarea>

          <label for="beAnonymous" class="checkbox-label">
            <input type="checkbox" id="beAnonymous">
            Submit Anonymously
          </label>

          <div class="form-actions">
            <button type="submit" class="submit-btn">Submit</button>
            <button type="reset" class="reset-btn">Reset</button>
          </div>
        </form>
        <!-- Close button -->
        <label for="toggleTicket" class="close-btn">&times;</label>
      </div>
    </div>
</body>
</html>
