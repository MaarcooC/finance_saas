<?php
require_once "../../config/config.php";

    session_unset();
    session_destroy();    
    header("Location: /finance_saas/pages/auth");
    exit;
?>