<?php
session_start();
require 'connect.php';

// Check if Admin/Staff is logged in
if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit();
}

// Allow only Admin or Staff
if ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff') {
    header("Location: userHome.php");
    exit();
}

$userId = $_SESSION['account_id'];

// Handle new suggestion submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['suggestion_details'])) {
  $details = trim($_POST['suggestion_details']);
  $anonymous = isset($_POST['suggestAnonymous']) ? 1 : 0;

  if (!empty($details)) {
    $sql = "INSERT INTO suggestion (account_id, details, anonymous, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $userId, $details, $anonymous);
    $stmt->execute();
  }
}

// Fetch all suggestions
$suggestions_sql = "SELECT s.*, a.name 
                    FROM suggestion s 
                    LEFT JOIN account a ON s.account_id = a.account_id
                    ORDER BY s.created_at DESC";
$suggestions = $conn->query($suggestions_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LigtasTalk | Suggestions</title>
  <link rel="stylesheet" href="css/adminStyle.css">
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
        <a href="adminDashboard.php"><h4>Dashboard</h4></a>
        <h4>Active Tickets</h4>
        <ul id="categoryFilter">
          <?php
          $categories = $conn->query("SELECT category, COUNT(*) AS total FROM ticket GROUP BY category");
          if ($categories->num_rows > 0):
            while ($cat = $categories->fetch_assoc()):
          ?>
              <li data-category="<?= htmlspecialchars($cat['category']) ?>" class="category-item">
                <?= htmlspecialchars($cat['category']) ?> <span class="badge"><?= $cat['total'] ?></span>
              </li>
          <?php endwhile; else: ?>
              <li>No tickets yet</li>
          <?php endif; ?>
        </ul>
        <h4>Suggestions</h4>
        <ul>
          <a href="adminSuggestions.php">
            <li>
              All 
              <span class="badge">
                <?php
                $suggestionCountQuery = "SELECT COUNT(*) AS total FROM suggestion";
                $result = $conn->query($suggestionCountQuery);
                if ($result && $row = $result->fetch_assoc()) {
                    echo htmlspecialchars($row['total']);
                } else {
                    echo "0";
                }
                ?>
              </span>
            </li>
          </a>
        </ul>
      </div>
    </div>
    <div class="bottom">
      <div class="profile">
        <div class="profile-pic"></div>
        <div class="username"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
      </div>
      <div class="usern-container">
        <input type="checkbox" id="ellipsisToggle" class="ellipsis-checkbox">
        <label for="ellipsisToggle" class="ellipsis">⋮</label>
        <div class="user-menu">
          <?php if ($_SESSION['role'] === 'Admin') {
              echo '<a href="adminRegister.php">Create Account</a>';
            } else {
              echo '';
          }?>
          <a href="editprofile.php">Edit Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Suggestion Overlay -->
  <input type="checkbox" id="toggleSuggestion" hidden>
  <div class="suggestion-content">
    <div class="suggestion">
      <h1>Create Suggestion</h1>
      <form method="POST">
        <label for="details">Details</label>
        <textarea name="suggestion_details" id="details" placeholder="Describe your suggestion..." required></textarea>

        <label for="suggestAnonymous" class="checkbox-label">
          <input type="checkbox" name="suggestAnonymous" id="suggestAnonymous">
          Submit Anonymously
        </label>

        <div class="form-actions">
          <button type="submit" class="submit-btn">Submit</button>
          <button type="reset" class="reset-btn">Reset</button>
        </div>
      </form>
      <label for="toggleSuggestion" class="close-btn">&times;</label>
    </div>
  </div>

  <input type="checkbox" id="toggleTicket" hidden>

  <!-- Suggestions -->
  <main class="suggestions-container">
    <h2>SUGGESTIONS</h2>

    <?php
    if ($suggestions->num_rows > 0) {
      while ($row = $suggestions->fetch_assoc()) {
        $author = $row['anonymous'] ? 'Anonymous' : htmlspecialchars($row['name']);
        $date = date("F j, Y", strtotime($row['created_at']));
        echo "
        <div class='suggestion-card'>
          <div class='suggestion-header'>
            <div class='suggestion-title'>{$author}'s Suggestion</div>
            <div class='user-avatar'></div>
            
          </div>
          <div class='suggestion-body'>
            " . nl2br(htmlspecialchars($row['details'])) . "
          </div>
          <div class='suggestion-meta'>
            Suggestion ID: {$row['suggestion_id']} | {$date}
          </div>
          <div class='suggestion-footer'>
            <button class='vote-btn vote-up' onclick='vote(this, 1)' data-suggestion='{$row['suggestion_id']}'>▲ <span class='count'>" . ($row['upvotes'] ?? 0) . "</span></button>
            <button class='vote-btn vote-down' onclick='vote(this, -1)' data-suggestion='{$row['suggestion_id']}'>▼ <span class='count'>" . ($row['downvotes'] ?? 0) . "</span></button>
          </div>
        </div>";
      }
    } else {
      echo "<p style='color:gray;text-align:center;margin-top:50px;margin-bottom:50px;'>No suggestions yet. Be the first to submit one!</p>";
    }
    ?>

    <label for="toggleSuggestion" class="create-btn">Create Suggestion</label>
  </main>

  <script>
    function vote(button, changeType) {
      const suggestionId = button.dataset.suggestion;
      const voteType = changeType === 1 ? 'Upvote' : 'Downvote';

      fetch('vote.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `suggestion_id=${suggestionId}&vote_type=${voteType}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }

        // Update counts dynamically
        button.parentElement.querySelector('.vote-up .count').textContent = data.upvotes;
        button.parentElement.querySelector('.vote-down .count').textContent = data.downvotes;
      })
      .catch(err => console.error('Error:', err));
    }
  </script>
</body>
</html>
