<?php
$server = "134.65.245.136";
$username = "root";
$password = "Sk0vr0n5k!";
$dbname = "azuredovps";
// Create connection
try{
   $conn = new PDO("mysql:host=$server;dbname=$dbname","$username","$password");
   $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
   die('Unable to connect with the database');
}