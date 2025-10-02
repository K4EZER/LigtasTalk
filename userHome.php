<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LigtasTalk | Home</title>
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

  <!-- Main -->
  <main class="main">
    <div class="header">
      <div class="welcome-container">
        <div class="welcome-text">
          <h2>Welcome, User!</h2>
          <p>Giving every student a safe voice to report, protect, and create a stronger campus community.</p>
        </div>
        <div class="profile-picture">
          <img src="images/defaultProfile.png" alt="User Profile">
        </div>
      </div>
    </div>
    <section class="info-section">
      <h3>About LigtasTalk</h3>
      <p>LigtasTalk is your dedicated platform for reporting misconduct, bullying, harassment, and other concerns within the school community. We prioritize your safety and confidentiality, ensuring that every report is handled with care and urgency.</p>
      <p>Our goal is to foster a secure and supportive environment where students can voice their concerns without fear. Whether you choose to report anonymously or openly, your voice matters, and we are here to listen and act.</p>
      <p>Thank you for being a part of our commitment to a safer campus. Together, we can make a difference.</p>
    </section>
    <!-- Quick Actions -->
    <section class="quick-actions">
      <h3>Create a Ticket or Suggestion?</h3>
      <div class="action-buttons">
        <label for="toggleTicket" class="action-btn ticket-btn">Create Ticket</label>
        <label for="toggleSuggestion" class="action-btn suggestion-btn">Make a Suggestion</a>
      </div>
    </section>

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
  </main>

</body>
</html>
