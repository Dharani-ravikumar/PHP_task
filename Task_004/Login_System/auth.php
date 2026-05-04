<?php
session_start();
require "includes/validation.php";


$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);


$usernameErr = validateUsername($username);
$emailErr = validateEmail($email);
$passwordErr = validatePassword($password);

if ($usernameErr || $emailErr || $passwordErr) {

    $_SESSION['errors'] = [
        'username' => $usernameErr,
        'email' => $emailErr,
        'password' => $passwordErr
    ];

    $_SESSION['old'] = [
        'username' => $username,
        'email' => $email
    ];

    header("Location: index.php");
    exit();
}


$users = [
    ["id"=>1,"username"=>"user1","email"=>"user1@example.com","password"=>"Admin@123"],
    ["id"=>2,"username"=>"user2","email"=>"user2@example.com","password"=>"Admin@123"],
    ["id"=>3,"username"=>"user3","email"=>"user3@example.com","password"=>"Admin@123"]
];


$foundUser = null;

foreach ($users as $user) {
    if (
        $user['username'] === $username &&
        $user['email'] === $email &&
        $user['password'] === $password
    ) {
        $foundUser = $user;
        break;
    }
}


if (!$foundUser) {
    $_SESSION['error'] = "Invalid login details.";
    header("Location: index.php");
    exit();
}


if ($foundUser['username'] === "user1") {
    $theme = "bg-dark text-white";
} elseif ($foundUser['username'] === "user2") {
    $theme = "bg-warning";
} else {
    $theme = "bg-light";
}


$_SESSION['user_id'] = $foundUser['id'];
$_SESSION['username'] = $foundUser['username'];
$_SESSION['email'] = $foundUser['email'];
$_SESSION['theme'] = $theme;


$expiry = time() + 3600;

if ($remember) {
    setcookie("remember_username", $username, $expiry, "/");
} else {
    setcookie("remember_username", "", time() - 3600, "/");
}


setcookie("user_theme", $theme, $expiry, "/");


header("Location: dashboard.php");
exit();
?>