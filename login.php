<?php
session_start();
require 'connect.php';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $id_number = trim($_POST['IDNum']);
    $password  = trim($_POST['password']);

    // Fetch account from database
    $sql = "SELECT * FROM account WHERE id_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['account_id'] = $row['account_id'];
            $_SESSION['role']       = $row['role'];
            $_SESSION['name']       = $row['name'];

            // Redirect based on role
            if ($row['role'] === 'Admin') {
                header("Location: adminDashboard.php");
            } elseif ($row['role'] === 'Staff') {
                header("Location: staffDashboard.php");
            } else {
                header("Location: userHome.php");
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Account not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LigtasTalk</title>
    <link rel="stylesheet" href="css/loginStyle.css">
</head>
<body>
  <div class="wrapper">
    <!-- Logo -->
    <div class="logo">
      <img src="images/Logo.jpg" alt="LigtasTalk Logo">
    </div>
    <div class="card-switch">
      <label class="switch">
        <input type="checkbox" class="toggle">
        <span class="slider"></span>
        <span class="card-side"></span>
        <div class="flip-card__inner">
          
          <!-- Login Form -->
          <div class="flip-card__front">
            <div class="title">Log in</div>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form class="flip-card__form" method="POST" action="">
              <input class="flip-card__input" name="IDNum" placeholder="ID Number" type="text" required>
              <input class="flip-card__input" name="password" placeholder="Password" type="password" autocomplete="off" required>
              <a href="#" class="forget">Forgot password?</a>
              <button type="submit" name="login" class="flip-card__btn">Log in</button>
            </form>
          </div>

          <!-- Registration Form -->
          <div class="flip-card__back">
            <div class="title">Sign up</div>
            <form class="flip-card__form" method="POST" action="register.php">
              <input class="flip-card__input" name="Username" placeholder="Username" type="text" required>
              <input class="flip-card__input" name="IDNum" placeholder="ID Number" type="text" pattern="\d{8}" required>
              <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
              <input class="flip-card__input" name="password" placeholder="Password" type="password" autocomplete="off" required>
              <button type="submit" name="register" class="flip-card__btn">Sign Up</button>
            </form>
          </div>

        </div>
      </label>
    </div>  
  </div>
</body>
</html>
