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
        echo "<div id='orderSuccess'>Order submitted! Your reference code: 
              <b id='refCode'>$reference_code</b> 
              <i id='copyIcon' class='fas fa-copy' onclick='copyRef()'></i></div>";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="javascript:void(0);" id="orderNowBtn">Order Now</a></li>
            <li><a href="javascript:void(0);" id="orderStatusBtn">Order Status</a></li>
        </ul>
    </nav>

    <header>
        <h1>Read Your Heart Out!</h1>
        <div class="cta-buttons">
            <a href="javascript:void(0);" class="btn" id="orderNowBtn">Order Now</a>
            <a href="javascript:void(0);" class="btn"><i class="fas fa-book-open"></i> See Collections</a>
        </div>
    </header>

    <!-- Pop-up modals -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeOrderModal">&times;</span>
            <h2>Place Your Order</h2>
            <form id="orderForm" method="POST">
                <input type="text" name="title" placeholder="What's the book's title?" required>
                <input type="text" name="author" placeholder="Who's the book's author?" required>
                <input type="text" name="isbn" placeholder="Do you know the book's ISBN?">
                <select name="book_type" required>
                    <option value="paperback">Paperback</option>
                    <option value="hardback">Hardback</option>
                </select>
                <input type="text" name="customer_name" placeholder="What is your name?" required>
                <textarea name="address" placeholder="Where do you want it delivered?" required></textarea>
                <input type="text" name="phone" placeholder="Enter a phone number" required>
                <input type="email" name="email" placeholder="Enter an email address" required>
                <input type="url" name="facebook" placeholder="Your Facebook profile link">
                <input type="url" name="instagram" placeholder="Your Instagram profile link">
                <button type="submit">Submit Order</button>
            </form>
        </div>
    </div>

    <script>
    function copyRef() {
        var copyText = document.getElementById("refCode").innerText;
        navigator.clipboard.writeText(copyText);
        document.getElementById("copyIcon").classList.remove('fa-copy');
        document.getElementById("copyIcon").classList.add('fa-check');
    }
    </script>

    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeStatusModal">&times;</span>
            <h2>Check Order Status</h2>
            <form id="statusForm" method="GET">
                <input type="text" name="ref" placeholder="Enter Reference Code" required>
                <button type="submit">Check Status</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
