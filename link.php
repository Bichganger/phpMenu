<?php 
error_reporting(E_ALL);
$host = "localhost"; // Usually "localhost" or an IP address
$username = "root";
$password = "";
$database = "phpMenu";
$linkBase = new mysqli($host, $username, $password, $database);
if ($linkBase->connect_error) {
    //Instead of assigning the error message to `$link`, it should be handled separately.
    die("Connection failed: " . $linkBase->connect_error);
  }
  
  //Set character set
  $linkBase->set_charset("utf8");
?>