<?php
require_once "../../config/config.php";

// function that shows the table with all categories
function show ($conn)  {
    $query = "select idCat, category from categories";

    // execute the query to get the records
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        // Properly escape the variables inside the string
        echo "
            <tr>
                <td>{$row['category']}</td>
                <td>
                    <a href='delete_category.php?idCat={$row['idCat']}' onclick='return confirm(\"Are you sure you want to delete this category?\");'>
                        <img src='../../assets/img/bin.png' alt='delete category'>
                    </a>
                </td>
            </tr>
        ";
    }
}