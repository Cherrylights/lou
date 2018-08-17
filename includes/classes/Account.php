<?php
    class Account {

        private $con;
        private $errorArray;

        public function __construct($con) {
            //create a empty array for error messages
            $this->errorArray = array();

            $this->con = $con;    
        }

        public function register($username, $firstName, $lastName, $email, $email2, $password, $password2) {

            // do the validation for user's input
            $this->validateUsername($username);
            $this->validateFirstName($firstName);
            $this->validateLastName($lastName);
            $this->validateEmails($email, $email2);
            $this->validatePasswords($password, $password2);

            // if no errors, insert user info into database
            if(empty($this->errorArray)) {
                return $this->insertUserDetails($username, $firstName, $lastName, $email, $password);
            } else {
                return false;
            }
        }

        public function login($loginUsername, $loginPassword) {
            // encrypt password to match the password in the db
            $loginPassword = md5($loginPassword);
            // check if the user info can be found in the db
            $query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$loginUsername' AND password='$loginPassword'");
            if(mysqli_num_rows($query) == 1) {
                return true;
            } else {
                array_push($this->errorArray, Constants::$loginFailed);
                return false;
            }
        }

        public function getError($error) {
            if(!in_array($error, $this->errorArray)) {
                $error="";
            }
            return "<span class='errorMessage'>$error</span>";
        }

        private function insertUserDetails($username, $firstName, $lastName, $email, $password) {
            $encryptedPw = md5($password);
            $profilePic = "assets/images/profile-pics/head_emerald.png";
            $date = date("Y-m-d");

            $result = mysqli_query($this->con, "INSERT INTO users VALUE ('', '$username', '$firstName', '$lastName', '$email', '$encryptedPw', '$date', '$profilePic')");

            return $result;
        }

        private function validateUsername($username) {
            if(strlen($username) > 25 || strlen($username) < 5) {
                array_push($this->errorArray, Constants::$usernameCharacters);
                return;
            }

            // Check if username exists:
            $checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$username'");
            if(mysqli_num_rows($checkUsernameQuery) != 0) {
                array_push($this->errorArray, Constants::$usernameTaken);
                return;
            }
        }
    
        private function validateFirstName($firstName) {
            if(strlen($firstName) > 25 || strlen($firstName) < 2) {
                array_push($this->errorArray, Constants::$firstNameCharacters);
                return;
            }
        }
    
        private function validateLastName($lastName) {
            if(strlen($lastName) > 25 || strlen($lastName) < 2) {
                array_push($this->errorArray, Constants::$lastNameCharacters);
                return;
            }
        }
    
        private function validateEmails($email, $email2) {
            if($email != $email2) {
                array_push($this->errorArray, Constants::$emailsDoNotMatch);
                return;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($this->errorArray, Constants::$emailInvalid);
                return;
            }

            // Check if email already used.
            $checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$email'");
            if(mysqli_num_rows($checkEmailQuery) != 0) {
                array_push($this->errorArray, Constants::$emailTaken);
                return;
            }
        }
    
        private function validatePasswords($password, $password2) {
            if($password != $password2) {
                array_push($this->errorArray, Constants::$passwordsDoNotMatch);
                return;
            }

            if(preg_match('/[^A-Za-z0-9]/', $password)) {
                array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
                return;
            }

            if(strlen($password) > 30 || strlen($password) < 5) {
                array_push($this->errorArray, Constants::$passwordCharacters);
                return;
            }
        }
    }
?>