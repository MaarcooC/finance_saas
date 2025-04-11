<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

// Records per page
$records_per_page = 15;

// Determine the current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Sanitize input parameters
$from_date = isset($_GET['from_date']) ? $conn->real_escape_string($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? $conn->real_escape_string($_GET['to_date']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$groups = isset($_GET['groups']) ? $conn->real_escape_string($_GET['groups']) : '';
$descr = isset($_GET['descr']) ? $conn->real_escape_string($_GET['descr']) : '';
$account = isset($_GET['account']) ? $conn->real_escape_string($_GET['account']) : '';

// Construct the SQL query
$query = "SELECT 
            t.idTrans, 
            t.t_date, 
            t.descr, 
            t.amount, 
            t.account,
            t.groups, 
            c.category 
          FROM transactions t
          LEFT JOIN categories c ON t.leg_cat = c.idCat 
          WHERE t.leg_idUser = $user_id";

// Apply filters if provided
if (!empty($from_date)) {
    $query .= " AND t.t_date >= '$from_date'";
}

if (!empty($to_date)) {
    $query .= " AND t.t_date <= '$to_date'";
}

if ($category > 0) {
    $query .= " AND t.leg_cat = $category";
}

if (!empty($descr)) {
    $query .= " AND t.descr LIKE '%$descr%'";
}

if (!empty($account)) {
    $query .= " AND t.account = '$account'";
}

if ($groups !== '') {
    $query .= " AND (t.groups = '$groups' OR (t.groups IS NULL AND '$groups' = 'NULL'))";
}

// Add ordering and pagination
$query .= " ORDER BY t.t_date DESC LIMIT $records_per_page OFFSET $offset";

// Execute the query
$result = $conn->query($query);

// Query to get the total number of records (for pagination)
$total_query = "SELECT COUNT(*) as total FROM transactions t
                LEFT JOIN categories c ON t.leg_cat = c.idCat 
                WHERE t.leg_idUser = $user_id";

// Apply filters to the total count query
if (!empty($from_date)) {
    $total_query .= " AND t.t_date >= '$from_date'";
}

if (!empty($to_date)) {
    $total_query .= " AND t.t_date <= '$to_date'";
}

if ($category > 0) {
    $total_query .= " AND t.leg_cat = $category";
}

if (!empty($descr)) {
    $total_query .= " AND t.descr LIKE '%$descr%'";
}

if (!empty($account)) {
    $total_query .= " AND t.account = '$account'";
}

if ($groups !== '') {
    $query .= " AND (t.groups = '$groups' OR (t.groups IS NULL AND '$groups' = 'NULL'))";
}

$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// Calculate the total number of pages
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
                <div class="m-header">
                    <div class="trans-history">Transaction History</div>
                    <div class="trans-center">
                        <div class="cont-form-filter">
                        <form method="GET" action="transactions.php">
                            <!-- Search Descr -->
                            <input type="text" class="tptxt" name="descr" value="<?php echo $_GET['descr'] ?? ''; ?>">

                            <!-- Date filter -->
                            <label for="from_date">From:</label>
                            <input type="date" name="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>">

                            <label for="to_date">To:</label>
                            <input type="date" name="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>">

                            <!-- Category filter -->
                            <select name="category">
                                <option value="">All Categories</option>
                                <?php
                                $catQuery = "SELECT DISTINCT idCat, category FROM categories WHERE leguser_id = ?";
                                $stmtCat = $conn->prepare($catQuery);
                                $stmtCat->bind_param("i", $_SESSION['user_id']);
                                $stmtCat->execute();
                                $catResult = $stmtCat->get_result();
                                while ($catRow = $catResult->fetch_assoc()) {
                                    $selected = ($_GET['category'] ?? '') == $catRow['category'] ? 'selected' : '';
                                    echo "<option value='{$catRow['idCat']}' $selected>{$catRow['category']}</option>";
                                }
                                ?>
                            </select>

                            <!-- Groups filter -->
                            <select name="groups">
                                <option value="">All Groups</option>
                                <?php
                                $groupQuery = "SELECT DISTINCT groups FROM transactions WHERE leg_idUser = ? and groups is not null";
                                $stmtGroup = $conn->prepare($groupQuery);
                                $stmtGroup->bind_param("i", $_SESSION['user_id']);
                                $stmtGroup->execute();
                                $groupResult = $stmtGroup->get_result();
                                while ($groupRow = $groupResult->fetch_assoc()) {
                                    $selected = ($_GET['groups'] ?? '') == $groupRow['groups'] ? 'selected' : '';
                                    echo "<option value='{$groupRow['groups']}' $selected>{$groupRow['groups']}</option>";
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
                            <th>Group</th>
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
                                <td><?php echo htmlspecialchars($row['groups']); ?></td>
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
