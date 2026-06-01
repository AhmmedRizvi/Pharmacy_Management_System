<?php
include('config.php');
session_start();

if(isset($_POST['add_to_cart'])){
    $med_id = $_POST['medicine_id'];
    
    // Check available stock
    $stock_check = $conn->query("SELECT stock FROM medicines WHERE id='$med_id'")->fetch_assoc();
    $available_stock = $stock_check['stock'];
    
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }
    
    $current_qty = isset($_SESSION['cart'][$med_id]) ? $_SESSION['cart'][$med_id] : 0;
    
    if($current_qty < $available_stock){
        if(isset($_SESSION['cart'][$med_id])){
            $_SESSION['cart'][$med_id]++;
        } else {
            $_SESSION['cart'][$med_id] = 1;
        }
        header("Location: medicine.php?added=1");
    } else {
        header("Location: medicine.php?error=Not enough stock available.");
    }
    exit();
}

if(isset($_POST['request_restock'])){
    if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer'){
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $med_id = $_POST['medicine_id'];
    $conn->query("INSERT INTO stock_requests (user_id, medicine_id) VALUES ('$user_id', '$med_id')");
    header("Location: medicine.php?requested=1");
    exit();
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section class="featured">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
      <h2 class="section-title" style="margin: 0;">All Medicines</h2>
      <form method="GET" style="display: flex; gap: 0.5rem;">
          <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search medicines..." style="padding: 0.5rem; border-radius: 4px; border: 1px solid #ddd;">
          <button type="submit" class="btn" style="padding: 0.5rem 1rem;">Search</button>
      </form>
  </div>
  
  <div class="product-grid">
    <?php
    $sql = "SELECT * FROM medicines";
    if($search){
        $sql .= " WHERE (name LIKE '%$search%' OR brand LIKE '%$search%' OR category LIKE '%$search%')";
    }
    $sql .= " ORDER BY name ASC";
    
    $result = $conn->query($sql);
    
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $img_src = $row['image'] ? $row['image'] : 'https://cdn-icons-png.flaticon.com/512/883/883356.png';
            
            echo "
            <div class='product-card'>
              <div>
                <img src='{$img_src}' alt='{$row['name']}' style='width: 100%; height: 180px; object-fit: contain; margin-bottom: 1rem; border-radius: 8px;'>
                <h3>{$row['name']}</h3>
                <p class='brand'>{$row['brand']} | {$row['category']}</p>
                <p class='price'>৳{$row['price']}</p>
                <p style='font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;'>Available: {$row['stock']}</p>
              </div>
              <form method='POST'>
                <input type='hidden' name='medicine_id' value='{$row['id']}'>";
            
            $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
            
            if(!$is_admin) {
                if($row['stock'] > 0){
                    echo "<button type='submit' name='add_to_cart' style='width:100%;'>Add to Cart</button>";
                } else {
                    echo "<button type='submit' name='request_restock' style='width:100%; background: #F59E0B; color: white;'>Request Restock</button>";
                }
            }
            
            echo "</form>
            </div>
            ";
        }
    } else {
        echo "<p style='text-align: center; width: 100%; grid-column: 1 / -1;'>No medicines found matching your search.</p>";
    }
    ?>
  </div>
</section>

<?php include('includes/footer.php'); ?>
