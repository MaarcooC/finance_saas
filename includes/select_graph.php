<?php
require_once "../config/config.php";

if (isset($_POST['graphs1'])) {
    $_SESSION['graph1'] = intval($_POST['graphs1']);
    exit();
}
exit();