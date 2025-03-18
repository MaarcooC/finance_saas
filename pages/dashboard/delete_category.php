<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

if (isset($_GET['idCat'])) {
    $cat = $_GET['idCat'];

    // delete category query
    $query = "DELETE FROM categories WHERE idCat = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cat);

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
