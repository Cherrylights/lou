<?php
    if(isset($_POST['loginButton'])) {
        //Login button was pressed

        $loginUsername = $_POST['loginUsername'];
        $loginPassword = $_POST['loginPassword'];

        $wasSuccessful = $account->login($loginUsername, $loginPassword);
        if($wasSuccessful) {
            $_SESSION['userLoggedIn'] = $loginUsername;
            header("Location: index.php");
        }
    }
?>