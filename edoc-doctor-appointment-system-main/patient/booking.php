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
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        /* Add mobile responsiveness */
        @media (max-width: 768px) {
            .menu-container {
                width: 100%;
            }
            .profile-container, .sub-table {
                width: 100%;
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

    if (isset($_SESSION["user"])) {
        if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'p') {
            header("location: ../login.php");
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
    }

    include("../connection.php");

    $sqlmain = "SELECT * FROM patient WHERE pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userfetch = $result->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');

    if ($_GET && isset($_GET["id"])) {
        $id = $_GET["id"];

        $sqlmain = "SELECT * FROM schedule 
                    INNER JOIN doctor ON schedule.docid = doctor.docid 
                    WHERE schedule.scheduleid = ? ORDER BY schedule.scheduledate DESC";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $scheduleid = $row["scheduleid"];
        $title = $row["title"];
        $docname = $row["docname"];
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];

        // Fetch maximum apponum and increment it
        $sql2 = "SELECT MAX(apponum) AS max_apponum FROM appointment";
        $result2 = $database->query($sql2);
        $row2 = $result2->fetch_assoc();
        $max_apponum = $row2['max_apponum'];
        $apponum = $max_apponum ? $max_apponum + 1 : 1; // Increment or set to 1 if null
    }
    ?>

    <div class="container">
        <div class="menu">
            <!-- Menu code omitted for brevity -->
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php">
                            <button class="login-btn btn-primary-soft btn btn-icon-back" style="width:125px">Back</button>
                        </a>
                    </td>
                    <td colspan="3"></td>
                </tr>

                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                                    <tbody>
                                        <?php if (isset($scheduleid)): ?>
                                            <form action="booking-complete.php" method="post">
                                                <input type="hidden" name="scheduleid" value="<?= $scheduleid ?>">
                                                <input type="hidden" name="apponum" value="<?= $apponum ?>">
                                                <input type="hidden" name="date" value="<?= $today ?>">

                                                <tr>
                                                    <td style="width: 50%;" rowspan="2">
                                                        <div class="dashboard-items search-items">
                                                            <div style="width:100%">
                                                                <div class="h1-search" style="font-size:25px;">
                                                                    Session Details
                                                                </div><br><br>
                                                                <div class="h3-search" style="font-size:18px;line-height:30px">
                                                                    Doctor name: &nbsp;&nbsp;<b><?= $docname ?></b><br>
                                                                    Session Title: &nbsp;&nbsp;<b><?= $title ?></b><br>
                                                                    Scheduled Date: &nbsp;&nbsp;<b><?= $scheduledate ?></b><br>
                                                                    Time: &nbsp;&nbsp;<b><?= $scheduletime ?></b><br>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="width: 25%;">
                                                        <div class="dashboard-items search-items">
                                                            <div style="width:100%;padding: 15px;">
                                                                <div class="h1-search" style="font-size:20px;line-height:35px;text-align:center;">
                                                                    Your Appointment Number
                                                                </div>
                                                                <center>
                                                                    <div class="dashboard-icons" style="font-size:70px;font-weight:800;color:var(--btnnictext);background-color: var(--btnice);">
                                                                        <?= $apponum ?>
                                                                    </div>
                                                                </center>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="submit" class="login-btn btn-primary btn btn-book" style="width:95%;" value="Confirm Appointment" name="booknow">
                                                    </td>
                                                </tr>
                                            </form>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2">No session found</td>
                                            </tr>
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
</body>
</html>
