<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if (!isset($_SESSION['clerk_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['clerk_id'];

$check_email_driver = "SELECT * FROM clerks WHERE id='$user_id' AND status='active'";
$result_clerk = $conn->query($check_email_driver);

if ($result_clerk->num_rows > 0) {
    $user = $result_clerk->fetch_assoc();
    $user_name = $user['username'];
    $user_image = $user['image_url'];
} else {
    header("Location: ../pages/login.php");
    exit();
}

$sql_trips = "SELECT * FROM trips WHERE `status` = 'active' AND `clerk_id` = $user_id AND `progress`='completed' ";
$result_trips = $conn->query($sql_trips);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_parcel'])) {
        $parcel_id = $_POST['parcel_id'];
        $progress = 'Cleared';
        $cleared_on = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("UPDATE `parcels` SET `track_progress`=?, `cleared_on`=? WHERE `id`=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssi", $progress, $cleared_on, $parcel_id);
        if ($stmt->execute()) {
            header("refresh:1");
        } else {
            die("Execute failed: " . $stmt->error);
        }
    }

    if (isset($_POST['clear_parcels'])) {
        session_start();
        $trip_id = $_POST['trip_id'];
        $_SESSION['trip_id'] = $trip_id;
        header("Location: parcels.php");

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
            <div class="parcel-container">
                <?php
                if ($result_trips->num_rows > 0) {
                    while ($row = $result_trips->fetch_assoc()) {
                        
                        echo "<div class='parcel-child'>";
                        echo "<div>"; // Add closing angle bracket here
                        echo "<h4 class='trip-clerk'>Trip Number " . $row['id'] . "</h4>";

                        if (!empty($row['cleared_on'])) {
                            $formatted_cleared_on = date('Y-m-d', strtotime($row['cleared_on']));
                            echo "<h4 class='received'>Cleared on " . $formatted_cleared_on . "</h4>";
                        }
                        echo "</div>";
                        echo "<ul>";
                        echo "<li>";

                        $sqlParcels = "SELECT COUNT(*) AS active_parcels_count FROM parcels WHERE trip_id='" . $row['id'] . "' AND status='active'";
                        $resultParcels = $conn->query($sqlParcels);

                        // Fetch active drivers count
                        if ($resultParcels) {
                            $rowParcels = $resultParcels->fetch_assoc();
                            $activeTripsCount = $rowParcels['active_parcels_count'];
                        } else {
                            $activeTripsCount = 0;
                        }

                        echo "<h5>No. of parcels</h5>";
                        echo "<p>" . $activeTripsCount . "</p>";
                        echo "</li>";
                        echo "</ul>";
                        echo "<form method='post' class='proceed-form' value='1'>";
                        echo "<input type='hidden' name='clear_parcels' />";
                        echo "<input type='hidden' name='trip_id' value='" . $row['id'] . "' />";
                        echo "<button class='proceed-clear'>Clear Parcels</button>";
                        echo "</form>";
                        echo "</div>";
                      
                        
                    }
                } else {
                    echo " <div class='no-trips'>";
                    echo "<p>No Active trips</p>";
                    echo "<img src='../assets/images/nodata2.png' />";
                    echo "</div>";
                }
                ?>
            </div>
        </main>
    </div>


    <div class="start-trip-form">
        <div class="popup-content">
            <div class="delete-form animate__animated animate__zoomIn">
                <h1 class="proceed">Proceed to Clear this parcel</h1>
                <div class="delete-section-button">
                    <form method='post'>
                        <input type='hidden' name='clear_parcel' />
                        <input type='hidden' name='parcel_id' id="tripId" />
                        <button class="start-trip-btn">Clear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="end-trip-form">
        <div class="popup-content">
            <div class="delete-form animate__animated animate__zoomIn">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h1 class="proceed">Proceed to end the Trip</h1>
                <p>This action cannot be undone. All Values associated with this field will be lost</p>
                <div class="delete-section-button">
                    <form method='post'>
                        <input type='hidden' name='end_trip' />
                        <input type='hidden' name='trip_id' id="trip_Id" />
                        <button class="end-trip-btn">End Trip</button>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    </main>
    </div>
</body>

<?php include('../includes/pageFooter.php'); ?>