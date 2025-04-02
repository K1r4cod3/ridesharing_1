<?php
    $host = "localhost"; 
    $user = "root";      
    $pass = "";           
    $dbname = "ridesharing";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connect failed: " . $conn->connect_error);
    }
?> 