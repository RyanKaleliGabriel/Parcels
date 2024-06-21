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

$sql = "SELECT * FROM `drivers` WHERE `status`='active'";
$result = $conn->query(($sql));



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_driver'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $image_url = "default.png";
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    $default_status = "active";

    $errors = array();
    if ($password !== $passwordConfirm) {
      $errors[] = "Passwords do not match:Please try again";
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if (empty($errors)) {
      $stmt = $conn->prepare("INSERT INTO `drivers` (`username`, `email`, `password`, `image_url`, `status`) VALUES (?,?,?,?,?)");
      if (!$stmt) {
        die("Prepare failed: " . $conn->error);
      }
      $stmt->bind_param("sssss", $name, $email, $hashed_password, $image_url, $default_status);
      if ($stmt->execute()) {
        $_SESSION['successfully_added'] = true;
        header("refresh:2");
      } else {
        die("Execute field: " . $stmt->error);
      }
    }
  }

  if (isset($_POST['delete_driver'])) {
    $driver_id = $_POST['driver_id'];
    $status = "inactive";
    $stmt = $conn->prepare("UPDATE `drivers` SET `status`=? WHERE `id`=?");
    if (!$stmt) {
      die("Prepare failed:" . $conn->error);
    }
    $stmt->bind_param("si", $status, $driver_id);
    if ($stmt->execute()) {
      $_SESSION['successfully_deleted'] = true;
      header("refresh:2");
    } else {
      die("Execute failed: " . $stmt->error);
    }
  }
}
include('../includes/Header.php');
?>
<main class="dash-main">
  <div class="upper-table">
    <p>Driver Operations</p>
    <div><button class="newparc" id="newDriver">Add New +</button></div>
  </div>
  <table class="main-operations">
    <thead>
      <tr class="table-heading">
        <th></th>
        <th>UiD</th>
        <th class="table-heading">Name</th>
        <th>Email</th>
        <th>Trips</th>
        <th>Action</th>
      </tr>
      <hr />
    </thead>
    <tbody>

      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td><img class='table-img' src=\"../assets/images/{$row['image_url']}\" /></td>";
          echo "<td>" . $row["id"] . "</td>";
          echo "<td>" . $row["username"] . "</td>";
          echo "<td>" . $row["email"] . "</td>";
          echo "<td>" . '3' . "</td>";
          echo "<td>";
          echo "<div>";
          echo "<button class='delete' onclick='showDeleteForm(" . $row["id"] . ")'>";
          echo "<i class='fa-solid fa-trash'></i>";
          echo "</button>";
          echo "</div>";
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='8'>No Drivers found</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <div class="paginate">
    <div>
      <i class="fa-solid fa-chevron-left"></i>
      <p>Previous</p>
    </div>
    <div>
      <p>Next</p>
      <i class="fa-solid fa-chevron-right"></i>
    </div>
  </div>
</main>

<div class="popup-form">
  <div class="popup-content">
    <div class="auth-form animate__animated animate__zoomIn">
      <div class="custom-logo">
        <p>Add New Driver</p>
      </div>
      <form method="post">
        <input type="hidden" name="add_driver" />
        <label for="fname">Full Name</label><br />
        <input type="text" name="name" placeholder="John Doe" required /><br />
        <label for="email">Email</label><br />
        <input type="email" name="email" autocomplete="off" placeholder="example@email.com" required /><br />
        <label for="password">Password</label><br />
        <input type="password" id="password" name="password" placeholder="********" min="8" autocomplete="off" required /><br />
        <label for="passwordConfirm">Password Confirm</label><br />
        <input type="password" id="passwordConfirm" placeholder="********" name="passwordConfirm" required /><br />
        <button>Add new driver</button>
      </form>
    </div>
  </div>
</div>

<div class="delete-popup-form">
  <div class="popup-content">
    <div class="delete-modal animate__animated animate__zoomIn">
      <i class="modal-icon fa-solid fa-triangle-exclamation"></i>
      <div>
        <h3>Delete Driver?</h3>
        <p>Proceed to delete this record. This action cannot be undone.</p>
      </div>
      <div class="forms-modal delete-section-button">
        <form method='post'>
          <input type="hidden" name="delete_driver" value="1" />
          <input type="hidden" name="driver_id" id="delete-id" />
          <button class="end-trip-btn">Delete</button>
        </form>
        <form><button class="cancel">Cancel</button></form>
      </div>
    </div>
  </div>
</div>


<?php if (!empty($errors)) : ?>
  <?php foreach ($errors as $error) : ?>
    <div class="toast-validate"><?php echo $error; ?></div>
  <?php endforeach; ?>
<?php endif; ?>



<?php include('../includes/Footer.php'); ?>