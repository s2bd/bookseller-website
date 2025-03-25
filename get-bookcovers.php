<?php
$directory = "asset/bookcovers/";
$images = array_values(array_diff(scandir($directory), array('..', '.')));
header('Content-Type: application/json');
echo json_encode($images);
?>
