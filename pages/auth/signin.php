<?php
require_once "../../config/config.php";

// Sanitize input
$username = trim($_POST["user"]);
$email = trim($_POST["email"]);
$password = $_POST["pwd"];

// Hash password securely
$hash = password_hash($password, PASSWORD_BCRYPT);

// Check if email already exists (prevent duplicate accounts)
$stmt = $conn->prepare("SELECT idUser FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['error_message'] = "An account already exists with this email.";
    header("Location: register.php");
    exit;
}

// Insert user securely using prepared statement
$stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hash, $email);

if ($stmt->execute()) {
    header("Location: index.php");
    exit;
} else {
    $_SESSION['error_message'] = "Registration failed. Please try again.";
    header("Location: register.php");
}

$stmt->close();
$conn->close();