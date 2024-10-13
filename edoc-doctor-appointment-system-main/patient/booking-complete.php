<?php

session_start();

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Import database connection
include("../connection.php");

$sqlmain = "SELECT * FROM patient WHERE pemail = ?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["booknow"])) {
        // Get appointment details from the form
        $apponum = $_POST["apponum"];
        $scheduleid = $_POST["scheduleid"];
        
        // Current date will be automatically handled by the database, so no need to include it in the SQL query.
        // Insert appointment using a prepared statement
        $sqlInsert = "INSERT INTO appointment (pid, apponum, scheduleid) VALUES (?, ?, ?)";
        $stmtInsert = $database->prepare($sqlInsert);
        
        if ($stmtInsert === false) {
            die("Error preparing the query: " . $database->error);
        }

        $stmtInsert->bind_param("iii", $userid, $apponum, $scheduleid);
        $stmtInsert->execute();
        
        // Check for errors during execution
        if ($stmtInsert->affected_rows > 0) {
            // Successfully booked the appointment, redirect to 'appointment.php'
            header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
            exit();
        } else {
            echo "Failed to book the appointment. Please try again.";
        }
        
        // Close the statement
        $stmtInsert->close();
    }
}

// Close the database connection
$database->close();
?>
