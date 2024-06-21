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


?>

<body>
    <header class="parcel-header">

        <div class="logo">
            <a href="./sender.php" class="anchor-logo">
                <p class="logo-p1">quickSend</p>
                <p class="logo-p2">Secure Parcel Delivery System</p>
            </a>
        </div>
    </header>
    <main class="main-settings">
        <div class="success-section">
            <img src="../assets/images/purchase.png" class="successnot" />
            <h5>Payment successfully processed</h5>
            <a href="./sender.php">Back to Home</a>
        </div>
    </main>
</body>
<?php include('../includes/pageFooter.php'); ?>