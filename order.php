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
?>

<script>
function copyRef() {
    var copyText = document.getElementById("refCode").innerText;
    navigator.clipboard.writeText(copyText);
    document.getElementById("copyIcon").classList.remove('fa-copy');
    document.getElementById("copyIcon").classList.add('fa-check');
}
</script>


<form method="POST">
    <input type="text" name="title" placeholder="Book Title" required>
    <input type="text" name="author" placeholder="Author" required>
    <input type="text" name="isbn" placeholder="ISBN">
    <select name="book_type" required>
        <option value="hardback">Hardback</option>
        <option value="paperback">Paperback</option>
    </select>
    <input type="text" name="customer_name" placeholder="Your Name" required>
    <textarea name="address" placeholder="Address" required></textarea>
    <input type="text" name="phone" placeholder="Phone Number" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="url" name="facebook" placeholder="Facebook Profile Link">
    <input type="url" name="instagram" placeholder="Instagram Profile Link">
    <button type="submit">Submit Order</button>
</form>
