<footer>
  <p>Manager: Rizvi Ahmmed | Email: ahmmed.rizvi30@gmail.com | Phone: +8801987654321</p>
  <p>Developed by RedDot</p>
  <div class="social">
    <a href="https://www.facebook.com">Facebook</a> | <a href="https://x.com/">Twitter</a> | <a href="https://www.instagram.com/">Instagram</a>
  </div>
</footer>

<?php
$toasts = [];

// Session toasts
if(isset($_SESSION['toast'])){
    $toasts[] = $_SESSION['toast'];
    unset($_SESSION['toast']);
}

// GET param toasts
if(isset($_GET['success'])) $toasts[] = ['type' => 'success', 'message' => $_GET['success']];
if(isset($_GET['error'])) $toasts[] = ['type' => 'error', 'message' => $_GET['error']];
if(isset($_GET['added'])) $toasts[] = ['type' => 'success', 'message' => 'Medicine added to cart successfully!'];
if(isset($_GET['requested'])) $toasts[] = ['type' => 'info', 'message' => 'Restock request submitted successfully!'];

if(!empty($toasts)): ?>
<div id="toast-container">
    <?php foreach($toasts as $idx => $t): ?>
    <div class="toast <?php echo htmlspecialchars($t['type']); ?>" id="toast-<?php echo $idx; ?>">
        <span><?php echo htmlspecialchars($t['message']); ?></span>
        <span style="cursor:pointer; margin-left:15px; color:#999; font-size:1.2rem; font-weight:bold;" onclick="this.parentElement.style.display='none'">&times;</span>
    </div>
    <?php endforeach; ?>
</div>
<script>
    setTimeout(() => {
        document.querySelectorAll('.toast').forEach(t => {
            t.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => t.remove(), 300);
        });
    }, 5000);
</script>
<?php endif; ?>

</body>
</html>
