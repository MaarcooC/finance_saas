<?php
require_once "../../config/config.php";
require_once "check_auth.php";

//** Function for the main query */
function Index_query($conn)
{
    $user_id = $_SESSION['user_id'];
    $query = "SELECT amount, t_date, category, groups FROM transactions, categories WHERE leg_cat = idCat and leg_idUser = $user_id";

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

    if (isset($_GET['groups']) && $_GET['groups'] !== '') {
        $groups = $_GET['groups'];
        $query .= " AND groups = '$groups'";
    }

    if (isset($_GET['account']) && $_GET['account'] !== '') {
        $account = $_GET['account'];
        $query .= " AND account = '$account'";
    }

    $result = $conn->query($query);
    
    // Converts the result in an assoc
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data; // return assoc array
}

/** Function that returns the income */
function income($data)
{
    $sum = 0;
    foreach ($data as $row) {
        if ($row['amount'] > 0) $sum += $row['amount'];
    }
    return round($sum, 2);
}

/** Function that returns the outcome */
function outcome($data)
{
    $sum = 0;
    foreach ($data as $row) {
        if ($row['amount'] < 0) $sum += $row['amount'];
    }

    return round($sum, 2);
}

//** function that prints the first of jenuary of the current year (date filters defaul) */
function getFirstJanuary() : void
{
    echo date('Y-01-01');
}

//** function that prints the last day of december of the current year (date filters defaul) */
function getLastDec() : void
{
    echo date('Y-12-31');
}