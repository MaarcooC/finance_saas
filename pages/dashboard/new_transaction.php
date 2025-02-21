<?php
require_once("C:/xampp\htdocs/finance_saas\config\config.php");
require_once("C:/xampp\htdocs/finance_saas\includes\check_auth.php");
?>

<html lang="en">
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="../../assets/css/dashboard/base.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/new_t.css">
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
            <div class="main">
                <div class="nt-text">New Transaction</div>
                <div class="cont-form">
                    <form action="in_transaction.php" method="post">
                        <label for="date_t">Date</label>
                        <input type="date" required id="date_t" name="date_t">

                        <label for="descr">Description</label>
                        <input type="text" required id="descr" name="descr">
                        
                        <label for="category">Category</label>
                        <select name="category" id="category">
                            <option value="health">Health</option>
                            <option value="Food">Food</option>
                            <option value="Clothes">Clothes</option>
                            <option value="School">School</option>
                            <option value="Travels">Travels</option>
                            <option value="Motorsport">Motorsport</option>
                            <option value="Leisure">Leisure</option>
                            <option value="My car">My car</option>
                            <option value="Subscriptions">Subscriptions</option>
                            <option value="Gifts">Gifts</option>
                            <option value="Bank">Bank</option>
                            <option value="Space">Space</option>
                            <option value="Income">Income</option>
                        </select>

                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" required id="amount" name="amount">
                        
                        <label for="account">Account</label>
                        <select name="account" id="account">
                            <option value="BPM">BPM</option>
                            <option value="Revolut">Revolut</option>
                        </select>

                        <input type="submit" value="Add Transaction">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>