<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    error_reporting(E_ALL);
ini_set('display_errors', 1);

    $group = $_POST['group'];
    $user_id = $_SESSION['user_id'];

    // Sanitize input
    $group = $conn->real_escape_string($group);

    // Check if group already exists
    $check_query = "SELECT idGroup FROM groups WHERE groupname = ? AND leg_iduser = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("si", $group, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Group already exists!'); window.location.href='settings.php';</script>";
    } else {
        // Insert new group
        $query = "INSERT INTO groups (groupname, leg_iduser) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $group, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Group added successfully!'); window.location.href='settings.php';</script>";
        } else {
            echo "<script>alert('Error adding group: " . $conn->error . "'); window.location.href='settings.php';</script>";
        }
    }
}
?>