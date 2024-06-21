<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['logout'])){
    $_SESSION['admin_id'] = "";
    header("refresh:2");
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="./../assets/css/styles.css" />
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let loadedPage = window.location.href;
      let page = loadedPage.split("/")
      let currentPage = page[5]
      if (currentPage === "dashboard.php") {
        document.getElementById("dashboard-item").classList.add("active-dd");
        console.log("true")
      } else if (currentPage.includes("parcels.php")) {
        document.getElementById("parcels-item").classList.add("active-dd");
      } else if (currentPage.includes("trips.php")) {
        document.getElementById("trips-item").classList.add("active-dd");
      } else if (currentPage.includes("drivers.php")) {
        document.getElementById("drivers-item").classList.add("active-dd");
      } else if (currentPage.includes("admins.php")) {
        document.getElementById("admins-item").classList.add("active-dd");
      } else if (currentPage.includes("settings.php")) {
        document.getElementById("settings-item").classList.add("active-dd");
      } else if (currentPage.includes("clerks.php")) {
        document.getElementById("clerks-item").classList.add("active-dd");
      }
    });
  </script>
</head>

<body>
  <section class="dashboard">
    <header class="dash-header">
      <div>
        <?php
          echo "<p>Welcome, " . $user_name . "</p>";
          ?>

        <?php echo "<img class='table-img' src=\"../assets/images/{$user_image}\" />";
        ?>
      </div>
    </header>
    <aside class="dash-aside">
      <div class="logo">
        <p class="logo-p1">quickSend</p>
        <p class="logo-p2">Secure Parcel Delivery Systems</p>
      </div>
      <ul class="first-list">
        <li>
          <a href="../public/dashboard.php">
            <div>
              <i class="fa-solid fa-chart-line"></i>
              <p id="dashboard-item">Dashboard</p>
            </div>
          </a>
        </li>
        <li>
          <a href="../pages/parcels.php">
            <div>
              <i class="fa-solid fa-cube"></i>
              <p id="parcels-item">Parcels</p>
            </div>
          </a>
        </li>
        <li>
          <a href="../pages/drivers.php">
            <div>
              <i class="fa-solid fa-id-card"></i>
              <p id="drivers-item">Drivers</p>
            </div>
          </a>
        </li>
        <li>
          <a href="../pages/trips.php">
            <div>
              <i class="fa-solid fa-plane"></i>
              <p id="trips-item">Trips</p>
            </div>
          </a>
        </li>
        <li>
          <a href="../pages/clerks.php">
            <div>
              <i class="fa-solid fa-user-plus"></i>
              <p id="clerks-item">Clerk</p>
            </div>
          </a>
        </li>
        <li>
          <a href="../pages/admins.php">
            <div>
              <i class="fa-solid fa-user-tie"></i>
              <p id="admins-item">System Admins</p>
            </div>
          </a>
        </li>
      </ul>
      <ul class="last-aside">
        <li>
          <a href="../pages/settings.php">
            <div>
              <i class="fa-solid fa-user"></i>
              <p id="settings-item">Account</p>
            </div>
          </a>
        </li>
        <li>
          <div>
            <form method="post">
              <input type="hidden" name="logout" />
              <i class="fa-solid fa-right-from-bracket"></i>
              <button class="logout">Logout</button>
            </form>
           
            
          </div>
        </li>
      </ul>
    </aside>