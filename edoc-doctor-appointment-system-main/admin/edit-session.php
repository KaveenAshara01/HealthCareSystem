<?php
include("../connection.php");

if ($_POST) {
    $scheduleid = $_POST["scheduleid"];
    $title = $_POST["title"];
    $docid = $_POST["docid"];
    $venueid = $_POST["venueid"];
    $nop = $_POST["nop"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $price = $_POST["price"];

    $sql = "UPDATE schedule 
            SET title = ?, docid = ?, venue_id = ?, nop = ?, scheduledate = ?, scheduletime = ?, price = ?
            WHERE scheduleid = ?";
    
    $stmt = $database->prepare($sql);
    $stmt->bind_param("siisssdi", $title, $docid, $venueid, $nop, $date, $time, $price, $scheduleid);
    
    if ($stmt->execute()) {
        header("Location: schedule.php?action=session-updated&title=$title");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
