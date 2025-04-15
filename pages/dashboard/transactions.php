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
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$groups = isset($_GET['groups']) ? $_GET['groups'] : '';
$descr = isset($_GET['descr']) ? $_GET['descr'] : '';
$account = isset($_GET['account']) ? $_GET['account'] : '';

// Constructing the SQL query to fetch transactions with group and category information
$query = "SELECT 
            t.idTrans, 
            t.t_date, 
            t.descr, 
            t.amount, 
            t.account,
            g.groupname,
            c.category 
          FROM transactions t
          LEFT JOIN categories c ON t.leg_cat = c.idCat 
          LEFT JOIN groups g ON t.leg_idgroup = g.idGroup
          WHERE t.leg_idUser = $user_id";

// Applying filters if provided
if (!empty($from_date)) {
    $query .= " AND t.t_date >= '$from_date'";  // Filter by 'from_date'
}

if (!empty($to_date)) {
    $query .= " AND t.t_date <= '$to_date'";  // Filter by 'to_date'
}

if ($category > 0) {
    $query .= " AND t.leg_cat = $category";  // Filter by category ID
}

if (!empty($descr)) {
    $query .= " AND t.descr LIKE '%$descr%'";  // Filter by description
}

if (!empty($account)) {
    $query .= " AND t.account = '$account'";  // Filter by account
}

// Filter by group if provided
if ($groups !== '') {
    $query .= " AND (g.groupname = '$groups' OR (g.groupname IS NULL AND '$groups' = 'NULL'))"; // Adjusting filter for group
}

// Adding ordering and pagination to the query
$query .= " ORDER BY t.t_date DESC LIMIT $records_per_page OFFSET $offset";  // Sorting by transaction date and limiting results for pagination

// Execute the query
$result = $conn->query($query);

// Query to get the total number of records for pagination purposes
$total_query = "SELECT COUNT(*) as total 
                FROM transactions t
                LEFT JOIN categories c ON t.leg_cat = c.idCat 
                LEFT JOIN groups g ON t.leg_idgroup = g.idGroup
                WHERE t.leg_idUser = $user_id";  // Filtering by the logged-in user's ID

// Applying filters to the total query
if (!empty($from_date)) {
    $total_query .= " AND t.t_date >= '$from_date'";  // Filter by 'from_date'
}

if (!empty($to_date)) {
    $total_query .= " AND t.t_date <= '$to_date'";  // Filter by 'to_date'
}

if ($category > 0) {
    $total_query .= " AND t.leg_cat = $category";  // Filter by category ID
}

if (!empty($descr)) {
    $total_query .= " AND t.descr LIKE '%$descr%'";  // Filter by description
}

if (!empty($account)) {
    $total_query .= " AND t.account = '$account'";  // Filter by account
}

// Get the total number of records after applying filters
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// Calculate total pages based on the number of records and records per page
$total_pages = ceil($total_records / $records_per_page);

// Query to fetch distinct groups for the filter dropdown
$groupQuery = "SELECT DISTINCT g.idGroup, g.groupname
               FROM groups g 
               LEFT JOIN transactions t ON t.leg_idgroup = g.idGroup
               WHERE t.leg_idUser = ? AND g.groupname IS NOT NULL";  // Ensuring that groups are not null
$stmtGroup = $conn->prepare($groupQuery);
$stmtGroup->bind_param("i", $_SESSION['user_id']);  // Binding the user ID to the query
$stmtGroup->execute();
$groupResult = $stmtGroup->get_result();

// Populate the group filter dropdown with groups from the database
while ($groupRow = $groupResult->fetch_assoc()) {
    // Check if the group is selected in the filter
    $selected = ($_GET['groups'] ?? '') == $groupRow['groupname'] ? 'selected' : '';
}
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

<!-- Header Section -->
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

<!-- Content Section -->
<div class="content">
    <div class="main">
        <!-- Filter Header -->
        <div class="m-header">
            <div class="trans-history">Transaction History</div>
            <div class="trans-center">
                <div class="cont-form-filter">
                    <!-- Filter Form -->
                    <form method="GET" action="transactions.php">
                        <!-- Description Filter -->
                        <input type="text" class="tptxt" name="descr" value="<?php echo $_GET['descr'] ?? ''; ?>">

                        <!-- Date Filters -->
                        <label for="from_date">From:</label>
                        <input type="date" name="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>">

                        <label for="to_date">To:</label>
                        <input type="date" name="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>">

                        <!-- Category Filter -->
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php
                            // Display categories in the dropdown
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

                        <!-- Group Filter -->
                        <select name="groups">
                            <option value="">All Groups</option>
                            <?php
                            // Display groups in the dropdown
                            $groupQuery = "SELECT DISTINCT g.idGroup, g.groupname 
                                           FROM groups g 
                                           LEFT JOIN transactions t ON t.leg_idgroup = g.idGroup
                                           WHERE t.leg_idUser = ? AND g.groupname IS NOT NULL";
                            $stmtGroup = $conn->prepare($groupQuery);
                            $stmtGroup->bind_param("i", $_SESSION['user_id']);
                            $stmtGroup->execute();
                            $groupResult = $stmtGroup->get_result();
                            while ($groupRow = $groupResult->fetch_assoc()) {
                                $selected = ($_GET['groups'] ?? '') == $groupRow['groupname'] ? 'selected' : '';
                                echo "<option value='{$groupRow['groupname']}' $selected>{$groupRow['groupname']}</option>";
                            }
                            ?>
                        </select>

                        <!-- Account Filter -->
                        <select name="account">
                            <option value="">All Accounts</option>
                            <?php
                            // Display accounts in the dropdown
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

                        <!-- Submit and Reset Buttons -->
                        <button type="submit">Filter</button>
                        <a href="transactions.php" class="reset-btn">Reset</a>
                    </form>
                </div>    
            </div>
            <div class="m-h-right">
                <button><a href="new_transaction.php">New Transaction</a></button>
            </div>
        </div>

        <!-- Transactions Table -->
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
                        <td><?php echo htmlspecialchars($row['groupname']); ?></td>  <!-- Display group name -->
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

        <!-- Pagination Controls -->
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