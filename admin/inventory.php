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
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="categories.php">Manage Categories</a></li>
      <li><a href="add_medicine.php">Add Medicine</a></li>
      <li><a href="inventory.php" class="active">Manage Inventory</a></li>
      <li><a href="manage_order.php">Manage Orders</a></li>
      <li><a href="stock_requests.php">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
      <h2>Inventory Management</h2>
      <a href="add_medicine.php" class="btn">Add New</a>
    </div>

    <div class="table-container">
      <?php
      $result = $conn->query("SELECT * FROM medicines ORDER BY id DESC");
      
      echo "<table>
      <thead>
      <tr><th>Name</th><th>Brand</th><th>Category</th><th>Price</th><th>Stock</th><th>Expiry</th><th>Actions</th></tr>
      </thead>
      <tbody>";
      
      while($row = $result->fetch_assoc()){
          echo "<tr>
          <td><strong>{$row['name']}</strong></td>
          <td>{$row['brand']}</td>
          <td>{$row['category']}</td>
          <td>৳{$row['price']}</td>
          <td>{$row['stock']}</td>
          <td>{$row['expiry_date']}</td>
          <td>
            <a href='edit_medicine.php?id={$row['id']}' class='btn' style='padding: 0.3rem 0.6rem; font-size: 0.8rem;'>Edit</a>
            <a href='delete_medicine.php?id={$row['id']}' class='btn btn-danger' style='padding: 0.3rem 0.6rem; font-size: 0.8rem;' onclick='return confirm(\"Are you sure?\");'>Delete</a>
          </td>
          </tr>";
      }
      echo "</tbody></table>";
      ?>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
