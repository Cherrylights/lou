<?php
    include('./includes/config.php');

    //log out session
    //session_destroy();
    
    if(isset($_SESSION['userLoggedIn'])) {
       $userLoggedIn = $_SESSION['userLoggedIn'];
    } else {
        header("Location: register.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    Hello! Lou.
</body>
</html>