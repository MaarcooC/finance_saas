<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/finance_saas/includes/check_auth.php");

if (!isset($_SESSION["user_id"])) {
    $_SESSION['error_message'] = "Session expired!";
    header("Location: /finance_saas/pages/auth");
    exit;
}
?>