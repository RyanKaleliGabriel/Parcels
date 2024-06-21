<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM senders WHERE id='$user_id'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $user_name = $user['username'];
    $user_image = $user['image_url'];
    $user_email = $user['email'];
} else {
    header("Location: ../pages/login.php");
    exit();
}

$runame = $_SESSION['recipient_name'];
$email = $_SESSION['recipient_email'];
$desc = $_SESSION['description'];
$weight = $_SESSION['weight'];
$tel = $_SESSION['tel'];
$trip_id = $_SESSION['trip_id'];

$price = $weight * 10000;
$vattax = $price * 0.12;
$total = $price + $vattax;


$sql_trips_parcel = "SELECT * FROM trips WHERE `id`='$trip_id' AND `status` = 'active'";
$result_trips_parcel = $conn->query($sql_trips_parcel);

if ($result_trips_parcel->num_rows > 0) {
    $trip_data = $result_trips_parcel->fetch_assoc();
    $departure_date = $trip_data['departure_date'];
    $arrival_date = $trip_data['arrival_date'];
    $departure_point = $trip_data['departure_point'];
    $pickup_point = $trip_data['pickup_point'];
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_parcel'])) {
        $parcel_id = $_POST['parcel_id'];
        $progress = 'Cleared';

        $stmt = $conn->prepare("UPDATE `parcels` SET `track_progress`=? WHERE `id`=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("si", $progress, $parcel_id);
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
                <a href="./sender.php" class="anchor-logo">
                    <p class="logo-p1">quickSend</p>
                    <p class="logo-p2">Secure Parcel Delivery System</p>
                </a>
            </div>


            <div class="header-profile">
                <?php echo "<td><img class='table-img' src=\"../assets/images/{$user_image}\" /></td>";
                ?>
                <a href="./profile.php" class="anchor-logo">
                    <?php echo "<p>" . $user_name . "</p>"; ?> </a>
            </div>
        </header>


        <main>
            <div class="checkpage">
                <div class="how-to-pay">
                    <p>How would you like to pay ?</p>
                    <hr />
                    <div class="pay-images">
                        <img src="../assets/images/mpesa.png" />
                        <img src="../assets/images/visa.png" />
                        <img src="../assets/images/paypal.png" />
                        <form method="post" action="checkout.php">
                            <input type="hidden" value='<?php echo htmlspecialchars($email); ?>' name="remail" />
                            <input type="hidden" value='<?php echo htmlspecialchars($user_email); ?>' name="semail" />
                            <input type="hidden" value='<?php echo htmlspecialchars($total); ?>' name="price" />
                            <input type="hidden" name="description" value='<?php echo htmlspecialchars($desc); ?>'/>
                            <button class="stripe-btn"><img src="../assets/images/stripe.jpg" class="stripe" /></button>
                        </form>
                    </div>
                    <div class="cashpayment">
                        <button> <i class="fa-solid fa-money-bill"></i> Cash</button>
                    </div>
                </div>

                <div class="order-summary">
                    <h4>Order Summary</h4>
                    <p><?php echo $desc; ?></p>
                    <hr />
                    <div>
                        <p>From</p>
                        <p><?php echo $departure_point; ?></p>
                    </div>
                    <div>
                        <p>To</p>
                        <p><?php echo $pickup_point; ?></p>
                    </div>

                    <div>
                        <p>Departure Date</p>
                        <p><?php echo $departure_date; ?></p>
                    </div>


                    <div>
                        <p>Arrival Date</p>
                        <p><?php echo $arrival_date; ?></p>
                    </div>

                    <div>
                        <p>Parcel Weight(kgs)</p>
                        <p><?php echo $weight; ?></p>
                    </div>
                    <hr />
                    <div>
                        <h5>Total Amount(Inclusive VAT 12%)</h5>
                        <h5>Kshs: <?php echo $total / 100 ?></h5>
                    </div>
                    <button class="cash">Continue to secure payment</button>
                </div>
            </div>
    </div>

    </main>
    </div>
</body>

<?php include('../includes/pageFooter.php'); ?>