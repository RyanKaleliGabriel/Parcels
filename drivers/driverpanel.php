<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if (!isset($_SESSION['driver_id'])) {
  header("Location: ../pages/login.php");
  exit();
}

$user_id = $_SESSION['driver_id'];

$check_email_driver = "SELECT * FROM drivers WHERE id='$user_id' AND status='active'";
$result_driver = $conn->query($check_email_driver);

if ($result_driver->num_rows > 0) {
  $user = $result_driver->fetch_assoc();
  $user_name = $user['username'];
  $user_image = $user['image_url'];
} else {
  header("Location: ../pages/login.php");
  exit();
}

$sql = "SELECT * FROM trips WHERE `driver_id` = $user_id AND `status` = 'active' ";
$result = $conn->query(($sql));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['start_trip'])) {
    $trip_id = $_POST['trip_id'];
    $progress = 'In Progress';

    $stmt = $conn->prepare("UPDATE `trips` SET `progress`=? WHERE `id`=?");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $progress, $trip_id);
    if ($stmt->execute()) {
      header("refresh:1");
    } else {
      die("Execute failed: " . $stmt->error);
    }
  }

  if (isset($_POST['end_trip'])) {
    $trip_id = $_POST['trip_id'];
    $progress = 'Completed';

    $stmt = $conn->prepare("UPDATE `trips` SET `progress`=? WHERE `id`=?");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $progress, $trip_id);
    if ($stmt->execute()) {
      header("refresh:1");
    } else {
      die("Execute failed: " . $stmt->error);
    }
  }
}
?>

<body>
  <div class="parcel-page">
    <header class="parcel-header">
      <div class="logo">
        <p class="logo-p1">quickSend</p>
        <p class="logo-p2">Secure Parcel Delivery System</p>
      </div>


      <div class="header-profile">
        <?php echo "<td><img class='table-img' src=\"../assets/images/{$user_image}\" /></td>";
        ?>
        <a href="./profile.php" class="anchor-logo">
          <?php echo "<p>" . $user_name . "</p>"; ?> </a>

      </div>
    </header>
    <main>
      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

          echo "<div class='trip-container'>";
          echo "<div class='trip-child'>";
          echo "<div>";
          echo "<div class='trip-intro'>";
          echo "<p>Trip No. " . $row['id'] . "</p>"; // Use the trip ID from the database
          switch ($row["progress"]) {
            case "Not Started":
              echo "<span class='not-started'>" . $row["progress"] . "</span>";
              break;
            case "Completed":
              echo "<span class='completed'>"  . $row["progress"] . "</span>";
              break;
            case "In Progress":
              echo "<span class='progress'>"  . $row["progress"] . "</span>";
              break;
          }
          echo "</div>";
          echo "<div class='trip-details'>";
          echo "<ul>";
          echo "<li><span class='brown'>Departure Location: </span>" . $row['departure_point'] . "</li>";
          echo "<li><span class='brown'>Destination: </span>" . $row['pickup_point'] . "</li>";
          echo "<li><span class='brown'>Driver Name:</span> " . $row['driver_id'] . "</li>"; // Assuming driver_name is the column name
          echo "</ul>";
          echo "<ul>";
          echo "<li><span class='brown'>Departure Time:</span> " . $row['departure_date'] . "</li>"; // Assuming departure_time is the column name
          echo "<li><span class='brown'>Expected Arrival Time:</span> " . $row['arrival_date'] . "</li>"; // Assuming expected_arrival_time is the column name
          echo "</ul>";
          switch ($row["progress"]) {
            case "Not Started":
              echo "<div>
                <button onClick='showStartTripForm(" . $row["id"] . ")'>Start Trip</button></div>";
              break;
            case "In Progress":
              echo "<button onClick='showEndTripForm(" . $row["id"] . ")'>End Trip</button></form></div>";
              break;
          }

          echo "</div>";
          echo "</div>";
          echo "</div>";
        }
      } else {
        echo " <div class='no-trips'>";
        echo "<p>No active trips </p>";
        echo "<img src='../assets/images/nodata2.png' />";
        echo "</div>";
      }
      ?>
  </div>


  <div class="start-trip-form">
    <div class="popup-content">
      <div class="start-modal animate__animated animate__zoomIn">
        <i class=" modal-icon fa-solid fa-plane"></i>
        <div>
          <h3>Start Trip ?</h3>
          <p>Proceed to end the trip. This action cannot be undone.</p>
        </div>

        <div class="forms-modal delete-section-button">
          <form method='post'>
            <input type='hidden' name='start_trip' />
            <input type='hidden' name='trip_id' id="tripId" />
            <button class="start-trip-btn">Start Trip</button>
          </form>
          <form> <button class="end-trip-btn">Cancel</button></form>
        </div>
      </div>
    </div>
  </div>

  <div class="end-trip-form">
    <div class="popup-content">
      <div class="delete-modal animate__animated animate__zoomIn">
        <i class="modal-icon fa-solid fa-triangle-exclamation"></i>
        <div>
          <h3>End Trip ?</h3>
          <p>Proceed to end the trip. This action cannot be undone.</p>
        </div>
        <div class="forms-modal delete-section-button">
          <form method='post'>
            <input type='hidden' name='end_trip' />
            <input type='hidden' name='trip_id' id="trip_Id" />
            <button class="end-trip-btn">End Trip</button>
          </form>
          <form><button class="cancel">Cancel</button></form>
        </div>
      </div>
    </div>
  </div>
  </main>
  </div>
</body>

<?php include('../includes/pageFooter.php'); ?>