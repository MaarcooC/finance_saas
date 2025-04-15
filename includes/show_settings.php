<?php
require_once "../../config/config.php";

// Function to show the table with all categories
function show_cat($conn) {
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT idCat, category FROM categories WHERE leguser_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
                <tr>
                    <td>" . htmlspecialchars($row['category']) . "</td>
                    <td>
                        <a href='delete_category.php?idCat=" . $row['idCat'] . "' onclick='return confirm(\"Are you sure you want to delete this category?\");'>
                            <img src='../../assets/img/bin.png' alt='delete category'>
                        </a>
                    </td>
                </tr>
            ";
        }
    } else {
        echo "<tr><td colspan='2'>No categories found.</td></tr>";
    }
    
    $stmt->close();
}

// Function to show the table with all groups
function show_group($conn) {
    // Ensure no pending results
    while ($conn->more_results()) {
        $conn->next_result();
        if ($result = $conn->store_result()) {
            $result->free();
        }
    }

    $user_id = $_SESSION['user_id'];
    $query = "SELECT idGroup, groupname FROM groups WHERE leg_iduser = ?";
    $stmt = $conn->prepare($query);

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
                <tr>
                    <td>" . htmlspecialchars($row['groupname']) . "</td>
                    <td>
                        <a href='delete_group.php?idGroup=" . $row['idGroup'] . "' onclick='return confirm(\"Are you sure you want to delete this group?\");'>
                            <img src='../../assets/img/bin.png' alt='delete group'>
                        </a>
                    </td>
                </tr>
            ";
        }
    } else {
        echo "<tr><td colspan='2'>No groups found.</td></tr>";
    }

    $result->free();
    $stmt->close();
}
?>