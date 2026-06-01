<?php
include('config.php');
include('includes/auth.php');
redirectIfNotLoggedIn();

if(isAdmin()){
    header("Location: admin/dashboard.php");
    exit();
}

if(empty($_SESSION['cart'])){
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['place_order'])){
    $user_id = $_SESSION['user_id'];
    
    // Calculate total
    $total = 0;
    $ids = implode(',', array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT * FROM medicines WHERE id IN ($ids)");
    while($row = $result->fetch_assoc()){
        $total += $_SESSION['cart'][$row['id']] * $row['price'];
    }
    
    // Check if file is uploaded
    $has_prescription = false;
    $file_path = '';
    if(isset($_FILES['prescription']) && $_FILES['prescription']['error'] == 0){
        $upload_dir = 'uploads/';
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }
        
        $clean_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['prescription']['name']));
        $file_name = time() . '_' . $clean_name;
        $target_file = $upload_dir . $file_name;
        
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if(in_array($file_type, ['jpg', 'jpeg', 'png', 'pdf'])){
            if(move_uploaded_file($_FILES['prescription']['tmp_name'], $target_file)){
                $has_prescription = true;
                $file_path = $target_file;
            } else {
                $error = "Failed to upload prescription.";
            }
        } else {
            $error = "Only JPG, PNG & PDF files are allowed.";
        }
    } else {
        $error = "Prescription is required to place an order.";
    }
    
    if(!$error){
        $conn->begin_transaction();
        try {
            // Insert Order
            $conn->query("INSERT INTO orders (user_id, status, total) VALUES ('$user_id', 'pending', '$total')");
            $order_id = $conn->insert_id;
            
            // Insert Order Items and Update Stock
            foreach($_SESSION['cart'] as $id => $qty){
                $conn->query("INSERT INTO order_items (order_id, medicine_id, quantity) VALUES ('$order_id', '$id', '$qty')");
                $conn->query("UPDATE medicines SET stock = stock - $qty WHERE id = '$id'");
            }
            
            // Insert Prescription
            $conn->query("INSERT INTO prescriptions (user_id, order_id, file_path) VALUES ('$user_id', '$order_id', '$file_path')");
            
            $conn->commit();
            unset($_SESSION['cart']);
            $success = "Order placed successfully! Your Order ID is #$order_id.";
        } catch(Exception $e) {
            $conn->rollback();
            $error = "Failed to place order. " . $e->getMessage();
        }
    }
}
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section>
  <div class="form-container glass" style="max-width: 600px; margin: 2rem auto; padding: 2rem;">
    <h2 class="section-title">Checkout</h2>
    
    <?php if($error): ?>
      <p style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if($success): ?>
      <div style="background: #D1FAE5; color: #059669; padding: 2rem; border-radius: 8px; text-align: center;">
        <h3><?php echo $success; ?></h3>
        <p style="margin-top: 1rem;">We will review your prescription and process your order shortly.</p>
        <a href="customer/dashboard.php" class="btn" style="margin-top: 1.5rem;">Go to Dashboard</a>
      </div>
    <?php else: ?>
      <form method="POST" enctype="multipart/form-data">
        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
          <h3 style="margin-bottom: 1rem;">Order Summary</h3>
          <p style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem;">
            <span>Total Amount:</span>
            <?php
            $total = 0;
            $ids = implode(',', array_keys($_SESSION['cart']));
            $result = $conn->query("SELECT * FROM medicines WHERE id IN ($ids)");
            while($row = $result->fetch_assoc()){
                $total += $_SESSION['cart'][$row['id']] * $row['price'];
            }
            ?>
            <span style="color: var(--primary-color);">৳<?php echo number_format($total, 2); ?></span>
          </p>
        </div>
        
        <div class="form-group">
          <label>Upload Prescription <span style="color: red;">*</span></label>
          <div class="file-upload-wrapper">
            <input type="file" name="prescription" id="prescription" accept=".jpg,.jpeg,.png,.pdf" required style="width: 100%;">
            <p style="margin-top: 0.5rem; color: var(--text-light); font-size: 0.9rem;">Accepted formats: JPG, PNG, PDF</p>
          </div>
        </div>
        
        <button type="submit" name="place_order" class="btn" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Confirm & Place Order</button>
      </form>
    <?php endif; ?>
  </div>
</section>

<?php include('includes/footer.php'); ?>
