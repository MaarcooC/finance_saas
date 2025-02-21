<?php
require_once('C:\xampp\htdocs\finance_saas\config\config.php');

    session_unset();
    session_destroy();    
    header("Location: /finance_saas/pages/auth");
    exit;
?>