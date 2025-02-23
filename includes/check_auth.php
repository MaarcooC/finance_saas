<?php
if (!isset($_SESSION["user_id"])) {
    $_SESSION['error_message'] = "Session expired!";
    header("Location: /finance_saas/pages/auth");
    exit;
}
?>