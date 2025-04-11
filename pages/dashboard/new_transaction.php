<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

// get the logged user's id
$user_id = $_SESSION['user_id'];

$query = "select idCat, category from categories where leguser_id = $user_id";

// show select input
function select_personal($conn, $ris, $res):void
{
    while ($row = mysqli_fetch_assoc($ris)) {
        echo '<option value="'.$row["$res"].'">';
        echo htmlspecialchars($row['category']);
        echo '</option>';
    }
}
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
                <button><a href="settings.php">Settings</a></button>
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
                            <?php 
                            // get the logged user's id
                            $user_id = $_SESSION['user_id'];

                            $query = "select idCat, category from categories where leguser_id = $user_id";

                            // execute the query to get the records
                            $result = $conn->query($query);

                            select_personal($conn, $result, "idCat");
                            ?>
                        </select>

                        <label for="groups">Group</label>
                        <input type="text" name="groups" id="groups">

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