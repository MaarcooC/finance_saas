<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $date = $_POST['date_t'];
    $description = $_POST['descr'];
    $category = $_POST['category'];
    $groups = $_POST['groups'];
    $amount = $_POST['amount'];
    $account = $_POST['account'];
    $user_id = $_SESSION['user_id'];

    // Validate and sanitize input (for security)
    $date = $conn->real_escape_string($date);
    $description = $conn->real_escape_string($description);
    $category = $conn->real_escape_string($category);
    $groups = $conn->real_escape_string($groups);
    $amount = $conn->real_escape_string($amount);
    $account = $conn->real_escape_string($account);

    // Prepare the SQL query to insert the data
    $query = "INSERT INTO transactions (leg_idUser, t_date, descr, leg_cat, groups, amount, account)
              VALUES ('$user_id', '$date', '$description', '$category', '$groups', '$amount', '$account')";

    // Execute the query
    if ($conn->query($query) === true) {
        echo "<script>alert('Transaction added successfully!'); window.location.href='transactions.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>
