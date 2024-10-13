<?php

    $database= new mysqli("localhost","root","achintha2002","edoc");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>