<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // delete category query
    $query = "DELETE FROM categories WHERE idcat = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

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
