<?php
session_start();
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scheduleId = $_POST['scheduleid'];
    $useremail = $_SESSION["user"];
    
    // Get the patient's ID
    $sql = "SELECT pid FROM patient WHERE pemail=?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $patientData = $result->fetch_assoc();
    $patientId = $patientData['pid'];

    // Get the maximum appointment number
    $sqlMax = "SELECT MAX(apponum) as max_apponum FROM appointment";
    $stmtMax = $database->prepare($sqlMax);
    $stmtMax->execute();
    $resultMax = $stmtMax->get_result();
    $maxApponum = $resultMax->fetch_assoc()['max_apponum'];
    $newApponum = $maxApponum ? $maxApponum + 1 : 1; // Increment max appointment number or set to 1

    // Insert the new appointment
    $sqlInsert = "INSERT INTO appointment (apponum, pid, scheduleid) VALUES (?, ?, ?)";
    $stmtInsert = $database->prepare($sqlInsert);
    $stmtInsert->bind_param("iii", $newApponum, $patientId, $scheduleId);
    if ($stmtInsert->execute()) {
        echo "Appointment created successfully.";
    } else {
        echo "Error creating appointment: " . $database->error;
    }
}
?>
