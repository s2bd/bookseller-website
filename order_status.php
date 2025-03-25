<?php
include 'config.php';

if (isset($_GET['ref'])) {
    $reference_code = $_GET['ref'];
    $sql = "SELECT title, author, customer_name, address, status FROM orders WHERE reference_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reference_code);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        echo "<h2>Order Details</h2>";
        echo "<p><b>Book:</b> " . $result['title'] . " by " . $result['author'] . "</p>";
        echo "<p><b>Ordered by:</b> " . $result['customer_name'] . "</p>";
        echo "<p><b>Delivery Address:</b> " . $result['address'] . "</p>";
        echo "<p><b>Status:</b> " . $result['status'] . "</p>";
    } else {
        echo "<p style='color:red;'>Order not found. Please check your reference code.</p>";
    }
}
?>

<form method="GET">
    <input type="text" name="ref" placeholder="Enter Reference Code" required>
    <button type="submit">Check Status</button>
</form>
