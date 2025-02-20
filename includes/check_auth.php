<?php
require_once('../config/config.php');

if (!isset($_SESSION["user_id"])) {
    $_SESSION['error_message'] = "Session expired!";
    header("Location: ../pages/auth/login.php");
    exit;
}
?>