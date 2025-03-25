<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $book_type = $_POST['book_type'];
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $reference_code = strtoupper(substr(md5(time()), 0, 8));

    $sql = "INSERT INTO orders (reference_code, title, author, isbn, book_type, customer_name, address, phone, email, facebook_link, instagram_link) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $reference_code, $title, $author, $isbn, $book_type, $customer_name, $address, $phone, $email, $facebook, $instagram);

    if ($stmt->execute()) {
        header("Location: index.php?ref=$reference_code");
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
}



if (isset($_GET['ref'])) {
    $reference_code = $_GET['ref'];
    $sql = "SELECT title, author, customer_name, address, status FROM orders WHERE reference_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reference_code);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        echo "<script>
            document.getElementById('responseContent').innerHTML = '<h2>Order Details</h2><p><b>Book:</b> " . $result['title'] . " by " . $result['author'] . "</p><p><b>Ordered by:</b> " . $result['customer_name'] . "</p><p><b>Delivery Address:</b> " . $result['address'] . "</p><p><b>Status:</b> " . $result['status'] . "</p>';
            document.getElementById('responseModal').style.display = 'flex';
        </script>";
    } else {
        echo "<script>
            document.getElementById('responseContent').innerHTML = '<p style=\"color:red;\">Order not found. Please check your reference code.</p>';
            document.getElementById('responseModal').style.display = 'flex';
        </script>";
    }
}
?>
