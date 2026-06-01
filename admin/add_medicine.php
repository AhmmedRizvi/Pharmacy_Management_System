<?php
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();

$msg = '';
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $expiry = $_POST['expiry_date'];
    
    $image_path = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $upload_dir = '../uploads/medicines/';
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", $name) . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
            $image_path = 'uploads/medicines/' . $file_name;
        }
    }

    $sql = "INSERT INTO medicines (name, brand, category, price, stock, expiry_date, image) 
            VALUES ('$name','$brand','$category','$price','$stock','$expiry', '$image_path')";
    if($conn->query($sql)){
        $msg = "<p style='color: green; margin-bottom: 1rem;'>Medicine added successfully!</p>";
    } else {
        $msg = "<p style='color: red; margin-bottom: 1rem;'>Error: ".$conn->error."</p>";
    }
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
      <li><a href="add_medicine.php" class="active">Add Medicine</a></li>
      <li><a href="inventory.php">Manage Inventory</a></li>
      <li><a href="manage_order.php">Manage Orders</a></li>
      <li><a href="stock_requests.php">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <h2 style="margin-bottom: 2rem;">Add New Medicine</h2>
    
    <div class="form-container" style="margin: 0; box-shadow: none; border: 1px solid #eee;">
      <?php echo $msg; ?>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Medicine Name</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Brand</label>
          <input type="text" name="brand" required>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
            <option value="">Select Category</option>
            <?php
            $cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
            while($c = $cats->fetch_assoc()){
                echo "<option value='{$c['name']}'>{$c['name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Medicine Photo</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <div class="form-group">
          <label>Price (৳)</label>
          <input type="number" step="0.01" name="price" required>
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input type="number" name="stock" required>
        </div>
        <div class="form-group">
          <label>Expiry Date</label>
          <input type="date" name="expiry_date" required>
        </div>
        <button type="submit" name="submit" class="btn">Add Medicine</button>
      </form>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
