<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if (!isset($_SESSION['clerk_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$parcel_id = $_SESSION['parcel_id'];
$to = $_SESSION['$email_con'];

$user_id = $_SESSION['clerk_id'];
$user_query = "SELECT * FROM clerks WHERE id='$user_id'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $user_name = $user['username'];
    $user_image = $user['image_url'];
} else {
    $user_name = "Unknown";
}
?>

<body>
    <header class="parcel-header">

        <div class="logo">
            <a href="./clerkspanel.php" class="anchor-logo">
                <p class="logo-p1">quickSend</p>
                <p class="logo-p2">Secure Parcel Delivery System</p>
            </a>
        </div>
    </header>
    <main class="main-settings">
        <div class="success-section">
            <img src="../assets/images/notification.svg" class="successnot" />
            <h5>Parcel No. <?php echo $parcel_id ?> has been successfully cleared. The client will receive an acknowledge email at <?php echo $to ?></h5>
            <a href="./clerkspanel.php" >Back to Home</a>
        </div>
    </main>
</body>
<?php include('../includes/pageFooter.php'); ?>