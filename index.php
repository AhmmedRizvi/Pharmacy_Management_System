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
        header("Location: index.php?added=1");
    } else {
        header("Location: index.php?error=Not enough stock available.");
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
    header("Location: index.php?requested=1");
    exit();
}
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content glass" style="background: rgba(255,255,255,0.9);">
    <h1>Your Health, Our Priority</h1>
    <p>Access quality healthcare products and medicines from the comfort of your home.</p>
    <form method="GET" action="medicine.php" class="search-bar">
      <input type="text" name="search" placeholder="Search medicines, brands, categories...">
      <button type="submit">Search</button>
    </form>
  </div>
</section>

<!-- Pharmacy Status -->
<section class="status" style="text-align: center; margin-top: -2rem;">
  <?php
    date_default_timezone_set('Asia/Dhaka');
    $currentTime = date("H:i");
    $open = "08:00";
    $close = "22:00";
    if($currentTime >= $open && $currentTime <= $close){
        echo "<span class='badge completed' style='font-size: 1.1rem;'>🟢 Pharmacy is Open (8:00 AM - 10:00 PM)</span>";
    } else {
        echo "<span class='badge cancelled' style='font-size: 1.1rem;'>🔴 Pharmacy is Closed (Working Hours: 8:00 AM - 10:00 PM)</span>";
    }
  ?>
</section>

<!-- Featured Products -->
<section class="featured">
  <h2 class="section-title">Featured Medicines</h2>
  
  <div class="product-grid">
    <?php
    $result = $conn->query("SELECT * FROM medicines ORDER BY id DESC LIMIT 8");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $image = $row['image'] ? $row['image'] : 'assets/images/default_medicine.png';
            // If the local default doesn't exist, use an online placeholder
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
        echo "<p style='text-align: center; width: 100%;'>No medicines available at the moment.</p>";
    }
    ?>
  </div>
</section>

<!-- Assistance -->
<section class="assistance" style="text-align: center; background: white; margin-top: 2rem; border-radius: 16px;">
  <h2 class="section-title" style="margin-bottom: 1rem;">Need Assistance?</h2>
  <p style="margin-bottom: 2rem;">Our pharmacists are available for consultation.</p>
  <a href="contact.php" class="btn btn-secondary">Get in touch</a>
</section>

<?php include('includes/footer.php'); ?>
