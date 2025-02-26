<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="../../assets/css/login_logout/login.css">
    </head>
    <body>
      <div class="main">
          <div class="cont">
              <div class="login-text">LOG IN</div>
              <div class="not-registered">Don't have an account yet? register <a href="register.php">here</a></div>
              <div class="form">
                <form action="login.php" method="post">
                    <label for="user">User</label>
                    <input type="text" name="user" id="user" required>
                    <label for="pwd">Password</label>
                    <input type="password" name="pwd" id="pwd" required>
                    <button type="submit">Log In</button>
                </form>
                <?php
                    // error
                    session_start();
                    if (isset($_SESSION['error_message'])) {
                        echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
                        unset($_SESSION['error_message']);
                    }
                ?>
              </div>
          </div>
      </div>
  </body>
</html>