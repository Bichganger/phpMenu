<?php 
error_reporting(E_ALL);

$linkBase = mysqli_connect('localhost', 'root', '', 'phpMenu');
if ($linkBase->connect_error) {
    die("Connection failed: " . $linkBase->connect_error);
  }
  
  $linkBase->set_charset("utf8");
?>