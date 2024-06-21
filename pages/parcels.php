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

$sql_parcels = "SELECT * FROM parcels WHERE `status`='active' ";
$result_parcels = $conn->query(($sql_parcels));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['delete_parcel'])) {
    $parcel_id = $_POST['parcel_id'];
    $status = "inactive";
    $stmt = $conn->prepare("UPDATE `parcels` SET `status`=? WHERE `id`=?");
    if (!$stmt) {
      die("Prepare failed:" . $conn->error);
    }
    $stmt->bind_param("si", $status, $parcel_id);
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
    <p>Parcel Operations</p>
  </div>
  <table class="main-operations">
    <thead>
      <tr>
        <th>Parcel Number</th>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Destination Point</th>
        <th>Pick Up Point</th>
        <th>Track Progress</th>
        <th>Action</th>
      </tr>
    </thead>
    <hr />
    <tbody>
      <?php
      if ($result_parcels->num_rows > 0) {
        while ($row = $result_parcels->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["id"] . "</td>";
          $sender_id = $row["sender_id"];
          $sql = "SELECT * FROM senders WHERE `id`=$sender_id AND `status`='active' ";

          $result_sender = $conn->query($sql);
          if ($result_sender->num_rows > 0) {
            while ($user_row = $result_sender->fetch_assoc()) {
              echo "<td>" . $user_row["username"] . "</td>";
            }
          } else {
            echo "<td>Not found</td>";
          }
          echo "<td>" . $row["recipient_name"] . "</td>";
          $trip_id = $row["trip_id"];
          $sql = "SELECT * FROM trips WHERE `id`=$trip_id AND `status`='active' ";
          $result_trips = $conn->query($sql);
          if ($result_trips->num_rows > 0) {
            while ($trip_row = $result_trips->fetch_assoc()) {
              echo "<td>" . $trip_row["departure_point"] . "</td>";
            }
          } else {
            echo "<td>Not found</td>";
          }
          $sql = "SELECT * FROM trips WHERE id = $trip_id ";
          $result_trip = $conn->query($sql);
          if ($result_trip->num_rows > 0) {
            while ($trip_row = $result_trip->fetch_assoc()) {
              echo "<td>" . $trip_row["pickup_point"] . "</td>";
            }
          } else {
            echo "<td>Not found</td>";
          }
          echo "<td>";
          switch ($row["track_progress"]) {
            case "Not Cleared":
              echo "<button  class='status-inactive' disabled>" . $row["track_progress"] . "</button>";
              break;
            case "Cleared":
              echo "<button  class='status-active' disabled>" . $row["track_progress"] . "</button>";
              break;
          }
          echo "</td>";
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
        echo "<tr><td colspan='8'>No Parcels found</td></tr>";
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
        <input type="hidden" name="delete_parcel" value="1" />
          <input type="hidden" name="parcel_id" id="delete-id" />
          <button class="end-trip-btn">Delete</button>
        </form>
        <form><button class="cancel">Cancel</button></form>
      </div>
    </div>
  </div>
</div>
<?php include('../includes/Footer.php'); ?>