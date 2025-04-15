<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

if (isset($_GET['idGroup'])) {
    $idGroup = $_GET['idGroup'];

    // delete group query
    $query = "DELETE FROM groups WHERE idGroup = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idGroup);

    if ($stmt->execute()) {
        header("Location: settings.php?success=1");
        exit();
    } else {
        header("Location: settings.php?error=1");
        exit();
    }
} else {
    header("Location: settings.php?error=1");
    exit();
}
?>
