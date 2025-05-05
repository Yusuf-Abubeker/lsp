<?php
/**
 * Database setup script
 * Run this script once to create the database and tables
 */

// Create the database if it doesn't exist
$conn = new PDO("mysql:host=localhost", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $conn->exec("CREATE DATABASE IF NOT EXISTS service_finder");
    echo "Database created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating database: " . $e->getMessage() . "<br>";
}

// Connect to the database
require_once 'database.php';
$database = new Database();
$db = $database->getConnection();

// Create users table
try {
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role ENUM('customer', 'provider', 'admin') NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        profile_image VARCHAR(255) DEFAULT 'default-avatar.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($query);
    echo "Users table created successfully<br>";

    // Create an admin user
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $query = "INSERT INTO users (role, name, email, password) 
              VALUES ('admin', 'Admin User', 'admin@example.com', :password)
              ON DUPLICATE KEY UPDATE id=id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $password_hash);
    $stmt->execute();
    echo "Admin user created<br>";
} catch(PDOException $e) {
    echo "Error creating users table: " . $e->getMessage() . "<br>";
}

// Create providers table
try {
    $query = "CREATE TABLE IF NOT EXISTS providers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        bio TEXT,
        address VARCHAR(255),
        city VARCHAR(100),
        state VARCHAR(100),
        zip_code VARCHAR(20),
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        is_verified BOOLEAN DEFAULT false,
        avg_rating DECIMAL(3,2) DEFAULT 0,
        total_reviews INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($query);
    echo "Providers table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating providers table: " . $e->getMessage() . "<br>";
}

// Create categories table
try {
    $query = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        icon VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($query);
    echo "Categories table created successfully<br>";

    // Insert some default categories
    $categories = [
        ['Cleaning', 'House cleaning, office cleaning, and more', 'broom'],
        ['Plumbing', 'Pipe repairs, installations, and more', 'wrench'],
        ['Electrical', 'Wiring, repairs, installations', 'bolt'],
        ['Tutoring', 'Academic help and tutoring', 'book'],
        ['Landscaping', 'Garden maintenance, lawn care', 'leaf'],
        ['Moving', 'Help with moving houses or offices', 'truck'],
        ['IT Support', 'Computer repairs, software help', 'computer'],
        ['Beauty', 'Haircuts, makeup, manicures', 'scissors']
    ];

    $query = "INSERT INTO categories (name, description, icon) 
              VALUES (:name, :description, :icon)";
    $stmt = $db->prepare($query);
    
    foreach ($categories as $category) {
        $stmt->bindParam(':name', $category[0]);
        $stmt->bindParam(':description', $category[1]);
        $stmt->bindParam(':icon', $category[2]);
        $stmt->execute();
    }
    
    echo "Default categories created<br>";
} catch(PDOException $e) {
    echo "Error creating categories table: " . $e->getMessage() . "<br>";
}

// Create services table
try {
    $query = "CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        provider_id INT NOT NULL,
        category_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2),
        price_type ENUM('hourly', 'fixed', 'starting_at') DEFAULT 'hourly',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )";
    $db->exec($query);
    echo "Services table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating services table: " . $e->getMessage() . "<br>";
}

// Create availability table
try {
    $query = "CREATE TABLE IF NOT EXISTS availability (
        id INT AUTO_INCREMENT PRIMARY KEY,
        provider_id INT NOT NULL,
        day_of_week TINYINT NOT NULL COMMENT '0=Sunday, 1=Monday, etc',
        start_time TIME,
        end_time TIME,
        is_available BOOLEAN DEFAULT true,
        FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE
    )";
    $db->exec($query);
    echo "Availability table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating availability table: " . $e->getMessage() . "<br>";
}

// Create bookings table
try {
    $query = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        service_id INT NOT NULL,
        customer_id INT NOT NULL,
        booking_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        total_price DECIMAL(10,2),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (service_id) REFERENCES services(id),
        FOREIGN KEY (customer_id) REFERENCES users(id)
    )";
    $db->exec($query);
    echo "Bookings table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating bookings table: " . $e->getMessage() . "<br>";
}

// Create reviews table
try {
    $query = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        service_id INT NOT NULL,
        customer_id INT NOT NULL,
        provider_id INT NOT NULL,
        rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id),
        FOREIGN KEY (service_id) REFERENCES services(id),
        FOREIGN KEY (customer_id) REFERENCES users(id),
        FOREIGN KEY (provider_id) REFERENCES providers(id)
    )";
    $db->exec($query);
    echo "Reviews table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating reviews table: " . $e->getMessage() . "<br>";
}

echo "<br>Database setup completed!";
?>