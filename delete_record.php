<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference_code = $_POST['reference_code'];
    $type = $_POST['type'];

    if ($type === 'order') {
        $delete_sql = "DELETE FROM orders WHERE reference_code = ?";
    } else {
        $delete_sql = "DELETE FROM completed_orders WHERE reference_code = ?";
    }

    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param('s', $reference_code);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}
?>
