<?php
require_once("/opt/lampp/htdocs/finance_saas/config/config.php");
require_once("/opt/lampp/htdocs/finance_saas/includes/check_auth.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id']; // Verifica che appartenga all'utente loggato

    // Query per eliminare la transazione
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
