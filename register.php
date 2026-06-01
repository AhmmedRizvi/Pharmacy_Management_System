<?php
include('config.php');
include('includes/auth.php');

if(isLoggedIn()){
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        $error = "Email already exists.";
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'customer')";
        if($conn->query($sql)){
            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section>
  <div class="form-container glass">
    <h2 class="section-title">Create an Account</h2>
    <?php if($error): ?>
      <p style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if($success): ?>
      <p style="color: green; text-align: center; margin-bottom: 1rem;"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" required>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" name="register" class="btn" style="width: 100%;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="login.php">Login here</a></p>
  </div>
</section>

<?php include('includes/footer.php'); ?>
