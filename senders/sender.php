<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../pages/login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$check_email_sender = "SELECT * FROM senders WHERE id=$user_id AND status='active'";
$result_sender = $conn->query($check_email_sender);

if ($result_sender->num_rows > 0) {
  $user = $result_sender->fetch_assoc();
  $user_name = $user['username'];
  $user_image = $user['image_url'];
} else {
  header("Location: ../pages/login.php");
  exit();
}



$sql_trips = "SELECT * FROM trips WHERE `status`='active'";
$result_trips = $conn->query(($sql_trips));

$sql_parcels = "SELECT * FROM parcels WHERE `status`='active' AND `sender_id`=$user_id";
$result_parcels = $conn->query(($sql_parcels));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['send_parcel'])) {
    $desc = $_POST['desc'];
    $email = $_POST['email'];
    $runame = $_POST['runame'];
    $tel = $_POST['tel'];
    $trip_id = $_POST['trip_id'];
    $weight = $_POST['weight'];
    $sender_id = $user_id;
    $default_status = "active";
    $track_progress = "Not Cleared";
    $created_at = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO `parcels` (`recipient_name`, `description`, `quantity`, `recpient_tel`,`recpient_email`,  `status`, `sender_id`, `trip_id`, `track_progress`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssiiss", $runame, $desc, $weight, $tel, $email, $default_status, $sender_id, $trip_id, $track_progress, $created_at);
    if ($stmt->execute()) {
      session_start();
      $_SESSION['recipient_name'] = $runame;
      $_SESSION['recipient_email'] = $email;
      $_SESSION['description'] = $desc;
      $_SESSION['weight'] = $weight;
      $_SESSION['tel'] = $tel;
      $_SESSION['trip_id'] = $trip_id;
      $_SESSION['user_id'] = $user_id;
      header("Location: ./order.php");
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

      <div class="tracking">
        <input type="text" placeholder="Enter Tracking Id" />
      </div>

      <div class="header-profile">

        <?php echo "<td><img class='table-img' src=\"../assets/images/{$user_image}\" /></td>";
        ?>
        <a href="./profile.php" class="anchor-logo">
          <?php
          echo "<p>" . $user_name . "</p>";
          ?>
        </a>
      </div>
    </header>
    <main>
      <div class="newDiv">
        <button class="newparc">Send New +</button>
      </div>

      <div class="parcel-container">
        <?php
        if ($result_parcels->num_rows > 0) {
          while ($row = $result_parcels->fetch_assoc()) {
            echo "<div class='parcel-child'>";
            echo "<div>"; // Add closing angle bracket here
            $trip_id = $row['trip_id'];
            $sql_trip_parcel = "SELECT departure_date FROM trips WHERE id = '" . $trip_id . "'";
            $result = $conn->query($sql_trip_parcel);
            if ($result->num_rows > 0) {
              $trip_row = $result->fetch_assoc();
              $departure_date = $trip_row['departure_date'];
              $formatted_departure_date = date('Y-m-d', strtotime($departure_date));
              echo "<h4 class='sent'>Will be sent on " . $formatted_departure_date . "</h4>";
            } else {
              echo "<h4 class='sent'>Departure date not found</h4>";
            }
            if (!empty($row['cleared_on'])) {
              $formatted_cleared_on = date('Y-m-d', strtotime($row['cleared_on']));
              echo "<h4 class='received'>Cleared on " . $formatted_cleared_on . "</h4>";
            }
            echo "</div>";
            echo "<ul>";
            echo "<li>";
            echo "<h5>Parcel Number</h5>";
            echo "<p>" . $row['id'] . "</p>";
            echo "</li>";
            echo "<li>";
            echo "<h5>Recipient Name</h5>";
            echo "<p>" . $row['recipient_name'] . "</p>";
            echo "</li>";
            echo "<li>";
            echo "<h5>Parcel Details </h5>";
            echo "<p>" . $row['description'] . " (" . $row['quantity'] . "kgs)</p>";
            echo "</li>";
            echo "<li>";
            echo "<h5>To</h5>";
            $sql = "SELECT * FROM trips WHERE status='active' AND id='" . $row['trip_id'] . "' ";
            $result_trips = $conn->query($sql);
            if ($result_trips->num_rows > 0) {
              while ($trip_row = $result_trips->fetch_assoc()) {
                echo "<p>" . $trip_row['pickup_point'] . "</p>";
              }
            } else {
              echo "Not found";
            }
            echo "</li>";
            echo "</ul>";
            echo "</div>";
          }
        } else {
          echo " <div class='no-trips'>";
          echo "<p>No parcels sent for this user</p>";
          echo "<img src='../assets/images/nodata2.png' />";
          echo "</div>";
        }
        ?>
      </div>

    </main>

    <div class="popup-form">
      <div class="popup-content">
        <div class="send-parcel animate__animated animate__zoomIn">
          <div class="custom-logo">
            <h5 class="logo-p1">quickSend</h5>
            <p>Secure Parcel Delivery System</p>
            <h5 class="form-heading">Send New Parcel</h5>
          </div>
          <form method="post">
            <div class="send-form">
              <div> <input type="hidden" name="send_parcel" value="1">
                <input type="hidden" name="sender_id" />
                <label for="runame">Recipient Full Name</label><br />
                <input type="text" name="runame" placeholder="John Doe" required /><br />
                <label for="email">Recipient email</label><br />
                <input type="text" name="email" placeholder="example@email.com" required /><br />
                <label for="tel">Recipient phone number</label><br />
                <input type="tel" name="tel" placeholder="+254 704 383 812" required /><br />
              </div>
              <div>
                <label for="desc">Parcel Description</label><br />
                <textarea cols="30" rows="5" name="desc" required></textarea>
                <label for="weight">Parcel Weight (Kgs)</label><br />
                <input type="number" name="weight" required /><br />
                <label for="trip">Trip</label><br />
                <select name="trip_id" required>
                  <?php
                  $sql_choice = "SELECT * FROM trips WHERE status='active' AND departure_date > CURDATE()";
                  $result_trips_choice = $conn->query($sql_choice);
                  if ($result_trips_choice->num_rows > 0) {
                    while ($row = $result_trips_choice->fetch_assoc()) {
                      echo "<option value= '" . $row["id"] . "' >" . $row["departure_point"] . " -> " . $row["pickup_point"] . " (Sent on " . $row["departure_date"] . "  Arrives on " . $row["arrival_date"] . "  ) </option>";
                    }
                  } else {
                    echo "<option disabled>Trips not available</option>";
                  }
                  ?>
                </select><br />
              </div>

            </div>
            <button>Send</button>

          </form>
        </div>
      </div>
    </div>

    <footer>
      <div class="logo">
        <p class="logo-p1">quickSend</p>
        <p class="logo-p2">Secure Parcel Delivery System</p>
      </div>
    </footer>
  </div>
</body>


<?php include('../includes/pageFooter.php'); ?>