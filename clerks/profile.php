<?php
include('../includes/pageHeader.php');
include('../config/config.php');

session_start();

if (!isset($_SESSION['clerk_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

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

        <nav>
            <ul class="navlinks">
                <li>
                    <a href="/">Today's Trips</a>
                </li>
                <li>
                    <a href="/">Completed</a>
                </li>
            </ul>
        </nav>
    </header>
    <main class="main-settings">
        <div>
            <p>Account Details</p>
        </div>
        <section id="profile">
            <div class="profile-settings">
                <?php echo "<td><img class='table-img' src=\"../assets/images/{$user_image}\" /></td>";
                ?>
                <form>
                    <input type="file" />
                </form>
            </div>

            <div class="username-settings">
                <form>
                    <h5>Update Account Details</h5>
                    <label for="username">Username</label><br />
                    <input type='text' name='username' value='<?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>' /><br /> <br />
                    <button class="settings-button">Update</button>
                </form>
            </div>

            <div class="password-settings">
                <form>
                    <h5>Change Password</h5>
                    <label for="currpassword">Current Password</label><br />
                    <input type="text" name="currpassword" /><br />
                    <label for="newpassword">New Password</label><br />
                    <input type="text" name="newpassword" /><br />
                    <label for="conpassword">Confirm Password</label><br />
                    <input type="text" name="conpassword" /><br />
                    <button class="settings-button">Update</button>
                </form>
            </div>

            <div class="delete-acc">
                <form>
                    <button>Delete Account</button>
                </form>
            </div>
    </main>
</body>
<?php include('../includes/pageFooter.php'); ?>