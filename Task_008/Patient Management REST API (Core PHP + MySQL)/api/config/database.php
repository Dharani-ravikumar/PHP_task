<?php 
$host="localhost";
$username="root";
$password="";
$database="a_hospital_db";
$conn= mysqli_connect($host,$username,$password,$database);
if(!$conn)
    {
        die("connection failed");
    }
?>