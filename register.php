<?php
require 'connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = trim($_POST['Username']);
    $id_number = trim($_POST['IDNum']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($id_number) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location='login.php';</script>";
        exit;
    }

    if (!preg_match('/^[0-9]{8}$/', $id_number)) {
        echo "<script>alert('ID Number must be exactly 8 digits!'); window.location='login.php';</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location='login.php';</script>";
        exit;
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and contain both letters and numbers!'); window.location='login.php';</script>";
        exit;
    }

    $check_sql = "SELECT * FROM account WHERE id_number = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $id_number, $email);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('ID Number or Email already exists!'); window.location='login.php';</script>";
        exit;
    }

    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO account (id_number, email, password, name, role, profile_pic) VALUES (?, ?, ?, ?, 'User', 'default.jpg')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $id_number, $email, $hashed_pw, $name);

    if ($stmt->execute()) {
        $_SESSION['account_id'] = $stmt->insert_id;
        $_SESSION['role'] = "User";
        $_SESSION['name'] = $name;

        echo "<script>alert('Registration successful! Welcome to LigtasTalk.'); window.location='userHome.php';</script>";
        exit;
    } else {
        echo "<script>alert('Registration failed! Please try again.'); window.location='login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
