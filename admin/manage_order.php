<?php
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();

if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");
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
      <li><a href="manage_order.php" class="active">Manage Orders</a></li>
      <li><a href="stock_requests.php">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <h2 style="margin-bottom: 2rem;">Manage Orders</h2>

    <div class="table-container">
      <?php
      $result = $conn->query("SELECT o.*, u.name as customer_name, u.email, p.file_path as prescription_path 
                              FROM orders o 
                              JOIN users u ON o.user_id = u.id 
                              LEFT JOIN prescriptions p ON p.id = (SELECT id FROM prescriptions WHERE order_id = o.id ORDER BY id DESC LIMIT 1)
                              ORDER BY o.created_at DESC");
      
      echo "<table>
      <thead>
      <tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Date</th><th>Prescription</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>";
      
      while($row = $result->fetch_assoc()){
          $status_class = strtolower($row['status']);
          if($row['prescription_path']){
              $parts = explode('/', $row['prescription_path']);
              $encoded_path = $parts[0] . '/' . rawurlencode($parts[1] ?? '');
              $prescription_link = "<a href='/sad_proj/{$encoded_path}' target='_blank' style='color: var(--primary-color);'>View File</a>";
          } else {
              $prescription_link = "No file";
          }
          
          $items_query = $conn->query("SELECT m.name, oi.quantity FROM order_items oi JOIN medicines m ON oi.medicine_id = m.id WHERE oi.order_id = '{$row['id']}'");
          $item_names = [];
          while($item = $items_query->fetch_assoc()){
              $item_names[] = "{$item['name']} (x{$item['quantity']})";
          }
          $items_str = !empty($item_names) ? implode("<br>", $item_names) : "<em>Unknown</em>";
          
          echo "<tr>
          <td>#{$row['id']}</td>
          <td>{$row['customer_name']}<br><small>{$row['email']}</small></td>
          <td><span style='font-size: 0.9rem;'>{$items_str}</span></td>
          <td>৳{$row['total']}</td>
          <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
          <td>{$prescription_link}</td>
          <td><span class='badge {$status_class}'>{$row['status']}</span></td>
          <td>
            <form method='POST' style='display:flex; gap:0.5rem;'>
              <input type='hidden' name='order_id' value='{$row['id']}'>
              <select name='status' style='padding:0.2rem; border-radius:4px;'>
                <option value='pending' ".($row['status']=='pending'?'selected':'').">Pending</option>
                <option value='processing' ".($row['status']=='processing'?'selected':'').">Processing</option>
                <option value='completed' ".($row['status']=='completed'?'selected':'').">Completed</option>
                <option value='cancelled' ".($row['status']=='cancelled'?'selected':'').">Cancelled</option>
              </select>
              <button type='submit' name='update_status' class='btn' style='padding: 0.2rem 0.5rem; font-size: 0.8rem;'>Update</button>
            </form>
          </td>
          </tr>";
      }
      echo "</tbody></table>";
      ?>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
