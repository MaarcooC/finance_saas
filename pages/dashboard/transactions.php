<?php
require_once("/opt/lampp/htdocs/finance_saas/config/config.php");
require_once("/opt/lampp/htdocs/finance_saas/includes/check_auth.php");

// records per page
$records_per_page = 15;

// determine the current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// get the logged user's id
$user_id = $_SESSION['user_id'];

// start building the SQL query
$query = "SELECT * FROM transactions WHERE leg_idUser = $user_id";

// filters for date
if (isset($_GET['from_date']) && $_GET['from_date'] !== '') {
    $from_date = $_GET['from_date'];
    $query .= " AND t_date >= '$from_date'";
}

if (isset($_GET['to_date']) && $_GET['to_date'] !== '') {
    $to_date = $_GET['to_date'];
    $query .= " AND t_date <= '$to_date'";
}

// filter for category
if (isset($_GET['category']) && $_GET['category'] !== '') {
    $category = $_GET['category'];
    $query .= " AND category = '$category'";
}

// filter for account
if (isset($_GET['account']) && $_GET['account'] !== '') {
    $account = $_GET['account'];
    $query .= " AND account = '$account'";
}

// add ordering and pagination
$query .= " ORDER BY t_date DESC LIMIT $records_per_page OFFSET $offset";

// execute the query to get the records
$result = $conn->query($query);

// total number of records (with any applied filters)
$total_query = "SELECT COUNT(*) as total FROM transactions WHERE leg_idUser = $user_id";

// apply filters to the total query
if (isset($from_date) && $from_date !== '') {
    $total_query .= " AND t_date >= '$from_date'";
}

if (isset($to_date) && $to_date !== '') {
    $total_query .= " AND t_date <= '$to_date'";
}

if (isset($category) && $category !== '') {
    $total_query .= " AND category = '$category'";
}

if (isset($account) && $account !== '') {
    $total_query .= " AND account = '$account'";
}

$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// calculate the total number of pages
$total_pages = ceil($total_records / $records_per_page);
?>



<html lang="en">
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="../../assets/css/dashboard/base.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/transaction.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/filters.css">
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
                <div class="m-header">
                    <div class="trans-history">Transaction History</div>
                    <div class="trans-center">
                        <div class="cont-form-filter">
                        <form method="GET" action="transactions.php">
                            <!-- Date filter -->
                            <label for="from_date">From:</label>
                            <input type="date" name="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>">

                            <label for="to_date">To:</label>
                            <input type="date" name="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>">

                            <!-- Category filter -->
                            <select name="category">
                                <option value="">All Categories</option>
                                <?php
                                $catQuery = "SELECT DISTINCT category FROM transactions WHERE leg_idUser = ?";
                                $stmtCat = $conn->prepare($catQuery);
                                $stmtCat->bind_param("i", $_SESSION['user_id']);
                                $stmtCat->execute();
                                $catResult = $stmtCat->get_result();
                                while ($catRow = $catResult->fetch_assoc()) {
                                    $selected = ($_GET['category'] ?? '') == $catRow['category'] ? 'selected' : '';
                                    echo "<option value='{$catRow['category']}' $selected>{$catRow['category']}</option>";
                                }
                                ?>
                            </select>

                            <!-- Account filter -->
                            <select name="account">
                                <option value="">All Accounts</option>
                                <?php
                                $accQuery = "SELECT DISTINCT account FROM transactions WHERE leg_idUser = ?";
                                $stmtAcc = $conn->prepare($accQuery);
                                $stmtAcc->bind_param("i", $_SESSION['user_id']);
                                $stmtAcc->execute();
                                $accResult = $stmtAcc->get_result();
                                while ($accRow = $accResult->fetch_assoc()) {
                                    $selected = ($_GET['account'] ?? '') == $accRow['account'] ? 'selected' : '';
                                    echo "<option value='{$accRow['account']}' $selected>{$accRow['account']}</option>";
                                }
                                ?>
                            </select>

                            <button type="submit">Filter</button>
                            <a href="transactions.php" class="reset-btn">Reset</a>
                        </form>
                        </div>    
                    </div>
                    <div class="m-h-right">
                        <button><a href="new_transaction.php">New Transaction</a></button>
                    </div>
                </div>
                
                <!-- transactions table -->
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Account</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo date("d/m/Y", strtotime($row['t_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['descr']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo number_format($row['amount'], 2); ?> €</td>
                                <td><?php echo htmlspecialchars($row['account']); ?></td>
                                <td>
                                    <a href="delete_transaction.php?id=<?php echo $row['idTrans']; ?>" onclick="return confirm('Are you sure you want to delete this transaction?');">
                                        <img src="../../assets/img/bin.png" alt="delete">
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- page navigation -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">← Previous</a>
                    <?php endif; ?>

                    <span>Page <?= $page ?> of <?= $total_pages ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>
