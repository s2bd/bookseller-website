<?php

$host = " sql313.infinityfree.com";
$username = "if0_37957070";
$password = "rjla9n7hRmNMzBY";
$dbname = "if0_37957070_bookseller_demo";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
