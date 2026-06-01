<?php
include('../config.php');
include('../includes/auth.php');
redirectIfNotLoggedIn();
if(isAdmin()){
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = '';

// Profile Update
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "UPDATE users SET name='$name', email='$email' WHERE id='$user_id'";
    if($conn->query($sql)){
        $_SESSION['name'] = $name;
        $msg = "<p style='color: green;'>Profile updated successfully!</p>";
    } else {
        $msg = "<p style='color: red;'>Error updating profile.</p>";
    }
}

// Password Update
if(isset($_POST['change_password'])){
    $old = $_POST['old_password'];
    $new = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $user_check = $conn->query("SELECT password FROM users WHERE id='$user_id'")->fetch_assoc();
    if(password_verify($old, $user_check['password'])){
        if($conn->query("UPDATE users SET password='$new' WHERE id='$user_id'")){
            $msg = "<p style='color: green;'>Password changed successfully!</p>";
        }
    } else {
        $msg = "<p style='color: red;'>Incorrect old password.</p>";
    }
}
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="dashboard-grid" style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
  <div class="sidebar glass">
    <h3>Customer Menu</h3>
    <ul style="margin-top: 1rem;">
      <li><a href="dashboard.php" class="active">My Dashboard</a></li>
      <li><a href="../index.php">Shop Medicines</a></li>
      <li><a href="../cart.php">My Cart</a></li>
    </ul>
  </div>
  
  <div class="dashboard-content glass">
    <h2 style="margin-bottom: 2rem;">Welcome, <?php echo $_SESSION['name']; ?>!</h2>
    
    <?php
    $notifications = $conn->query("SELECT r.*, m.name as medicine_name 
                                   FROM stock_requests r 
                                   JOIN medicines m ON r.medicine_id = m.id 
                                   WHERE r.user_id='$user_id' AND r.status='resolved' 
                                   ORDER BY r.created_at DESC LIMIT 3");
    if($notifications->num_rows > 0){
        echo "<div style='margin-bottom: 2rem;'>";
        while($notif = $notifications->fetch_assoc()){
            echo "<div style='background: #D1FAE5; border-left: 4px solid #10B981; color: #065F46; padding: 1rem; margin-bottom: 0.5rem; border-radius: 4px;'>
                    <strong>Good news!</strong> The medicine <strong>{$notif['medicine_name']}</strong> you requested is now back in stock! 
                    <a href='../index.php' style='color: #047857; text-decoration: underline; font-weight: bold;'>Shop Now</a>
                  </div>";
        }
        echo "</div>";
    }
    ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="form-container" style="margin: 0; box-shadow: none; border: 1px solid #eee;">
            <h3>Update Profile</h3>
            <?php echo $msg; ?>
            <form method="POST" style="margin-top: 1rem;">
                <?php
                $user = $conn->query("SELECT * FROM users WHERE id='$user_id'")->fetch_assoc();
                ?>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <button type="submit" name="update" class="btn">Update Profile</button>
            </form>
        </div>
        
        <div class="form-container" style="margin: 0; box-shadow: none; border: 1px solid #eee;">
            <h3>Change Password</h3>
            <form method="POST" style="margin-top: 1rem;">
                <div class="form-group">
                    <label>Old Password</label>
                    <input type="password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-secondary">Change Password</button>
            </form>
        </div>
    </div>

    <h3>My Orders & Prescriptions</h3>
    <div class="table-container" style="margin-top: 1rem;">
      <?php
      $orders = $conn->query("SELECT o.*, p.file_path 
                              FROM orders o 
                              LEFT JOIN prescriptions p ON p.order_id = o.id
                              WHERE o.user_id='$user_id' ORDER BY o.created_at DESC");
      
      if($orders->num_rows > 0){
          echo "<table>
          <thead>
          <tr><th>Order ID</th><th>Items</th><th>Date</th><th>Total</th><th>Status</th><th>Prescription</th></tr>
          </thead>
          <tbody>";
          
          while($order = $orders->fetch_assoc()){
              $status_class = strtolower($order['status']);
              if($order['file_path']){
                  $parts = explode('/', $order['file_path']);
                  $encoded_path = $parts[0] . '/' . rawurlencode($parts[1] ?? '');
                  $prescription = "<a href='../{$encoded_path}' target='_blank' style='color: var(--primary-color); font-weight: bold;'>View File</a>";
              } else {
                  $prescription = "No file";
              }
              
              $items_query = $conn->query("SELECT m.name, oi.quantity FROM order_items oi JOIN medicines m ON oi.medicine_id = m.id WHERE oi.order_id = '{$order['id']}'");
              $item_names = [];
              while($item = $items_query->fetch_assoc()){
                  $item_names[] = "{$item['name']} (x{$item['quantity']})";
              }
              $items_str = !empty($item_names) ? implode("<br>", $item_names) : "<em>Unknown</em>";
              
              echo "<tr>
              <td><strong>#{$order['id']}</strong></td>
              <td><span style='font-size: 0.9rem;'>{$items_str}</span></td>
              <td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>
              <td>৳{$order['total']}</td>
              <td><span class='badge {$status_class}'>{$order['status']}</span></td>
              <td>{$prescription}</td>
              </tr>";
          }
          echo "</tbody></table>";
      } else {
          echo "<p style='padding: 1rem; text-align: center;'>You have no orders yet. <a href='../index.php'>Start shopping!</a></p>";
      }
      ?>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
