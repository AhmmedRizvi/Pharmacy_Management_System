<?php
$conn = new mysqli("localhost", "root", "", "shebaclinic");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("ALTER TABLE prescriptions ADD COLUMN order_id INT AFTER user_id");
$conn->query("ALTER TABLE prescriptions ADD FOREIGN KEY (order_id) REFERENCES orders(id)");
echo "Database patched successfully";
?>
