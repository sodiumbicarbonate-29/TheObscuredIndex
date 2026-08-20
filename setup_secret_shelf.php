<?php
// This script sets up the necessary database tables for the Secret Shelf feature
require_once 'includes/db_connect.php';

// Create Secret_Shelf_Access table
$create_access_table = "CREATE TABLE IF NOT EXISTS Secret_Shelf_Access (
    access_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    granted_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $create_access_table)) {
    echo "Secret_Shelf_Access table created successfully.<br>";
} else {
    echo "Error creating Secret_Shelf_Access table: " . mysqli_error($conn) . "<br>";
}

// Create Secret_Manhwas table
$create_secret_manhwas_table = "CREATE TABLE IF NOT EXISTS Secret_Manhwas (
    secret_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    manhwa_id INT NOT NULL,
    added_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (manhwa_id) REFERENCES Manhwas(manhwa_id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $create_secret_manhwas_table)) {
    echo "Secret_Manhwas table created successfully.<br>";
} else {
    echo "Error creating Secret_Manhwas table: " . mysqli_error($conn) . "<br>";
}

echo "<p>Setup complete. You can now use the Secret Shelf feature.</p>";
echo "<p><a href='IN/home.php'>Return to homepage</a></p>";
?>