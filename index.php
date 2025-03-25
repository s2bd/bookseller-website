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

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- HTML from Bookseller Template by Dewan Mukto, MuxAI 2025-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Lora:wght@400;600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="brand">
            <!-- Placeholder logo image; replace with your real logo if you have one -->
            <img src="asset/logo.png" alt="Logo" class="logo">
            <span class="site-title">Bookseller</span>
        </div>
        <ul>
            <li><a href="#about">About</a></li>
            <li class="dropdown">
                <a href="javascript:void(0);">Updates <i class="fas fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="https://www.facebook.com/profile.php?id=100095097050083" target="_blank">Facebook <i class="fas fa-external-link-alt"></i></a></li>
                    <li><a href="https://www.instagram.com/bookon_life/" target="_blank">Instagram <i class="fas fa-external-link-alt"></i></a></li>
                </ul>
            </li>
            <li><a href="javascript:void(0);" id="orderNowBtn" class="pill-btn">Order Now</a></li>
            <li><a href="javascript:void(0);" id="orderStatusBtn" class="pill-btn">Order Status</a></li>
            <li><a href="javascript:void(0);" id="submitReviewBtn">Submit Review</a></li>
        </ul>
    </nav>

    <header>
        <div class="cta-phrase">
            <span class="big-quote">“</span>
            <h2>READ YOUR HEART OUT!</h2>
            <span class="big-quote">”</span>
        </div>
        <div class="glass-cta-container">
        <div class="cta-buttons">
            <a href="javascript:void(0);" class="btn" id="orderNowBtn">Order Now</a>
            <a href="javascript:void(0);" class="btn"><i class="fas fa-book-open"></i> See Collections</a>
        </div></div>
        <div class="floating-books-container" id="floatingBooks"></div>
    </header>

    <section id="about">
        <div class="about-container">
            <div class="carousel">
                <img src="asset/about/image1.png" alt="Book Image 1">
                <img src="asset/about/image2.png" alt="Book Image 2">
                <img src="asset/about/image3.jpg" alt="Book Image 3">
            </div>
            <div class="about-text">
                <h2>About Us</h2>
                <p><b>Bookseller</b> is dedicated to deliver literary masterpieces to every household, guaranteed to inspire and welcome hearts and minds to realize the beauty of words, thoughts and fictional landscapes. Established since 2020, Bookseller Co. operates in 27 cities across 9 different countries in the hopes of enlightening every individual on the planet.</p>
                <p>Founded by Maksudul Islam, Bookseller is home to 4 amazing souls who work day and night to produce and provide the best quality of books possible.</p>
            </div>
        </div>
    </section>

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

    <footer class="sticky-footer">
        &copy; <b>Bookseller Template</b> by 
        <a href="https://dewanmukto.github.io/" target="_blank">Dewan Mukto</a>, MuxAI 2025
    </footer>

    <a href="https://m.me/DewanMukto" class="messenger-btn" target="_blank">
        <i class="fab fa-facebook-messenger"></i>
    </a>

    <script src="script.js"></script>
    <div id="responseModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeResponseModal">&times;</span>
        <div id="responseContent"></div>
    </div>
</div>
</body>
</html>
