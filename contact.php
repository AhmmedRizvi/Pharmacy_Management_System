<?php
session_start();
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section>
  <div class="form-container glass" style="max-width: 600px; margin: 2rem auto; padding: 2rem; text-align: center;">
    <h2 class="section-title">Contact Us</h2>
    
    <div style="margin-top: 2rem; text-align: left;">
      <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Sheba Clinic Pharmacy</h3>
      <p style="margin-bottom: 1rem;"><strong>Address:</strong> Namapara,Khilkhet( Near by lake City),Dhaka-1229</p>
      <p style="margin-bottom: 1rem;"><strong>Phone:</strong> 01822220269</p>
      <p style="margin-bottom: 1rem;"><strong>Email:</strong> ahmmed.rizvi30@gmail.com</p>
      <p style="margin-bottom: 1rem;"><strong>Working Hours:</strong> 8:00 AM - 10:00 PM</p>
    </div>
    
    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
      <h3>Send us a message</h3>
      <form method="POST" style="margin-top: 1rem; text-align: left;">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="message" rows="4" required></textarea>
        </div>
        <button type="button" class="btn" onclick="alert('Message sent successfully!');" style="width: 100%;">Send Message</button>
      </form>
    </div>
  </div>
</section>

<?php include('includes/footer.php'); ?>
