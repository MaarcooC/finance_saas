<?php
require_once("C:/xampp\htdocs/finance_saas\config\config.php");
require_once("C:/xampp\htdocs/finance_saas\includes\check_auth.php");
?>

<html lang="en">
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="../../assets/css/dashboard/base.css">
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
        <div class="content">
            <div class="boxes">
                <div class="income">Income</div>
                <div class="expenses">Expenses</div>
                <div class="cur-balance">Current Balance</div>
            </div>
        </div>
    </body>
</html>