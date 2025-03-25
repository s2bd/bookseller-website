<?php
include 'config.php';

// Create PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if (isset($_GET['ref'])) {
    $refCode = $_GET['ref'];

    // Query both orders and completed_orders tables using UNION for fast searching
    $sql = "
        (SELECT title, author, customer_name, address, status FROM orders WHERE reference_code = :refCode)
        UNION
        (SELECT title, author, customer_name, address, status FROM completed_orders WHERE reference_code = :refCode)
    ";

    // Prepare the SQL statement
    $stmt = $pdo->prepare($sql);

    // Bind the reference code to the SQL statement
    $stmt->bindParam(':refCode', $refCode, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Return the order details as JSON if found
        echo json_encode([
            'success' => true,
            'order' => [
                'title' => $order['title'],
                'author' => $order['author'],
                'customer_name' => $order['customer_name'],
                'address' => $order['address'],
                'status' => $order['status']
            ]
        ]);
    } else {
        // Return an error message if the order is not found
        echo json_encode(['success' => false, 'message' => 'Order not found. Please check your reference code.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No reference code provided.']);
}

?>
