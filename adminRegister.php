<?php
session_start();
require 'connect.php';

// Handle admin registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $id_number = trim($_POST['id_number']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if account already exists
        $check = $conn->prepare("SELECT * FROM account WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Hash password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'Admin';
            $profile_pic = 'default.jpg'; // optional default profile pic

            $stmt = $conn->prepare("INSERT INTO account (id_number, email, password, name, role, profile_pic) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $id_number, $email, $hashed_password, $name, $role, $profile_pic);

            if ($stmt->execute()) {
                $success = "Admin account successfully created.";
            } else {
                $error = "Error creating account: " . $conn->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LigtasTalk | Create Admin Account</title>
  <link rel="stylesheet" href="css/adminStyle.css">
  <link rel="stylesheet" href="css/adminRegisterStyle.css">
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
        <ul>
          <?php
          $categories = $conn->query("SELECT category, COUNT(*) AS total FROM ticket GROUP BY category");
          if ($categories->num_rows > 0):
            while ($cat = $categories->fetch_assoc()):
          ?>
              <li><?= htmlspecialchars($cat['category']) ?> <span class="badge"><?= $cat['total'] ?></span></li>
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
          <a href="adminRegister.php">Create Account</a>
          <a href="editprofile.php">Edit Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <main class="main">
    <div class="header">
      <h2>Create Admin Account</h2>
      <p>Register a new administrator account</p>
    </div>

    <div class="form-container">
      <?php if (isset($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
      <?php elseif (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="" method="POST" class="register-form">
        <label>ID Number</label>
        <input type="text" name="id_number" required>

        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Create Admin</button>
      </form>
    </div>
  </main>
</body>
</html>
