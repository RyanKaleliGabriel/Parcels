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



$sql = "SELECT * FROM trips WHERE `status`='active'";
$result = $conn->query(($sql));

$sql_driver = "SELECT * FROM drivers WHERE `status`='active'";
$result_drivers = $conn->query(($sql_driver));
$result_drivers_edit = $conn->query(($sql_driver));

$sql_clerk = "SELECT * FROM clerks WHERE `status`='active'";
$result_clerks = $conn->query(($sql_clerk));
$result_clerks_edit = $conn->query(($sql_clerk));

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['add_trip'])) {
    $departurep = $_POST['departurep'];
    $pickp = $_POST['pickp'];
    $departured = $_POST['departured'];
    $arrivald = $_POST['arrivald'];
    $default_status = "active";
    $default_progress = "Not Started";
    $driver = $_POST['driver'];
    $clerk = $_POST['clerk'];
    $stmt = $conn->prepare("INSERT INTO `trips` (`departure_date`, `arrival_date`, `pickup_point`, `departure_point`, `status`, `progress`, `driver_id`, `clerk_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssss", $departured, $arrivald, $pickp, $departurep, $default_status, $default_progress, $driver, $clerk);
    if ($stmt->execute()) {
      $_SESSION['successfully_added'] = true;
      header("refresh:2");
    } else {
      die("Execute failed: " . $stmt->error);
    }
  }

  if (isset($_POST['update_trip'])) {
    $default_progress = $_POST['default_progress'];
    $driver = $_POST['driver'];
    $trip_id = $_POST['trip_id'];
    $departurep = $_POST['departurep'];
    $pickp = $_POST['pickp'];
    $departured = $_POST['departured'];
    $arrivald = $_POST['arrivald'];
    $clerk = $_POST['clerk'];
    $stmt = $conn->prepare("UPDATE `trips` SET `departure_date`=?, `arrival_date`=?, `pickup_point`=?, `departure_point`=?, `driver_id`=?, `progress`=?, `clerk_id`=? WHERE `id`=?");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssssii", $departured, $arrivald, $pickp, $departurep, $driver, $default_progress, $clerk, $trip_id);
    if ($stmt->execute()) {
      $_SESSION['successfully_updated'] = true;
      header("refresh:2");
    } else {
      die("Execute failed: " . $stmt->error);
    }
  }

  if (isset($_POST['delete_trip'])) {
    $trip_id = $_POST['trip_id'];
    $status = "inactive";
    $stmt = $conn->prepare("UPDATE `trips` SET `status`=? WHERE `id`=?");
    if (!$stmt) {
      die("Prepare failed:" . $conn->error);
    }
    $stmt->bind_param("si", $status, $trip_id);
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
    <p>Trip Operations</p>
    <div><button class="newparc">Add New +</button></div>
  </div>
  <table class="main-operations">
    <thead>
      <tr>
        <th>Trip Number</th>
        <th>Departure Date</th>
        <th>Arrival Date</th>
        <th>Departure Point</th>
        <th>Pick up Point</th>
        <th>Driver</th>
        <th>Clerk</th>
        <th>Progress</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["id"] . "</td>";
          echo "<td>" . $row["departure_date"] . "</td>";
          echo "<td>" . $row["arrival_date"] . "</td>";
          echo "<td>" . $row["departure_point"] . "</td>";
          echo "<td>" . $row["pickup_point"] . "</td>";
          echo "<td>";
          $driver_id = $row["driver_id"];
          $sql = "SELECT username FROM drivers WHERE id=$driver_id";
          $result_driver = $conn->query($sql);
          if ($result_driver->num_rows > 0) {
            while ($user_row = $result_driver->fetch_assoc()) {
              echo $user_row["username"];
            }
          } else {
            echo "Not found";
          }
          "</td>";
          echo "<td>";
          $clerk_id = $row["clerk_id"];
          $sql = "SELECT username FROM clerks WHERE id=$clerk_id";
          $result_clerk_read = $conn->query($sql);
          if ($result_clerk_read->num_rows > 0) {
            while ($user_row = $result_clerk_read->fetch_assoc()) {
              echo $user_row["username"];
            }
          } else {
            echo "Not found";
          }
          "</td>";
          echo "<td>";

          switch ($row["progress"]) {
            case "Not Started":
              echo "<button  class='status-inactive' disabled>" . $row["progress"] . "</button>";
              break;
            case "Completed":
              echo "<button  class='status-active' disabled>" . $row["progress"] . "</button>";
              break;
            case "In Progress":
              echo "<button  class='status-inactive' disabled>" . $row["progress"] . "</button>";
              break;
          }
          echo "</td>";
          echo "<td>";
          echo "<div>";
          echo "<button class='edit' onclick='showEditForm(" . $row["id"] . ", \"" . $row["departure_date"] . "\", \"" . $row["arrival_date"] . "\", \"" . $row["departure_point"] . "\", \"" . $row["pickup_point"] . "\", \"" . $row["driver_id"] . "\", \"" . $row["clerk_id"] . "\", \"" . $row["progress"] . "\")'>";
          echo "<i class='fa-solid fa-pen'></i>";
          echo "</button>";
          echo "<button class='delete' onclick='showDeleteForm(" . $row["id"] . ")'>";
          echo "<i class='fa-solid fa-trash'></i>";
          echo "</button>";
          echo "</div>";
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='8'>No trips found</td></tr>";
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

<div class="popup-form ">
  <div class="popup-content ">
    <div class="auth-form animate__animated animate__zoomIn">
      <div class="custom-logo">
        <p>Add New Trip</p>
      </div>
      <form method="post">
        <input type="hidden" name="add_trip" />
        <label for="departurep">Departure point</label><br />
        <input type="text" name="departurep" required /><br />
        <label for="pickp">Pick Up Point</label><br />
        <input type="text" name="pickp" required /><br />
        <label for="trip">Select Driver</label><br />
        <select name="driver">
          <?php
          if ($result_drivers->num_rows > 0) {
            while ($row = $result_drivers->fetch_assoc()) {
              echo "<option value= '" . $row["id"] . "' >" . $row["username"] . "</option>";
            }
          } else {
            echo "<option disabled>Drivers not available</option>";
          }
          ?>
        </select><br />
        <label for="trip">Select Clearance Clerk</label><br />
        <select name="clerk">
          <?php
          if ($result_clerks->num_rows > 0) {
            while ($row = $result_clerks->fetch_assoc()) {
              echo "<option value= '" . $row["id"] . "' >" . $row["username"] . "</option>";
            }
          } else {
            echo "<option disabled>Clerks not available</option>";
          }
          ?>
        </select><br />
        <label for="departured">Departure Date</label><br />
        <input type="date" name="departured" required /><br />
        <label for="arrivald">Arrival Date</label><br />
        <input type="date" name="arrivald" required /><br />
        <button type="submit">Add Trip</button>
      </form>
    </div>
  </div>
</div>

<div class="edit-popup-form">
  <div class="popup-content ">
    <div class="auth-form animate__animated animate__zoomIn">
      <div class="custom-logo">
        <p>Update Trip</p>
      </div>
      <form method="post">
        <input type="hidden" name="update_trip" value="1">
        <input type="hidden" name="trip_id" id="edit-trip-id" />
        <input type="hidden" name="default_progress" id="edit-progress-id" />
        <input type="hidden" name="default_status" id="edit-status-id" />
        <label for="departurep">Departure point</label><br />
        <input type="text" name="departurep" required id="edit-departurep" /><br />
        <label for="pickp">Pick Up Point</label><br />
        <input type="text" id="edit-pickp" name="pickp" required /><br />
        <label for="trip">Select Driver</label><br />
        <select name="driver" id="edit-driver">
          <?php
          if ($result_drivers->num_rows > 0) {
            while ($row = $result_drivers_edit->fetch_assoc()) {
              echo "<option value= '" . $row["id"] . "' >" . $row["username"] . "</option>";
            }
          } else {
            echo "<option disabled>Drivers not available</option>";
          }
          ?>
        </select><br />
        <label for="trip">Select Clearance Clerk</label><br />
        <select name="clerk" id="edit-clerk">
          <?php
          if ($result_clerks_edit->num_rows > 0) {
            while ($row = $result_clerks_edit->fetch_assoc()) {
              echo "<option value= '" . $row["id"] . "' >" . $row["username"] . "</option>";
            }
          } else {
            echo "<option disabled>Clerks not available</option>";
          }
          ?>
        </select><br />
        <label for="departured">Departure Date</label><br />
        <input type="date" name="departured" id="edit-departured" required /><br />
        <label for="arrivald">Arrival Date</label><br />
        <input type="date" name="arrivald" id="edit-arrivald" required /><br />
        <button type="submit">Update</button>
      </form>
    </div>
  </div>
</div>

<div class="delete-popup-form">
  <div class="popup-content">
    <div class="delete-modal animate__animated animate__zoomIn">
      <i class="modal-icon fa-solid fa-triangle-exclamation"></i>
      <div>
        <h3>Delete Trip?</h3>
        <p>Proceed to delete this record. This action cannot be undone.</p>
      </div>
      <div class="forms-modal delete-section-button">
        <form method='post'>
          <input type="hidden" name="delete_trip" value="1" />
          <input type="hidden" name="trip_id" id="delete-id" />
          <button class="end-trip-btn">Delete</button>
        </form>
        <form><button class="cancel">Cancel</button></form>
      </div>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>




<?php include('../includes/Footer.php'); ?>