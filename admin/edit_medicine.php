<?php
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();

if(!isset($_GET['id'])){
    header("Location: inventory.php");
    exit();
}

$id = $_GET['id'];
$msg = '';

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $expiry = $_POST['expiry_date'];

    $image_sql = "";
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
            $image_sql = ", image='$image_path'";
        }
    }

    $sql = "UPDATE medicines SET name='$name', brand='$brand', category='$category', price='$price', stock='$stock', expiry_date='$expiry' $image_sql WHERE id='$id'";
    if($conn->query($sql)){
        $msg = "<p style='color: green; margin-bottom: 1rem;'>Medicine updated successfully!</p>";
    } else {
        $msg = "<p style='color: red; margin-bottom: 1rem;'>Error: ".$conn->error."</p>";
    }
}

$result = $conn->query("SELECT * FROM medicines WHERE id='$id'");
if($result->num_rows == 0){
    header("Location: inventory.php");
    exit();
}
$med = $result->fetch_assoc();
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
      <li><a href="stock_requests.php">Stock Requests</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Edit Medicine</h2>
        <a href="inventory.php" class="btn btn-secondary">Back to Inventory</a>
    </div>
    
    <div class="form-container" style="margin: 0; box-shadow: none; border: 1px solid #eee;">
      <?php echo $msg; ?>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Medicine Name</label>
          <input type="text" name="name" value="<?php echo $med['name']; ?>" required>
        </div>
        <div class="form-group">
          <label>Brand</label>
          <input type="text" name="brand" value="<?php echo $med['brand']; ?>" required>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
            <option value="">Select Category</option>
            <?php
            $cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
            while($c = $cats->fetch_assoc()){
                $selected = ($c['name'] == $med['category']) ? 'selected' : '';
                echo "<option value='{$c['name']}' {$selected}>{$c['name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Medicine Photo</label>
          <?php if($med['image']): ?>
            <div style="margin-bottom: 1rem;">
                <img src="../<?php echo $med['image']; ?>" alt="Current Photo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                <p style="font-size: 0.8rem; color: #666;">Current Photo</p>
            </div>
          <?php endif; ?>
          <input type="file" name="image" accept="image/*">
        </div>
        <div class="form-group">
          <label>Price (৳)</label>
          <input type="number" step="0.01" name="price" value="<?php echo $med['price']; ?>" required>
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input type="number" name="stock" value="<?php echo $med['stock']; ?>" required>
        </div>
        <div class="form-group">
          <label>Expiry Date</label>
          <input type="date" name="expiry_date" value="<?php echo $med['expiry_date']; ?>" required>
        </div>
        <button type="submit" name="update" class="btn">Update Medicine</button>
      </form>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
