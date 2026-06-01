<?php
$conn = new mysqli("localhost", "root", "", "shebaclinic");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
// Pre-populate with some defaults if empty
$check = $conn->query("SELECT COUNT(*) as c FROM categories");
if($check->fetch_assoc()['c'] == 0) {
    $conn->query("INSERT INTO categories (name) VALUES ('Antibiotics'), ('Painkillers'), ('Vitamins'), ('Syrups')");
}
echo "Database patched with categories table.";
?>
