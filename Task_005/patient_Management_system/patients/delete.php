<?php

include("../config/db.php");

$id = $_GET['id'];

// Delete Query
$sql = "DELETE FROM patients WHERE id=$id";

if ($conn->query($sql) === TRUE) {

   session_start();

$_SESSION['success'] = "Data deleted successfully!";
header("Location: list.php");
exit();

} else {

    echo "Error: " . $conn->error;
}

?>