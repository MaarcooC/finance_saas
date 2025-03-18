<?php
require_once "../../config/config.php";
require_once "../../includes/check_auth.php";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    // Validate and sanitize input (for security)
    $category = $conn->real_escape_string($category);

    // Prepare the SQL query to insert the data
    $query = "INSERT INTO categories (category, leguser_id)
              VALUES ('$category', $user_id)";

    // check if it already exist
    $check_query = "SELECT idCat FROM categories WHERE category = ? AND leguser_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("si", $category, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Category already exists!'); window.location.href='settings.php';</script>";
    } else {
        // Execute the query
        if ($conn->query($query) === true) {
            echo "<script>alert('Transaction added successfully!'); window.location.href='settings.php';</script>";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    }
}
?>
