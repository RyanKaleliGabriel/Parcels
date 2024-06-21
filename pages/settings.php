<?php
include('../config/config.php');


session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: ../pages/login.php");
  exit();
}

$user_id = $_SESSION['admin_id'];
$user_query = "SELECT * FROM admins WHERE id='$user_id'";
$user_result = $conn->query($user_query);


if ($user_result->num_rows > 0) {
  $user = $user_result->fetch_assoc();
  $user_name = $user['username'];
  $user_image = $user['image_url'];
} else {
  header("Location: ../pages/login.php");
  // $user_name = "Unknown";
}


include('../includes/Header.php'); ?>
<main class="dash-main">
  <div>
    <p>Account Details</p>
  </div>
  <section id="profile">
    <div class="profile-settings">
      <?php echo "<img class='table-img' src=\"../assets/images/{$user_image}\" />";
      ?>
      <form>
        <input type="file" />
      </form>
    </div>

    <div class="username-settings">
      <form>
        <h5>Update Account Details</h5>
        <label for="username">Username</label><br />
        <input type='text' name='username' value='<?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>' /><br />
        <button class="settings-button">Update</button>
      </form>
    </div>

    <div class="password-settings">
      <form>
        <h5>Change Password</h5>
        <label for="currpassword">Current Password</label><br />
        <input type="text" name="currpassword" /><br />
        <label for="newpassword">New Password</label><br />
        <input type="text" name="newpassword" /><br />
        <button class="settings-button">Update</button>
      </form>
    </div>

    <div class="delete-acc">
      <form>
        <button>Delete Account</button>
      </form>
    </div>
  </section>
</main>
<?php include('../includes/Footer.php'); ?>