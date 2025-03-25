CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_code VARCHAR(10) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20),
    book_type ENUM('hardback', 'paperback') NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    facebook_link VARCHAR(255),
    instagram_link VARCHAR(255),
    status ENUM('unpaid', 'paid', 'on the way', 'delivered') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);
CREATE TABLE completed_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_code VARCHAR(10) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20),
    book_type ENUM('hardback', 'paperback') NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    facebook_link VARCHAR(255),
    instagram_link VARCHAR(255),
    status ENUM('unpaid', 'paid', 'on the way', 'delivered') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);