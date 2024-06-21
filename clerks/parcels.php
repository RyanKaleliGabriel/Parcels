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

use PHPMailer\PHPMailer\PHPMailer;

require_once '../vendor/autoload.php';



$trip_id = $_SESSION['trip_id'];
$sql_parcels = "SELECT * FROM parcels WHERE `status` = 'active' AND `trip_id` = $trip_id";
$result_parcels = $conn->query($sql_parcels);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_parcel'])) {
        $parcel_id = $_POST['parcel_id'];
        $progress = 'Cleared';
        $cleared_on = date('Y-m-d H:i:s');
        $to = $_POST['recipient_email'];



        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = 'eb37b5de7cebee';
        $phpmailer->Password = '63c42b152d75bc';
        $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $phpmailer->setFrom('info@quicksend.com', 'Secure Parcel Deliveries');
        $phpmailer->addAddress($to, 'Me');
        $phpmailer->Subject = "Thanks for choosing us!";

        $phpmailer->isHTML(TRUE);
        $phpmailer->Body='<html>Hi there, we are happy to<br>clear your parcel.</br> Pleasure doing business with us. Good Day!</html>';
        $phpmailer->AltBody = 'Hi there, we are happy to clear your parcel. Pleasure doing business with us ';




        $stmt = $conn->prepare("UPDATE `parcels` SET `track_progress`=?, `cleared_on`=? WHERE `id`=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssi", $progress, $cleared_on, $parcel_id);
        if ($stmt->execute()) {
            if (!$phpmailer->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                header("refresh:1");
                echo "Message has been sent";

                $_SESSION['parcel_id'] = $parcel_id;
                $_SESSION['$email_con'] = $to;
                header("Location: ./success.php");
            }
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
            if ($result_parcels->num_rows > 0) {
                while ($parcel = $result_parcels->fetch_assoc()) {
                    echo "<div class='trip-container'>";
                    echo "<div class='trip-child'>";
                    echo "<div>";
                    echo "<div class='trip-intro'>";
                    echo "<p>Trip No. " . $trip_id . "</p>"; // Use the trip ID from the database
                    echo "</div>";
                    echo "<div class='trip-details'>";
                    echo "<ul>";
                    echo "<li><span class='brown'>Recipient Name: </span>" . $parcel['recipient_name'] . "</li>";
                    echo "<li><span class='brown'>Sent By: </span>";
                    $sender_id = $parcel['sender_id'];

                    // SQL query to retrieve sender's name using sender ID
                    $sql_sender = "SELECT * FROM senders WHERE id = '$sender_id'";
                    $result_sender = $conn->query($sql_sender);

                    if ($result_sender && $result_sender->num_rows > 0) {
                        $sender_row = $result_sender->fetch_assoc();
                        echo $sender_row['username'];
                    } else {
                        echo "Unknown";
                    }
                    echo "</li>";

                    echo "<li><span class='brown'>Recipient Phone Number:</span> " . $parcel['recpient_tel'] . "</li>";
                    echo "</ul>";
                    echo "<ul>";
                    echo "<li><span class='brown'>Recipient Email:</span> " . $parcel['recpient_email'] . "</li>";
                    echo "</ul>";
                    echo "<ul>";
                    echo "<li><span class='brown'>Parcel Description:</span> " . $parcel['description'] . "</li>";
                    echo "</ul>";

                    switch ($parcel["track_progress"]) {
                        case "Not Cleared":
                            echo "<div><button onClick='clearParcel(" . $parcel["id"] . ", \"" . $parcel["recpient_email"] . "\")'>Clear Parcel</button></div>";
                            break;
                        case "Cleared":
                            echo "<span class='completed'>" . $parcel["track_progress"] . "</span>";
                            break;
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-parcels'>";
                echo "<p>No parcels found for Trip No. " . $trip_id . "</p>";
                echo "<img src='../assets/images/nodata.png' />";
            }
            ?>
        </main>

        <div class="start-trip-form">
            <div class="popup-content">
                <div class="delete-form animate__animated animate__zoomIn">
                    <h1 class="proceed">Proceed to Clear this parcel</h1>
                    <div class="delete-section-button">
                        <form method='post'>
                            <input type='hidden' name='clear_parcel' />
                            <input type='hidden' name='parcel_id' id="tripId" />
                            <input type="hidden" name='recipient_email' id='recipient_email' />
                            <button class="start-trip-btn">Clear</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

<?php include('../includes/pageFooter.php'); ?>