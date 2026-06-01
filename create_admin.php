<?php
$conn = new mysqli('localhost', 'root', '', 'shebaclinic');
$check = $conn->query("SELECT * FROM users WHERE role='admin'");
if($check->num_rows == 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Admin User', 'admin@sheba.com', '$hash', 'admin')");
    echo "Admin created. Email: admin@sheba.com, Password: admin123";
} else {
    echo "Admin already exists.";
}
?>
