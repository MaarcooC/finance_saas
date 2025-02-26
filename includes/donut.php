<?php
function renderDonutChart($result) {
    if (empty($result)) return; // Exit if no data
    
    // Initialize category totals
    $categories = [];
    
    // Aggregate amounts by category (assuming $result has category and amount fields)
    foreach ($result as $row) {
        if ($row['category'] != 'Income') {
            $category = $row['category'];
            $amount = floatval($row['amount']);
            
            // Use absolute value for expenses, or positive only if that's your intent
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category] += abs($amount); // Using abs() like in your outcome example    
        }
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
        backgroundColor: "rgb(21, 21, 61)", // Custom background color
        animationEnabled: true,
        title: {
            text: "Outcomes by Category",
            fontColor: "rgb(235, 233, 248)",
            fontSize: 20
        },
        legend: {
            cursor: "pointer",
            fontColor: "rgb(235, 233, 248)",
            verticalAlign: "center",
            horizontalAlign: "right",
            // Remove percentages from the legend (just category names and amounts)
            legendText: "{label} : {y}",
        },
        data: [{
            type: "doughnut",
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
