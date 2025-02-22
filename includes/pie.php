<?php
function renderPieChart($result) {
    if (empty($result)) return; // Exit if no data
    
    // Initialize category totals
    $categories = [];
    
    // Aggregate amounts by category (assuming $result has category and amount fields)
    foreach ($result as $row) {
        $category = $row['category'];
        $amount = floatval($row['amount']);
        
        // Use absolute value for expenses, or positive only if that's your intent
        if (!isset($categories[$category])) {
            $categories[$category] = 0;
        }
        $categories[$category] += abs($amount); // Using abs() like in your outcome example
    }
    
    // Convert to data points format
    $dataPoints = [];
    foreach ($categories as $label => $amount) {
        $dataPoints[] = [
            "label" => $label,
            "y" => $amount
        ];
    }
    
    // If no data points, add a default one to avoid empty chart
    if (empty($dataPoints)) {
        $dataPoints[] = ["label" => "No Data", "y" => 0];
    }
?>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script>
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        backgroundColor: "rgb(21, 21, 61)", // From your column chart styling
        title: {
            text: "Expenses by Category",
            fontColor: "rgb(235, 233, 248)",
            fontSize: 20
        },
        subtitles: [{
            text: "Currency: Euro (€)",
            fontColor: "rgb(235, 233, 248)"
        }],
        legend: {
            cursor: "pointer",
            fontColor: "rgb(235, 233, 248)",
            verticalAlign: "center",
            horizontalAlign: "right"
        },
        data: [{
            type: "pie",
            showInLegend: true,
            legendText: "{label}",
            indexLabelFontSize: 16,
            indexLabelFontColor: "rgb(235, 233, 248)",
            indexLabel: "{label} - #percent%",
            yValueFormatString: "€#,##0.##",
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart.render();
}
</script>
<?php
}
?>