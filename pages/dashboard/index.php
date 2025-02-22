<?php
require_once("C:/xampp\htdocs/finance_saas\config\config.php");
require_once("C:/xampp\htdocs/finance_saas\includes\check_auth.php");
require_once("C:/xampp\htdocs/finance_saas\includes\index_query.php");
require_once("C:/xampp/htdocs/finance_saas/includes/spline_chart.php");
require_once("C:/xampp/htdocs/finance_saas/includes/multi_series.php");
require_once("C:/xampp/htdocs/finance_saas/includes/pie.php");
$result = Index_query($conn);
?>

<html lang="en">
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="../../assets/css/dashboard/overview.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/base.css">
        <link rel="stylesheet" href="../../assets/css/dashboard/filters.css">
        <script src="../../includes/select_graph.js" defer></script>
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
        <div class="index-content">
            <div class="index-filters-cont">
                <div class="trans-center">
                    <div class="cont-form-filter">
                        <form method="GET" action="index.php">
                            <!-- Date filter -->
                            <label for="from_date">From:</label>
                            <input type="date" name="from_date" id="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>">

                            <label for="to_date">To:</label>
                            <input type="date" name="to_date" id="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>">

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
                            <a href="index.php" class="reset-btn">Reset</a>
                        </form>
                    </div>    
                </div>
            </div>
            <div class="boxes">
                <div class="box">
                    <div class="cont-title">
                        <div class="c-title">Income</div>
                        <div><img src="../../assets/img/in.png" alt="income-icon"></div>
                    </div>
                    <div class="info" id="in">
                        <?php echo "+ ". income($result) . " €"; ?>
                    </div>
                </div>
                <div class="box">
                    <div class="cont-title">
                        <div class="c-title">Outcome</div>
                        <div><img src="../../assets/img/out.png" alt="outcome"></div>
                    </div>
                    <div class="info" id="out">
                        <?php 
                        $result1 = abs(outcome($result)); 
                        echo "- ". $result1 . " €";
                        ?>
                    </div>
                </div>
                <div class="box">
                    <?php
                    // calculate income and outcome
                    $income = income($result);
                    $outcome = outcome($result);

                    // Calculate the difference
                    // Add the absolute value of the outcome to avoid confusion with negative signs
                    $result2 = $income - abs($outcome);  // Add the absolute value of the outcome

                    if ($result2 >= 0) $string = "in";
                    else $string = "out"
                    ?>

                    <div class="cont-title">
                        <div class="c-title">Difference</div>
                        <div><img src="../../assets/img/<?php echo $string ?>.png" alt="<?php echo $string ?>-icon"></div>
                    </div>
                    <div class="info" id="<?php echo $string ?>">
                        <?php 
                        // Display the difference with the correct sign
                        if ($result2 >= 0) {
                            echo "+ ". number_format($result2, 2) ." €";
                        } else {
                            echo "- ". number_format(abs($result2), 2) ." €";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="cont-graphs">
            <div class="cont-g">
                <div class="g-title"></div>
                <div class="select-g">
                    <select id="graphs1" name="graphs1" onchange="sendReq()">
                        <option value="1" <?= (!isset($_SESSION['graph1']) || $_SESSION['graph1'] == 1) ? 'selected' : ''; ?>>Spline Graph</option>
                        <option value="2" <?= (isset($_SESSION['graph1']) && $_SESSION['graph1'] == 2) ? 'selected' : ''; ?>>Column Graph</option>
                        <option value="3" <?= (isset($_SESSION['graph1']) && $_SESSION['graph1'] == 3) ? 'selected' : ''; ?>>Pie Graph</option>
                    </select>
                </div>
                <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                <?php
                if (!isset($_SESSION['graph1'])) {
                    $_SESSION['graph1'] = 1; // default
                }

                switch ($_SESSION['graph1']) {
                case 1:
                    renderChart($result); // Spline chart
                    break;
                case 2:
                    renderChart2($result); // Column chart
                    break;
                case 3:
                    renderPieChart($result); // pie chart
                default:
                    echo "<p>No graph selected</p>";
                    break;
                }
                ?>

            </div>
        </div>
        </div>
    </body>
</html>