<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference_code = $_POST['reference_code'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE reference_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $reference_code);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
?>
