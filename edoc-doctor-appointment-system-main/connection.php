<?php

    $database= new mysqli("localhost","root","12345","edoc");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>