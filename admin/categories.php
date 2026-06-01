<?php
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();

$msg = '';

if(isset($_POST['add_category'])){
    $name = $_POST['name'];
    $conn->query("INSERT INTO categories (name) VALUES ('$name')");
    $msg = "Category added.";
}

if(isset($_POST['update_category'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $conn->query("UPDATE categories SET name='$name' WHERE id='$id'");
    $msg = "Category updated.";
}

if(isset($_GET['Trash'])){
    $id = $_GET['Trash'];
    $conn->query("DELETE FROM categories WHERE id='$id'");
    header("Location: categories.php");
    exit();
}
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="dashboard-grid" style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
  <div class="sidebar glass">
    <h3>Admin Menu</h3>
    <ul style="margin-top: 1rem;">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="categories.php" class="active">Manage Categories</a></li>
      <li><a href="add_medicine.php">Add Medicine</a></li>
      <li><a href="inventory.php">Manage Inventory</a></li>
      <li><a href="manage_order.php">Manage Orders</a></li>
      <li><a href="stock_requests.php">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <h2 style="margin-bottom: 2rem;">Manage Categories</h2>
    
    <?php if($msg): ?>
      <p style="color: green; margin-bottom: 1rem;"><?php echo $msg; ?></p>
    <?php endif; ?>

    <div style="margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 8px;">
        <h3>Add New Category</h3>
        <form method="POST" style="display: flex; gap: 1rem; margin-top: 1rem;">
            <input type="text" name="name" placeholder="Category Name" required style="flex: 1; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
            <button type="submit" name="add_category" class="btn" style="padding: 0.5rem 1.5rem;">Add</button>
        </form>
    </div>

    <div class="table-container">
      <?php
      $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
      if($result->num_rows > 0){
          echo "<table>
          <thead>
          <tr><th>ID</th><th>Category Name</th><th>Action</th></tr>
          </thead>
          <tbody>";
          
          while($row = $result->fetch_assoc()){
              echo "<tr>
              <td>{$row['id']}</td>
              <td>
                <form method='POST' style='display:flex; gap:0.5rem;'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <input type='text' name='name' value='{$row['name']}' required style='padding: 0.2rem; border-radius: 4px; border: 1px solid #ccc;'>
                    <button type='submit' name='update_category' class='btn' style='padding: 0.2rem 0.5rem; font-size: 0.8rem;'>Update</button>
                </form>
              </td>
              <td><a href='?delete={$row['id']}' style='color: red; text-decoration: underline;' onclick='return confirm(\"Are you sure?\");'>Delete</a></td>
              </tr>";
          }
          echo "</tbody></table>";
      } else {
          echo "<p>No categories found.</p>";
      }
      ?>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
