<?php 
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="dashboard-grid" style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
  <div class="sidebar glass">
    <h3>Admin Menu</h3>
    <ul style="margin-top: 1rem;">
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <li><a href="categories.php">Manage Categories</a></li>
      <li><a href="add_medicine.php">Add Medicine</a></li>
      <li><a href="inventory.php">Manage Inventory</a></li>
      <li><a href="manage_order.php">Manage Orders</a></li>
      <li><a href="stock_requests.php">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <h2 style="margin-bottom: 2rem;">Admin Dashboard</h2>
    
    <?php
    $medicines_count = $conn->query("SELECT COUNT(*) as count FROM medicines")->fetch_assoc()['count'];
    $orders_count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='pending'")->fetch_assoc()['count'];
    $users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='customer'")->fetch_assoc()['count'];
    ?>
    
    <div class="stat-grid">
      <div class="stat-card">
        <h3>Total Medicines</h3>
        <p><?php echo $medicines_count; ?></p>
      </div>
      <div class="stat-card">
        <h3>Pending Orders</h3>
        <p><?php echo $orders_count; ?></p>
      </div>
      <div class="stat-card">
        <h3>Total Customers</h3>
        <p><?php echo $users_count; ?></p>
      </div>
    </div>
    
    <?php
    $today = date('Y-m-d');
    $result = $conn->query("SELECT * FROM medicines WHERE expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY)");
    
    if($result->num_rows > 0) {
        echo "<h3 style='margin-bottom: 1rem; color: #DC2626;'>Expiry Alerts (Next 30 Days)</h3>";
        echo "<ul style='list-style-position: inside; color: #DC2626;'>";
        while($row = $result->fetch_assoc()){
            echo "<li><strong>{$row['name']}</strong> (Expiry: {$row['expiry_date']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3>Expiry Alerts</h3>";
        echo "<p style='color: var(--text-light);'>No medicines expiring soon.</p>";
    }
    
    $low_stock = $conn->query("SELECT * FROM medicines WHERE stock <= 10");
    if($low_stock->num_rows > 0) {
        echo "<h3 style='margin-top: 2rem; margin-bottom: 1rem; color: #D97706;'>Low Stock Alerts (<= 10)</h3>";
        echo "<ul style='list-style-position: inside; color: #D97706;'>";
        while($row = $low_stock->fetch_assoc()){
            echo "<li><strong>{$row['name']}</strong> (Stock: {$row['stock']})</li>";
        }
        echo "</ul>";
    }
    ?>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
