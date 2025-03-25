<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password_hash FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['admin'] = $id;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error_message = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookseller Admin Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    .login-container {
        width: 300px;
        margin: 100px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .login-container h2 {
        margin-bottom: 20px;
        color: #ff9a9e;
    }

    .error {
        color: red;
        margin-bottom: 15px;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
    <footer class="sticky-footer">
        &copy; <b>Bookseller Template</b> by 
        <a href="https://dewanmukto.github.io/" target="_blank">Dewan Mukto</a>, MuxAI 2025
    </footer>
</body>
<script>
    document.body.addEventListener('mousemove', (event) => {document.body.style.setProperty('--mouse-x', `${event.clientX}px`);document.body.style.setProperty('--mouse-y', `${event.clientY}px`);});
</script>
</html>