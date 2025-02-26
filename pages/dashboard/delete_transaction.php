<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id']; // verify logged user

    // query to delete trabsaction
    $query = "DELETE FROM transactions WHERE idTrans = ? AND leg_idUser = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        header("Location: transactions.php?success=1");
        exit();
    } else {
        header("Location: transactions.php?error=1");
        exit();
    }
} else {
    header("Location: transactions.php?error=1");
    exit();
}
?>
