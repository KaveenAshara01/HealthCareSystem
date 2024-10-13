<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Add Record</title>
</head>
<body>
    <?php
    session_start();

 
    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];

    // Retrieve doctor ID using the session email
    $doctor_email = $_SESSION["user"];
    $doctor_query = "SELECT docid FROM doctor WHERE docemail='$doctor_email'";
    $doctor_result = $database->query($doctor_query);

    if ($doctor_result->num_rows == 1) {
        $doctor = $doctor_result->fetch_assoc();
        $doctor_id = $doctor["docid"];
    } else {
        echo "Doctor not found.";
        exit();
    }

    // Check if patient ID is set
    if (isset($_GET["pid"])) {
        $pid = $_GET["pid"];
    } else {
        header("location: patient.php");
    }

    // Handle form submission to add record
    if ($_POST) {
        $record_name = $_POST["record_name"];
        $details = $_POST["details"];

        // Insert record into the database
        $database->query("INSERT INTO records (patient_id, description, doctor_id, record_name) VALUES ('$pid', '$details', '$doctor_id', '$record_name')");

        // Redirect back to the records page
        header("location: records.php?pid=$pid");
    }
    ?>
    <div class="container">
        <div class="menu">
        <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                        <a href="patient.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">My Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings   ">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="records.php?pid=<?php echo $pid; ?>"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">Add New Record</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                        <form action="" method="post" style="margin-top: 20px;">
                            <input type="text" name="record_name" placeholder="Enter record name" required><br><br>
                            <textarea name="details" rows="10" cols="80" placeholder="Enter record details" required></textarea><br><br>
                            <input type="submit" value="Add Record" class="login-btn btn-primary btn" style="padding: 15px 50px;">
                        </form>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
