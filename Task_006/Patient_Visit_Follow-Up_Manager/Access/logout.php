<?php
session_start();

/* ================= DESTROY SESSION ================= */

$_SESSION = [];   // clear all session variables

session_unset();  // remove session data

session_destroy(); // destroy session

/* ================= REDIRECT TO LOGIN ================= */

header("Location: ../index.php"); 
exit;
?> 