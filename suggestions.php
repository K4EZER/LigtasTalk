<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LigtasTalk | Suggestions</title>
  <link rel="stylesheet" href="css/userStyle.css">
  <link rel="stylesheet" href="css/suggestionsStyle.css">
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
        <a href="userHome.php"><h4>Home</h4></a>
        <label for="ticketDropdown" class="dropdown-label">
          <h4>Your Tickets ▼</h4>
        </label>
        <input type="checkbox" id="ticketDropdown" class="dropdown-checkbox">
        <div class="dropdown-menu">
          <a href="userTicket.php">Ticket 1</a>
          <a href="#">Ticket 2</a>
          <a href="#">Ticket 3</a>
          <a href="#">Ticket 4</a>
        </div>
        <h4>Suggestions</h4>
        <ul>
          <li><a href="suggestions.php">All</a> <span class="badge">20</span></li>
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
          <a href="editprofile.php">Edit Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </aside>

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

    <input type="checkbox" id="toggleSuggestion" hidden>
    <!-- Suggestion Overlay -->
    <div class="suggestion-content">
      <div class="suggestion">
        <h1>Create Suggestion</h1>
        <form>
          <label for="details">Details</label>
          <textarea id="details" placeholder="Describe your suggestion..."></textarea>

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
        <label for="toggleSuggestion" class="close-btn">&times;</label>
      </div>
    </div>

  <!-- Suggestions -->
  <main class="suggestions-container">
    <h2>SUGGESTIONS</h2>

    <!-- Suggestion Card -->
    <div class="suggestion-card">
      <div class="suggestion-header">
        <div class="suggestion-title">User1's Suggestion</div>
        <div class="user-avatar"></div>
      </div>
      <div class="suggestion-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus rhoncus ex a porta maximus.
        Maecenas quam lacus, cursus vitae lacus semper, fringilla dignissim libero.
      </div>
      <div class="suggestion-meta">
        <span class="meta-icon"></span>
        Suggestion ID: GHThdj | June 11, 2025
      </div>
      <div class="suggestion-footer">
        <button class="vote-btn vote-up" onclick="vote(this, 1)">▲ <span class="count">8</span></button>
        <button class="vote-btn vote-down" onclick="vote(this, -1)">▼ <span class="count">1</span></button>
      </div>
    </div>

    <div class="suggestion-card">
      <div class="suggestion-header">
        <div class="suggestion-title">User2's Suggestion</div>
        <div class="user-avatar"></div>
      </div>
      <div class="suggestion-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus rhoncus ex a porta maximus.
        Maecenas quam lacus, cursus vitae lacus semper, fringilla dignissim libero.
      </div>
      <div class="suggestion-meta">
        <span class="meta-icon"></span>
        Suggestion ID: GHt47 | June 11, 2025
      </div>
      <div class="suggestion-footer">
        <button class="vote-btn vote-up" onclick="vote(this, 1)">▲ <span class="count">8</span></button>
        <button class="vote-btn vote-down" onclick="vote(this, -1)">▼ <span class="count">1</span></button>
      </div>
    </div>

    <div class="suggestion-card">
      <div class="suggestion-header">
        <div class="suggestion-title">User3's Suggestion</div>
        <div class="user-avatar"></div>
      </div>
      <div class="suggestion-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus rhoncus ex a porta maximus.
        Maecenas quam lacus, cursus vitae lacus semper, fringilla dignissim libero.
      </div>
      <div class="suggestion-meta">
        <span class="meta-icon"></span>
        Suggestion ID: GHjk23 | June 11, 2025
      </div>
      <div class="suggestion-footer">
        <button class="vote-btn vote-up" onclick="vote(this, 1)">▲ <span class="count">8</span></button>
        <button class="vote-btn vote-down" onclick="vote(this, -1)">▼ <span class="count">1</span></button>
      </div>
    </div>

    <label for="toggleSuggestion" class="create-btn">Create Suggestion</label>
  </main>

  <script>
    function vote(button, change) {
      const countSpan = button.querySelector('.count');
      let current = parseInt(countSpan.innerText);
      countSpan.innerText = current + change;
    }
  </script>
</body>
</html>
