<?php
    ob_start();
    
    //enable session
    session_start();
    
    $timezone = date_default_timezone_set("America/Toronto");

    $con = mysqli_connect("localhost", "root", "root", "lou", 8889);
    if(mysqli_connect_errno()) {
        echo "Failed to connect: " . mysqli_connect_errno();
    }
    
?>