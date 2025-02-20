<?php
// db connection settings
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'finance_saas');
define('BASE_PATH', 'C:/Programming/finance_saas');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verify the connection with db
if ($conn->connect_error) {
    echo "Connection failed";
    echo '
        <button>
            <a href="login.php">Back to login</a>
        </button>
    ';
}

// manage sessions
session_start();