<?php
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();

if(isset($_POST['resolve'])){
    $request_id = $_POST['request_id'];
    $medicine_id = $_POST['medicine_id'];
    $qty = (int)$_POST['qty'];
    
    // Update the stock and mark request as resolved
    if($qty > 0){
        $conn->query("UPDATE medicines SET stock = stock + $qty WHERE id='$medicine_id'");
    }
    $conn->query("UPDATE stock_requests SET status='resolved' WHERE id='$request_id'");
}
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="dashboard-grid" style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
  <div class="sidebar glass">
    <h3>Admin Menu</h3>
    <ul style="margin-top: 1rem;">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="categories.php">Manage Categories</a></li>
      <li><a href="add_medicine.php">Add Medicine</a></li>
      <li><a href="inventory.php">Manage Inventory</a></li>
      <li><a href="manage_order.php">Manage Orders</a></li>
      <li><a href="stock_requests.php" class="active">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <h2 style="margin-bottom: 2rem;">Customer Stock Requests</h2>

    <div class="table-container">
      <?php
      $result = $conn->query("SELECT r.*, u.name as customer_name, m.name as medicine_name, m.id as med_id 
                              FROM stock_requests r 
                              JOIN users u ON r.user_id = u.id 
                              JOIN medicines m ON r.medicine_id = m.id
                              ORDER BY r.created_at DESC");
      
      echo "<table>
      <thead>
      <tr><th>Request ID</th><th>Customer</th><th>Medicine Requested</th><th>Date</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>";
      
      while($row = $result->fetch_assoc()){
          $status_class = $row['status'] == 'resolved' ? 'completed' : 'pending';
          
          echo "<tr>
          <td>#{$row['id']}</td>
          <td>{$row['customer_name']}</td>
          <td><strong>{$row['medicine_name']}</strong></td>
          <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
          <td><span class='badge {$status_class}'>{$row['status']}</span></td>
          <td>";
          if($row['status'] == 'pending') {
            echo "
            <form method='POST' style='display:flex; gap:0.5rem; align-items:center;'>
              <input type='hidden' name='request_id' value='{$row['id']}'>
              <input type='hidden' name='medicine_id' value='{$row['med_id']}'>
              <input type='number' name='qty' placeholder='Qty added' min='1' required style='width: 90px; padding: 0.2rem;'>
              <button type='submit' name='resolve' class='btn' style='padding: 0.2rem 0.5rem; font-size: 0.8rem;'>Resolve & Add Stock</button>
            </form>";
          } else {
              echo "Resolved";
          }
          echo "</td>
          </tr>";
      }
      echo "</tbody></table>";
      ?>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
