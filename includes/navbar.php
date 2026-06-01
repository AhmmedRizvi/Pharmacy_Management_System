<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
$cart_count = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $qty){
        $cart_count += $qty;
    }
}
?>
<nav class="navbar">
  <a href="/sad_proj/index.php" style="text-decoration: none; color: inherit;">
    <div class="logo" style="display: flex; align-items: center;">
      Sheba Clinic
      <?php if(isset($_SESSION['role'])): ?>
        <?php if($_SESSION['role'] === 'admin'): ?>
          <span style="font-size: 0.7rem; background: #EF4444; color: white; padding: 0.2rem 0.4rem; border-radius: 4px; margin-left: 0.5rem; letter-spacing: 1px;">ADMIN</span>
        <?php else: ?>
          <span style="font-size: 0.7rem; background: #3B82F6; color: white; padding: 0.2rem 0.4rem; border-radius: 4px; margin-left: 0.5rem; letter-spacing: 1px;">CUSTOMER</span>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </a>
  <ul>
    <li><a href="/sad_proj/index.php">Home</a></li>
    <li><a href="/sad_proj/medicine.php">Medicines</a></li>
    
    <?php if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
    <li>
      <a href="/sad_proj/cart.php">Cart 
        <?php if($cart_count > 0): ?>
          <span class="cart-count"><?php echo $cart_count; ?></span>
        <?php endif; ?>
      </a>
    </li>
    <?php endif; ?>

    <?php if(isset($_SESSION['user_id'])): ?>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <li><a href="/sad_proj/admin/dashboard.php">Dashboard</a></li>
        <?php else: ?>
            <li><a href="/sad_proj/customer/dashboard.php">Dashboard</a></li>
        <?php endif; ?>
        <li><a href="/sad_proj/logout.php" class="btn" style="padding: 0.4rem 1rem; color: white;">Logout</a></li>
    <?php else: ?>
        <li><a href="/sad_proj/login.php" class="btn" style="padding: 0.4rem 1rem; color: white;">Login</a></li>
    <?php endif; ?>
  </ul>
</nav>
