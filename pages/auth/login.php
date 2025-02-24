<?php
require_once "../../config/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sql injection
    $user = mysqli_real_escape_string($conn, strip_tags($_POST['user']));
    $password = $_POST['pwd'];

    // Query
    $sql = "SELECT idUser, username, password FROM users WHERE username = '$user'";

    $result = mysqli_query($conn, $sql);

    // check if the user exists
    if ($result) {
        if ($row = mysqli_fetch_assoc($result)) {
            // check if the password is correct
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['idUser'];
                $_SESSION['username'] = $row['username'];
                
                header("Location: ../dashboard/index.php");
                exit;
            } else {
                // if the password is incorrect
                $_SESSION['error_message'] = "User not found.";
                header("Refresh: 0; url=index.php");
                exit;
            }
        } else {
            // user not found
            $_SESSION['error_message'] = "User not found.";
            header("Refresh: 0; url=index.php");
            exit;
        }
    } else {
        // query error
        echo "query error.";
    }

    // close the session
    mysqli_close($conn);
}
?>