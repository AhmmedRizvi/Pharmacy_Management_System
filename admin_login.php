<?php
include('config.php');
include('includes/auth.php');

if(isLoggedIn() && isAdmin()){
    header("Location: admin/dashboard.php");
    exit();
}

$error = '';
if(isset($_POST['admin_login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='admin'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No admin found with this email.";
    }
}
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section>
  <div class="form-container glass" style="border-top: 4px solid var(--primary-color);">
    <h2 class="section-title">Admin Portal Login</h2>
    <?php if($error): ?>
      <p style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Admin Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" name="admin_login" class="btn" style="width: 100%;">Login as Admin</button>
    </form>
    
    <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
      <span style="color: var(--text-light); margin-right: 1rem;">Login as:</span>
      <a href="admin_login.php" style="padding: 0.4rem 1rem; background: var(--primary-color); border: 2px solid var(--primary-color); border-radius: 20px; color: white; font-weight: bold; text-decoration: none; margin-right: 0.5rem;">Admin</a>
      <a href="login.php" style="padding: 0.4rem 1rem; border: 2px solid var(--primary-color); border-radius: 20px; color: var(--primary-color); font-weight: bold; text-decoration: none;">Customer</a>
    </div>
  </div>
</section>

<?php include('includes/footer.php'); ?>
