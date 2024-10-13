
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Sessions</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }

        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }

        .abc.scroll {
            overflow-x: auto;
        }

        .dashboard-items {
            margin-bottom: 20px;
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .header-searchbar {
                width: 100%;
            }

            .h1-search,
            .h3-search,
            .h4-search {
                font-size: 1em;
            }

            .menu-container,
            .sub-table {
                width: 100%;
            }

            .container {
                padding: 10px;
            }

            .dashboard-items {
                width: 100%;
            }
        }




    </style>
</head>


<body>
    <?php
session_start();
if (!isset($_SESSION["user"]) || ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p')) {
    header("location: ../login.php");
    exit();
}
$useremail = $_SESSION["user"];

include("../connection.php");
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$result = $stmt->get_result();
$userfetch = $result->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Check if the search field is set to avoid undefined key warnings
$doctorName = isset($_POST['search']) ? $_POST['search'] : '';

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// SQL query to fetch schedules
$sqlmain = "SELECT schedule.*, schedule.price, doctor.docname, venue.name AS venue_name, venue.address AS venue_address 
            FROM schedule 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            INNER JOIN venue ON schedule.venue_id = venue.venue_id
            WHERE schedule.scheduledate >= ? AND LOWER(doctor.docname) LIKE LOWER(CONCAT('%', ?, '%'))
            ORDER BY schedule.scheduledate ASC";

$stmt = $database->prepare($sqlmain);
$stmt->bind_param("ss", $today, $doctorName);
$stmt->execute();
$result = $stmt->get_result();
?>

    
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username, 0, 13); ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail, 0, 22); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Menu Items -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appointment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="precords.php" class="non-style-link-menu"><div><p class="menu-text">My records</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="dash-body">
            <table border="0" width="100%" style="margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php">
                            <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px;width:125px">
                                <font class="tn-in-text">Back</font>
                            </button>
                        </a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name" value="<?php echo $doctorName; ?>" list="doctors">&nbsp;&nbsp;
                            <input type="submit" value="Search" class="login-btn btn-primary btn">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px; color: rgb(119, 119, 119); text-align: right;">Today's Date</p>
                        <p class="heading-sub12"><?php echo $today; ?></p>
                    </td>
                    <td width="10%">
                        <button class="btn-label"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px; font-size:18px; color:rgb(49, 49, 49)">
                            <?php echo "Sessions for Dr. " . $doctorName . " (" . $result->num_rows . ")"; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px; border:none">
                                   
                                
                                

                                
                                
                                
                                
                                
                                
                                
                                
                                <tbody>
                                        <?php if ($result->num_rows == 0): ?>
                                            <tr><td colspan="4"><center>No sessions found!</center></td></tr>
                                        <?php else: ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td style="width: 25%;">
                                                        <div class="dashboard-items search-items">
                                                            <div>
                                                                <div class="h1-search"><?= substr($row["title"], 0, 21) ?></div>
                                                                <div class="h3-search"><?= $row["docname"] ?></div>
                                                                <div class="h4-search">
                                                                    Date: <?= $row["scheduledate"] ?> | Time: <?= substr($row["scheduletime"], 0, 5) ?>
                                                                </div>
                                                                <div class="h4-search">
                                                                    Venue: <?= $row["venue_name"] ?>, <?= $row["venue_address"] ?>
                                                                </div>
                                                                <div class="h4-search">
                                                                    Price: Rs. <?= number_format($row["price"], 2) ?>
                                                                </div>
                                                                <button class="login-btn btn-primary-soft btn" 
                                                                    onclick="openPaymentPopup(<?= $row['scheduleid'] ?>)">
                                                                    Book Now
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>

     <div id="paymentPopup" class="overlay" style="display: none;">
    <div class="popup">
        <h2>Payment Details</h2>
        <form id="paymentForm" onsubmit="return validatePaymentForm()">
            <input type="text" id="cardNumber" class="input-text-popup" placeholder="Card Number (xxxx xxxx xxxx xxxx)" maxlength="19" required oninput="formatCardNumber()">
            
            <input type="month" id="expiryDate" class="input-text-popup" required>
            
            <input type="password" id="cvv" class="input-text-popup" placeholder="CVV" maxlength="3" required>
            <input type="hidden" id="sessionId">
            
            <button type="button" class="btn btn-primary-gray" onclick="closePaymentPopup()">Cancel</button>
            <button type="submit" class="btn btn-primary">Pay</button>
        </form>
    </div>
</div>

<script>
    // Auto-format the card number with spaces every 4 digits
    function formatCardNumber() {
        let cardInput = document.getElementById('cardNumber');
        let value = cardInput.value.replace(/\s+/g, '').replace(/[^0-9]/gi, ''); // Remove spaces and non-digits
        
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value; // Add space every 4 digits
        cardInput.value = formattedValue;
    }

    function openPaymentPopup(sessionId) {
        document.getElementById('sessionId').value = sessionId;
        document.getElementById('paymentPopup').style.display = 'block';
    }

    function closePaymentPopup() {
        document.getElementById('paymentPopup').style.display = 'none';
    }

    function validatePaymentForm() {
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, ''); // Remove spaces for validation
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;

        // Validate card number (16 digits)
        if (!/^\d{16}$/.test(cardNumber)) {
            alert('Invalid card number.');
            return false;
        }

        // Validate expiry date (must not be in the past)
        const currentDate = new Date();
        const selectedDate = new Date(expiryDate);
        if (selectedDate < currentDate) {
            alert('Invalid expiry date.');
            return false;
        }

        // Validate CVV (3 digits)
        if (!/^\d{3}$/.test(cvv)) {
            alert('Invalid CVV.');
            return false;
        }

        // If all validations pass, redirect to booking.php with session ID
        const sessionId = document.getElementById('sessionId').value;
        window.location.href = `booking.php?id=${sessionId}`;
        return false;
    }
</script>

</body>
</html>