<?php
require_once('C:/Programming/finance_saas/config/config.php');

$user = $_POST["user"];
$user = mysqli_real_escape_string($conn, $user);
$user = strip_tags($user);
$password = $_POST["pwd"];

$sql = "SELECT password FROM users WHERE username = '$user'";

// Esegui la query
$result = mysqli_query($conn, $sql);

if ($result) {
    if ($row = mysqli_fetch_assoc($result)) {
        // Verifica della password hashata
        if (password_verify($password, $row['password'])) {
            echo "Accesso riuscito!";
        } else {
            echo "Password errata!";
            echo '
                <button>
                <a href="login.html">Vai al login</a>
                </button>';
        }
    } else {
        echo "Utente non trovato!";
        echo '
            <button>
            <a href="login.html">Vai al login</a>
            </button>';
    }
} else {
    echo "Errore nell'esecuzione della query.";
}

mysqli_close($conn);