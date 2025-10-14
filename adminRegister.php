<?php
session_start();
require 'connect.php';

// Check if Admin is logged in
if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is an Admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: userHome.php");
    exit();
}

$error = "";
$success = "";

//Handle registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $id_number = trim($_POST['id_number']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];

    // Validation
    if (empty($name) || empty($id_number) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT * FROM account WHERE email = ? OR id_number = ?");
        $check->bind_param("ss", $email, $id_number);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "An account with this email or ID number already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $profile_pic = 'default.jpg';

            $stmt = $conn->prepare("INSERT INTO account (id_number, email, password, name, role, profile_pic) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $id_number, $email, $hashed_password, $name, $role, $profile_pic);

            if ($stmt->execute()) {
                $success = ucfirst($role) . " account successfully created.";
            } else {
                $error = "Error creating account: " . htmlspecialchars($conn->error);
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
  <title>LigtasTalk | Create Admin/Staff Account</title>
  <link rel="stylesheet" href="css/adminStyle.css">
  <link rel="stylesheet" href="css/adminRegisterStyle.css?v=1.1">
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

  <!-- Main -->
  <main class="main">
    <div class="header">
      <h2>Create Admin/Staff Account</h2>
      <p>Register a new Admin or Staff account</p>
    </div>

    <div class="form-container">
      <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
      <?php elseif (!empty($error)): ?>
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

        <label>Account Role</label>
        <select name="role" required>
          <option value="Admin">Admin</option>
          <option value="Staff">Staff</option>
        </select>

        <button type="submit">Create Account</button>
      </form>
    </div>
  </main>
</body>
</html>
