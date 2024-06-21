<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_client'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $image_url = "default.png";
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    $default_status = "active";

    $errors = array();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    if ($password !== $passwordConfirm) {
      $errors[] = "Passwords do not match:Please try again";
    }

    if (empty($errors)) {
      $stmt = $conn->prepare("INSERT INTO `senders` (`username`, `email`, `password`, `image_url`, `status`) VALUES (?,?,?,?,?)");
      if (!$stmt) {
        die("Prepare failed: " . $conn->error);
      }
      $stmt->bind_param("sssss", $name, $email, $hashed_password, $image_url, $default_status);
      if ($stmt->execute()) {
        session_start();
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $user['username'];
        header("Location: ../senders/sender.php");
      } else {
        die("Execute field: " . $stmt->error);
      }
    }
  }
}
?>
<div class="auth-page">
  <header>Quick Send . Secure Parcel Delivery</header>

  <div class="auth-section">
    <div class="auth-form">
      <a href="../public/index.php" class="anchor-logo">
        <div class="custom-logo">
          <h5 class="logo-p1">quickSend</h5>
          <p>Secure Parcel Delivery System</p>
        </div>
      </a>
      <form method="post">
        <input type="hidden" name="add_client" />
        <label for="name">Full Name</label><br />
        <input type="text" name="name" placeholder="John Doe" required /><br />
        <label for="email">Email</label><br />
        <input type="email" name="email" autocomplete="off" placeholder="example@email.com" required /><br />
        <label for="password">Password</label><br />
        <input type="password" id="password" name="password" placeholder="********" min="8" autocomplete="off" required /><br />
        <label for="passwordConfirm">Password Confirm</label><br />
        <input type="password" id="passwordConfirm" placeholder="********" name="passwordConfirm" required /><br />
        <button>Sign Up</button>
      </form>
      <span>Already have an Account ?
        <a href="../pages/login.php" class="auth-link">Login</a></span>
    </div>
  </div>
</div>

<?php if (!empty($errors)) : ?>
  <?php foreach ($errors as $error) : ?>
    <div class="toast-validate"><?php echo $error; ?></div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include('../includes/pageFooter.php'); ?>