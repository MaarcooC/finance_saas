<?php
require_once "../../config/config.php";
require_once "check_auth.php";

/**
 * Main function to fetch filtered transaction data for the dashboard
 */
function Index_query($conn)
{
    $user_id = $_SESSION['user_id'];

    // Construct the SQL query to fetch transactions with group and category information
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

    // apply filters
    if (isset($_GET['from_date']) && $_GET['from_date'] !== '') {
        $from_date = $_GET['from_date'];
        $query .= " AND t_date >= '$from_date'";
    }

    if (isset($_GET['to_date']) && $_GET['to_date'] !== '') {
        $to_date = $_GET['to_date'];
        $query .= " AND t_date <= '$to_date'";
    }

    if (isset($_GET['category']) && $_GET['category'] !== '') {
        $category = $_GET['category'];
        $query .= " AND category = '$category'";
    }

    if (isset($_GET['groupname']) && $_GET['groupname'] !== '') {
        $groupname = $_GET['groupname'];
        $query .= " AND g.groupname = '$groupname'";
    }

    if (isset($_GET['account']) && $_GET['account'] !== '') {
        $account = $_GET['account'];
        $query .= " AND account = '$account'";
    }

    // Execute the query
    $result = $conn->query($query);
    if (!$result) {
        die("Query Failed: " . $conn->error);
    } 

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

/**
 * Calculates total income from transaction data
 */
function income($data)
{
    $sum = 0;
    foreach ($data as $row) {
        if ($row['amount'] > 0) {
            $sum += $row['amount'];
        }
    }
    return round($sum, 2);
}

/**
 * Calculates total outcome (expenses) from transaction data
 */
function outcome($data)
{
    $sum = 0;
    foreach ($data as $row) {
        if ($row['amount'] < 0) {
            $sum += $row['amount'];
        }
    }
    return round($sum, 2);
}

/**
 * Returns the first day of the current year (for default filter)
 */
function getFirstJanuary(): void
{
    echo date('Y-01-01');
}

/**
 * Returns the last day of the current year (for default filter)
 */
function getLastDec(): void
{
    echo date('Y-12-31');
}