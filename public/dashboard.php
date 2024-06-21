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

$sqlAmount = "SELECT SUM(amount) AS total_amount FROM transactions";
$resultAmount = $conn -> query($sqlAmount);

if($resultAmount -> num_rows>0){
  $rowAmount = $resultAmount->fetch_assoc();
  $totalAm = $rowAmount['total_amount'] ;
}else{
  $totalAm = 0;
}

$sqlClerks = "SELECT COUNT(*) AS active_user_count FROM senders WHERE status = 'active'";
$resultClerks = $conn->query($sqlClerks);

// Fetch active drivers count
if ($resultClerks) {
  $rowClerks = $resultClerks->fetch_assoc();
  $activeClerksCount = $rowClerks['active_user_count'];
} else {
  $activeClerksCount = 0;
}

$sqlParcels = "SELECT COUNT(*) AS total_parcels_count FROM parcels WHERE track_progress = 'Cleared'";
$resultParcels = $conn->query($sqlParcels);

// Fetch total parcels count
if ($resultParcels) {
  $rowParcels = $resultParcels->fetch_assoc();
  $totalParcelsCount = $rowParcels['total_parcels_count'];
} else {
  $totalParcelsCount = 0;
}

$sqlParcelsProgress = "SELECT COUNT(*) AS total_parcels_progress FROM parcels WHERE track_progress = 'Not Cleared'";
$resultParcelsProgress = $conn->query($sqlParcelsProgress);

// Fetch total parcels count
if ($resultParcelsProgress) {
  $rowParcelsProgess = $resultParcelsProgress->fetch_assoc();
  $totalParcelsProgressCount = $rowParcelsProgess['total_parcels_progress'];
} else {
  $totalParcelsProgressCount = 0;
}

$oneWeekAgo = date('Y-m-d', strtotime('-1 week'));
$sqlTrips = "SELECT * FROM trips WHERE departure_date >= '$oneWeekAgo' AND status = 'active'";
$resultTrips = $conn->query($sqlTrips);
$dataPoints = array();
if ($resultTrips->num_rows > 0) {
  while ($row = $resultTrips->fetch_assoc()) {
    $departureDayOfWeek = date('l', strtotime($row['departure_date']));
    if (isset($dataPoints[$departureDayOfWeek])) {
      $dataPoints[$departureDayOfWeek]++;
    } else {
      $dataPoints[$departureDayOfWeek] = 1;
    }
  }
}
$dataPointsFormatted = array();
$daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
foreach ($daysOfWeek as $day) {
  $count = isset($dataPoints[$day]) ? $dataPoints[$day] : 0;
  $dataPointsFormatted[] = array("y" => $count, "label" => $day);
}

$sqlCompletedTripsCount = "SELECT COUNT(*) AS completed_trips_count FROM trips WHERE progress = 'Completed'";
$sqlNotCompletedTripsCount = "SELECT COUNT(*) AS not_completed_trips_count FROM trips WHERE progress <> 'Completed'";
$resultCompletedTrips = $conn->query($sqlCompletedTripsCount);
$resultNotCompletedTrips = $conn->query($sqlNotCompletedTripsCount);
$tripDataPoints = array();
if ($resultCompletedTrips && $resultNotCompletedTrips) {
  $rowCompletedTrips = $resultCompletedTrips->fetch_assoc();
  $rowNotCompletedTrips = $resultNotCompletedTrips->fetch_assoc();
  $tripDataPoints[] = array("y" => $rowCompletedTrips['completed_trips_count'], "name" => "Completed Trips", "color" => "#2ECC71");
  $tripDataPoints[] = array("y" => $rowNotCompletedTrips['not_completed_trips_count'], "name" => "Not Completed Trips", "color" => "#E74C3C");
} else {
  $tripDataPoints[] = array("y" => 0, "name" => "Completed Trips", "color" => "#2ECC71");
  $tripDataPoints[] = array("y" => 0, "name" => "Not Completed Trips", "color" => "#E74C3C");
}
$totalTrips = array_sum(array_column($tripDataPoints, 'y'));


