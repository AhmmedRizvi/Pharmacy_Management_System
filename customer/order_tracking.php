<?php
include('../config.php');
$order_id = $_GET['id'];
$order = $conn->query("SELECT * FROM orders WHERE id='$order_id'")->fetch_assoc();

echo "<h2>Order Tracking</h2>";
echo "<p>Order #{$order['id']} - Status: {$order['status']}</p>";
?>
