<?php
require 'connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $id_number = trim($_POST['IDNum']);
    if (!preg_match('/^[0-9]{8}$/', $id_number)) {
      echo "<script>alert('ID Number must be exactly 8 digits!'); window.location='login.php';</script>";
      exit;
    }
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);

    // Hash the password for security
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);

    // Check if ID number or email already exists
    $check_sql = "SELECT * FROM account WHERE id_number = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $id_number, $email);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('ID Number or Email already exists!'); window.location='login.php';</script>";
        exit;
    }

    // Insert as User (public registration only)
    $sql = "INSERT INTO account (id_number, email, password, role) VALUES (?, ?, ?, 'User')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $id_number, $email, $hashed_pw);

    if ($stmt->execute()) {
        // Auto-login after successful registration
        $_SESSION['account_id'] = $stmt->insert_id;
        $_SESSION['role'] = "User";
        $_SESSION['name'] = $id_number; // You can add a "name" field to form later

        header("Location: userHome.php");
        exit;
    } else {
        echo "<script>alert('Registration failed! Please try again.'); window.location='login.php';</script>";
    }
}
?>
