<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];


  $check_email_query = "SELECT * FROM senders WHERE email='$email' AND status='active' ";
  $result = $conn->query($check_email_query);

  $check_email_driver = "SELECT * FROM drivers WHERE email='$email' AND status='active'";
  $result_driver = $conn->query($check_email_driver);

  $check_email_admin = "SELECT * FROM admins WHERE email='$email' AND status='active'";
  $result_admin = $conn->query($check_email_admin);

  $check_email_clerk = "SELECT * FROM clerks WHERE email='$email' AND status='active'";
  $result_clerk = $conn->query($check_email_clerk);

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      session_start();
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['username'];
      header("Location: ../senders/sender.php");
    } else {
      $errors = array();
      $errors[] = "Incorrect Email or Password";
    }
  } else if ($result_admin->num_rows > 0) {
    $admin = $result_admin->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      session_start();
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_name'] = $admin['username'];
      header("Location: ../public/dashboard.php");
    } else {
      $errors = array();
      $errors[] = "Incorrect Email or Password";
    }
  } else if ($result_driver->num_rows > 0) {
    $driver = $result_driver->fetch_assoc();
    if (password_verify($password, $driver['password'])) {
      session_start();
      $_SESSION['driver_id'] = $driver['id'];
      $_SESSION['driver_name'] = $driver['username'];
      header("Location: ../drivers/driverpanel.php");
    } else {
      $errors = array();
      $errors[] = "Incorrect Email or Password";
    }
  } else if ($result_clerk->num_rows > 0) {
    $clerk = $result_clerk->fetch_assoc();
    if (password_verify($password, $clerk['password'])) {
      session_start();
      $_SESSION['clerk_id'] = $clerk['id'];
      $_SESSION['clerk_name'] = $clerk['username'];
      header("Location: ../clerks/clerkspanel.php");
    } else {
      $errors = array();
      $errors[] = "Incorrect Email or Password";
    }
  }
}

?>
<div class="auth-page">
  <header>Quick Send . Secure Parcel Delivery</header>

  <div class="auth-section">
    <div class="auth-form">
      <div class="custom-logo">
        <h5 class="logo-p1">quickSend</h5>
        <p>Secure Parcel Delivery System</p>
      </div>
      <form method="post">
        <label for="email">Email</label><br />
        <input type="email" name="email" autocomplete="off" placeholder="example@email.com" required /><br />
        <label for="password">Password</label><br />
        <input type="password" id="password" name="password" placeholder="********" min="8" autocomplete="off" required /><br />
        <button>Login</button>
      </form>
      <span>Don't have an Account ?
        <a href="../pages/register.php" class="auth-link">Create an account</a></span>
    </div>
  </div>
</div>
<?php include('../includes/pageFooter.php'); ?>