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

// -------- Fetch counts for dashboard --------
$totalTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket")->fetch_assoc()['total'];
$openTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket WHERE status='Open'")->fetch_assoc()['total'];
$progressTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket WHERE status='In-progress'")->fetch_assoc()['total'];
$closedTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket WHERE status='Closed'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LigtasTalk | Ticket Dashboard</title>
  <link rel="stylesheet" href="css/adminStyle.css">
  <style>
    .category-item {
      cursor: pointer;
      transition: background 0.2s;
    }
    .category-item:hover {
      background: #f0f0f0;
    }
    .category-item.active {
      background: #007bff;
      color: white;
      border-radius: 5px;
    }
  </style>
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
          <li>Ideas <span class="badge">20</span></li>
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

  <!-- Main -->
  <main class="main">
    <div class="header">
      <h2>Ticket Dashboard</h2>
      <p>Manage and track support tickets</p>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
      <div class="dashboardBox"><h3><?= $totalTickets ?></h3><p>Total Tickets</p></div>
      <div class="dashboardBox"><h3 class="open"><?= $openTickets ?></h3><p>Open</p></div>
      <div class="dashboardBox"><h3 class="progress"><?= $progressTickets ?></h3><p>In Progress</p></div>
      <div class="dashboardBox"><h3 class="closed"><?= $closedTickets ?></h3><p>Closed</p></div>
    </div>

    <!-- Search and filters -->
    <div class="search-filter">
      <input type="text" id="searchInput" placeholder="Search tickets...">
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Open">Open</option>
        <option value="In-progress">In-progress</option>
        <option value="Closed">Closed</option>
        <option value="Reopened">Reopened</option>
      </select>
      <select id="categoryDropdown">
        <option value="">All Category</option>
        <?php
          $categories = $conn->query("SELECT DISTINCT category FROM ticket");
          while ($cat = $categories->fetch_assoc()):
        ?>
          <option value="<?= htmlspecialchars($cat['category']) ?>"><?= htmlspecialchars($cat['category']) ?></option>
        <?php endwhile; ?>
      </select>
      <button id="filterBtn">Filter</button>
    </div>

    <!-- Tickets table -->
    <table>
      <thead>
        <tr>
          <th>Ticket</th>
          <th>Status</th>
          <th>Category</th>
          <th>Assigned To</th>
          <th>Last Activity</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="ticketTableBody">
        <!-- Ticket rows will be loaded here -->
      </tbody>
    </table>
  </main>

  <script>
  function loadTickets(filters = {}) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "updateTicketTable.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    const params = new URLSearchParams(filters).toString();

    xhr.onload = function() {
      if (this.status === 200) {
        document.getElementById("ticketTableBody").innerHTML = this.responseText;
      }
    };
    xhr.send(params);
  }

  // Initial load
  loadTickets();

  // Sidebar category click filter
  document.querySelectorAll(".category-item").forEach(item => {
    item.addEventListener("click", () => {
      document.querySelectorAll(".category-item").forEach(li => li.classList.remove("active"));
      item.classList.add("active");
      const category = item.getAttribute("data-category");
      loadTickets({ category });
    });
  });

  // Filter button
  document.getElementById("filterBtn").addEventListener("click", () => {
    const search = document.getElementById("searchInput").value;
    const status = document.getElementById("statusFilter").value;
    const category = document.getElementById("categoryDropdown").value;
    loadTickets({ search, status, category });
  });
  </script>
</body>
</html>
