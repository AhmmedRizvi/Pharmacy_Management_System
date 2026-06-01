<?php
include('config.php');
include('includes/auth.php');

if(isAdmin()){
    header("Location: admin/dashboard.php");
    exit();
}
if(isset($_GET['remove'])){
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

if(isset($_POST['update_cart'])){
    $error = '';
    foreach($_POST['qty'] as $id => $qty){
        $stock_check = $conn->query("SELECT stock FROM medicines WHERE id='$id'")->fetch_assoc();
        if($qty > $stock_check['stock']){
            $error = "error=Cannot exceed available stock.";
            $_SESSION['cart'][$id] = $stock_check['stock'];
        } else if($qty <= 0){
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    $redirect = "Location: cart.php";
    if($error) $redirect .= "?" . $error;
    header($redirect);
    exit();
}
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section>
  <div class="form-container glass" style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
    <h2 class="section-title">Your Cart</h2>
    
    <?php if(empty($_SESSION['cart'])): ?>
      <p style="text-align: center;">Your cart is empty. <a href="index.php">Continue shopping</a>.</p>
    <?php else: ?>
      <form method="POST">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
          <thead>
            <tr style="border-bottom: 2px solid #eee;">
              <th style="padding: 1rem; text-align: left;">Medicine</th>
              <th style="padding: 1rem; text-align: center;">Quantity</th>
              <th style="padding: 1rem; text-align: right;">Price</th>
              <th style="padding: 1rem; text-align: right;">Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total = 0;
            $ids = implode(',', array_keys($_SESSION['cart']));
            $result = $conn->query("SELECT * FROM medicines WHERE id IN ($ids)");
            
            while($row = $result->fetch_assoc()){
                $id = $row['id'];
                $qty = $_SESSION['cart'][$id];
                $subtotal = $qty * $row['price'];
                $total += $subtotal;
                
                $img_src = $row['image'] ? $row['image'] : 'https://cdn-icons-png.flaticon.com/512/883/883356.png';
                
                echo "
                <tr style='border-bottom: 1px solid #eee;'>
                  <td style='padding: 1rem; display: flex; align-items: center; gap: 1rem;'>
                    <img src='{$img_src}' alt='{$row['name']}' style='width: 50px; height: 50px; object-fit: contain; border-radius: 4px;'>
                    <div>
                        <strong>{$row['name']}</strong><br>
                        <small>{$row['brand']} | ৳{$row['price']}</small>
                    </div>
                  </td>
                  <td style='padding: 1rem; text-align: center;'>
                    <input type='number' name='qty[{$id}]' value='{$qty}' min='1' max='{$row['stock']}' style='width: 60px; padding: 0.3rem;'>
                  </td>
                  <td style='padding: 1rem; text-align: right;'>৳{$row['price']}</td>
                  <td style='padding: 1rem; text-align: right; font-weight: bold;'>৳" . number_format($subtotal, 2) . "</td>
                  <td style='padding: 1rem; text-align: center;'>
                    <a href='?remove={$id}' style='color: red;'>✕</a>
                  </td>
                </tr>
                ";
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" style="padding: 1rem; text-align: right; font-weight: bold; font-size: 1.2rem;">Grand Total:</td>
              <td style="padding: 1rem; text-align: right; font-weight: bold; font-size: 1.2rem; color: var(--primary-color);">৳<?php echo number_format($total, 2); ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
        
        <div style="display: flex; justify-content: space-between;">
          <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
          <a href="checkout.php" class="btn">Proceed to Checkout</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</section>

<?php include('includes/footer.php'); ?>
