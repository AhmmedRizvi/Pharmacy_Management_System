<?php
$conn = new mysqli("localhost", "root", "", "shebaclinic");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("CREATE TABLE IF NOT EXISTS stock_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  medicine_id INT,
  status ENUM('pending', 'resolved') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (medicine_id) REFERENCES medicines(id)
)");
echo "Database patched with stock_requests table.";
?>
