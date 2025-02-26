<?php
// Render Column Chart
function renderChart2($result, $maxPoints = 20) {
    $incomeDataPoints = [];
    $outcomeDataPoints = [];

    if (empty($result)) return; // Exit if no data

    // Sort results by date
    usort($result, function($a, $b) {
        return strtotime($a['t_date']) - strtotime($b['t_date']);
    });

    // Get min and max dates
    $minDate = strtotime($result[0]['t_date']);
    $maxDate = strtotime($result[count($result) - 1]['t_date']);

    // Calculate interval size
    $timeRange = $maxDate - $minDate;
    $intervalSize = $timeRange > 0 ? floor($timeRange / $maxPoints) : 86400;

    // Initialize intervals
    $intervals = [];
    $currentTime = $minDate;
    while ($currentTime <= $maxDate) {
        $intervals[$currentTime] = ['income' => 0, 'outcome' => 0];
        $currentTime += $intervalSize;
    }

    // Aggregate transactions into intervals
    foreach ($result as $row) {
        $timestamp = strtotime($row['t_date']);
        $amount = floatval($row['amount']);
        $intervalStart = $minDate + floor(($timestamp - $minDate) / $intervalSize) * $intervalSize;
        
        if ($amount > 0) {
            $intervals[$intervalStart]['income'] += $amount;
        } else {
            $intervals[$intervalStart]['outcome'] += abs($amount);
        }
    }

    // Create data points
    foreach ($intervals as $timestamp => $values) {
        if ($values['income'] > 0) $incomeDataPoints[] = ["x" => $timestamp * 1000, "y" => $values['income']];
        if ($values['outcome'] > 0) $outcomeDataPoints[] = ["x" => $timestamp * 1000, "y" => $values['outcome']];
    }

    ?>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script>
    window.onload = function () {
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            backgroundColor: "rgb(21, 21, 61)",
            title: { text: "Transactions", fontColor: "rgb(235, 233, 248)", fontSize: 20 },
            axisY: { title: "Amount in Euros", titleFontColor: "rgb(235, 233, 248)", labelFontColor: "rgb(235, 233, 248)", prefix: "€", valueFormatString: "#,##0.##" },
            axisX: { title: "", titleFontColor: "rgb(235, 233, 248)", labelFontColor: "rgb(235, 233, 248)", lineColor: "rgb(235, 233, 248)", valueFormatString: "DD MMM YYYY", labelAngle: -45, minimum: <?php echo $minDate * 1000; ?>, maximum: <?php echo ($maxDate + $intervalSize) * 1000; ?> },
            legend: { cursor: "pointer", fontColor: "rgb(235, 233, 248)", verticalAlign: "center", horizontalAlign: "right", itemclick: function(e) { if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) { e.dataSeries.visible = false; } else { e.dataSeries.visible = true; } chart.render(); } },
            toolTip: { shared: true },
            data: [
                { type: "column", name: "Income", showInLegend: true, color: "rgb(102, 192, 84)", indexLabel: "{y}", yValueFormatString: "€#,##0.##", xValueFormatString: "DD MMM YYYY", xValueType: "dateTime", dataPoints: <?php echo json_encode($incomeDataPoints, JSON_NUMERIC_CHECK); ?> },
                { type: "column", name: "Outcome", showInLegend: true, color: "rgb(255, 99, 132)", indexLabel: "{y}", yValueFormatString: "€#,##0.##", xValueFormatString: "DD MMM YYYY", xValueType: "dateTime", dataPoints: <?php echo json_encode($outcomeDataPoints, JSON_NUMERIC_CHECK); ?> }
            ]
        });
        chart.render();
    }
    </script>
    <?php
}
?>