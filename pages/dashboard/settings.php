<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";
require_once "../../includes/show_cat.php";
?>

<html lang="en">
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="../../assets/css/dashboard/overview.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/base.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/settings.css">
        <script src="../../includes/settings.js" defer></script>
        <meta charset="UTF-8">
    </head>
    <body>
        <div class="header">
            <div class="left">
                <div class="finance-manager">
                    <a href=""><img src="../../assets/img/graph.png" alt="Graph Icon"></a>
                    Finance Manager
                </div>            
                <div class="welcome">
                    <?php echo "Welcome back " . $_SESSION['username'] . "! 👋🏻"; ?>
                </div>
            </div>
            <div class="center">
                <button><a href="index.php">Overview</a></button>
                <button><a href="transactions.php">Transactions</a></button>
                <button>Settings</button>
            </div>
            <div class="right">
                <div class="logout">
                    <a href="../auth/logout.php"><img src="../../assets/img/logout.png" alt="logout"></a>
                </div>
            </div>
        </div>
        <div class="settings-content">
            <div class="sidebar">
                <div class="cat" onclick="showContent('man-cat-content')">
                    Manage categories
                </div>
                <div class="cat" onclick="showContent('altro-content')">
                    Others
                </div>
            </div>
            <div class="man-cat-content" id="c-content">
                <div class="cont-cat">
                    <div class="c-title">Manage Categories</div>
                    <table>
                        <tr><th>Categories</th><th>Action</th></tr>
                        <?php show($conn); ?>
                    </table>
                    <div>
                        <form action="new_cat.php" method="post">
                            <label for="cat">Insert new Category</label>
                            <input id="cat" type="text" name="category">
                            <input type="submit" value="Insert">
                        </form>
                    </div>
                </div>
            </div>
            <div class="altro-content" id="c-content">
                ciao2
            </div>
        </div>
    </body>
</html>