$parcelDataPoints = array();
for ($i = 5; $i >= 0; $i--) {
  $startOfMonth = date('Y-m-01', strtotime("-$i months"));
  $endOfMonth = date('Y-m-t', strtotime("-$i months"));
  $sql = "SELECT COUNT(*) AS parcel_count FROM parcels WHERE track_progress = 'Cleared' AND created_at >= '$startOfMonth' AND created_at <= '$endOfMonth'";
  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $parcelCount = $row['parcel_count'];
  } else {
    $parcelCount = 0;
  }
  $monthLabel = date('F Y', strtotime("-$i months"));
  $parcelDataPoints[] = array("y" => $parcelCount, "label" => $monthLabel);
}


$conn->close();
include('../includes/Header.php');
?>
<main class="dash-main">

  <script>
    window.onload = function() {
      var chart = new CanvasJS.Chart("chartContainer", {
        title: {
          text: "Trips Over the Past Week by Departure Day",
          fontFamily: "Poppins",
          fontSize: 18
        },
        axisY: {
          title: "Number of Trips",
          labelFontFamily: "Poppins",
          labelFontSize: 12,
        },
        axisX: {
          title: "Day of the Week",
          labelFontFamily: "Poppins",
          labelFontSize: 12,
        },
        data: [{
          type: "line",
          dataPoints: <?php echo json_encode($dataPointsFormatted, JSON_NUMERIC_CHECK); ?>
        }]
      });
      chart.render();

      var tripChart = new CanvasJS.Chart("tripChartContainer", {
        animationEnabled: true,
        theme: "light2",
        title: {
          text: "Distribution of Trip Completion Status",
          fontFamily: "Poppins",
          fontSize: 18
        },
        subtitles: [{
          text: "Total Trips: <?php echo $totalTrips; ?>",
          fontFamily: "Poppins",
          fontSize: 12

        }],
        data: [{
          type: "doughnut",
          showInLegend: true,
          legendText: "{name}: {y} (#percent%)",
          indexLabel: "{name} - #percent%",
          indexLabelPlacement: "inside",
          dataPoints: <?php echo json_encode($tripDataPoints, JSON_NUMERIC_CHECK); ?>
        }]
      });
      tripChart.render();

      var barChart = new CanvasJS.Chart("barChartContainer", {
        animationEnabled: true,
        title: {
          text: "Number of Parcels Cleared Over the Past 6 Months",
          fontFamily: "Poppins",
          fontSize: 18
        },
        axisX: {
          title: "Month"
        },
        axisY: {
          title: "Number of Parcels"
        },
        data: [{
          type: "column",
          dataPoints: <?php echo json_encode($parcelDataPoints, JSON_NUMERIC_CHECK); ?>
        }]
      });

      barChart.render();
    }
  </script>

  <div class="dash-stats">
    <div class="dash-items">
      <div><i class="fa-solid fa-money-bill"></i></div>
      <div>
        <h5>Amount garnered</h5>
        <p> Kshs: <?php echo $totalAm; ?></p>
      </div>
    </div>
    <div class="dash-items">
      <div><i class="fa-solid fa-user-plus"></i></div>
      <div>
        <h5>Total Users</h5>
        <p><?php echo $activeClerksCount; ?></p>
      </div>
    </div>
    <div class="dash-items">
      <div><i class="fa-solid fa-cube"></i></div>
      <div>
        <h5>Cleared Parcels</h5>
        <p><?php echo $totalParcelsCount; ?></p>
      </div>
    </div>
    <div class="dash-items">
      <div><i class="fa-solid fa-cube"></i></div>
      <div>
        <h5>Parcels in progress</h5>
        <p><?php echo $totalParcelsProgressCount; ?></p>
      </div>
    </div>
  </div>

  <div class="report-operations">
    <div class="chartContainer" id="chartContainer" style="height: 400px; width: 70%; color:#00adb5; "></div>
    <div class="tripChartContainer" id="tripChartContainer" style="height: 400px; width: 40%;"></div>
  </div>
  <div class="barChartContainer" id="barChartContainer" style="height: 400px; width: 94%; margin: 1rem ;"></div>
  <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

</main>

<?php include('../includes/Footer.php'); ?>