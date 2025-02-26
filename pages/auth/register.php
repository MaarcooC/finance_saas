<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="../../assets/css/login_logout/login.css">
    </head>
    <body>
      <div class="main">
          <div class="cont">
              <div class="login-text">REGISTER</div>
              <div class="not-registered">Already have an account? login <a href="index.php">here</a></div>
              <div class="form">
                <form action="signin.php" method="post">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                    <label for="user">User</label>
                    <input type="text" name="user" id="user" required>
                    <label for="pwd">Password</label>
                    <input type="password" name="pwd" id="pwd" required>
                    <button type="submit">Register</button>
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