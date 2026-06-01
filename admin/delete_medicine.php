<?php
include('../config.php');
include('../includes/auth.php'); 
redirectIfNotAdmin();

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $conn->query("DELETE FROM medicines WHERE id='$id'");
}
header("Location: inventory.php");
exit();
?>
