<?php 

require_once "utils/User.php";
require_once "utils/Validator.php";
include_once "utils/helpers.php";

use function Utils\helper;
use Utils\User;
use Utils\Validator as UserValidator;

$getDetails = require_once "data/users.php";

$validator = new UserValidator();


//$user=new Utils\User
//foreach for multiply data
    $userData = new User($getDetails['username'], $getDetails['email'], $getDetails['password']);

    echo $userData->getProfile() . "<br>";

    echo "Username: " . $validator->validateUsername($userData->username) . "<br>";
    echo "Email: " . $validator->validateEmail($userData->email) . "<br>";
    echo "Password: " . $validator->validatePassword($userData->password) . "<br>";

    helper();

?>