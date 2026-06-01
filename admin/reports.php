<?php
include('../config.php');

// Fetch sales data
$sales = $conn->query("SELECT DATE(created_at) as date, SUM(total) as total_sales 
                       FROM orders WHERE status='completed' 
                       GROUP BY DATE(created_at) ORDER BY date ASC");

// Prepare arrays for Chart.js
$dates = [];
$totals = [];
while($row = $sales->fetch_assoc()){
    $dates[] = $row['date'];
    $totals[] = $row['total_sales'];
}

// Fetch stock data
$stock = $conn->query("SELECT name, stock FROM medicines ORDER BY stock ASC LIMIT 10");
$medicine_names = [];
$medicine_stock = [];
while($row = $stock->fetch_assoc()){
    $medicine_names[] = $row['name'];
    $medicine_stock[] = $row['stock'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h2>Sales Report</h2>
  <canvas id="salesChart"></canvas>

  <h2>Stock Report (Low Stock Medicines)</h2>
  <canvas id="stockChart"></canvas>

  <script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
          label: 'Daily Sales (৳)',
          data: <?php echo json_encode($totals); ?>,
          borderColor: '#00c853',
          backgroundColor: 'rgba(0,200,83,0.2)',
          fill: true,
          tension: 0.3
        }]
      }
    });

    // Stock Chart
    const stockCtx = document.getElementById('stockChart').getContext('2d');
    new Chart(stockCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($medicine_names); ?>,
        datasets: [{
          label: 'Stock Quantity',
          data: <?php echo json_encode($medicine_stock); ?>,
          backgroundColor: 'rgba(255,99,132,0.6)'
        }]
      }
    });
  </script>
</body>
</html>
