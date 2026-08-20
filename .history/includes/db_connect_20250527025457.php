<?php
$db_config = [
    'local' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'TheObscuredIndex'
    ],
    'online' => [
        'host' => 'localhost',
        'user' => 's22800098_TheObscuredIndex',
        'pass' => 'TheObscuredIndex',
        'name' => 's22800098_TheObscuredIndex'
    ]
];

$environment = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') ? 'local' : 'online';

error_log("Server Name: " . $_SERVER['SERVER_NAME']);
error_log("Server Address: " . $_SERVER['SERVER_ADDR']);
error_log("Using environment: " . $environment);

$config = $db_config[$environment];

try {
    $conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);
    
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    
    echo "<div style='color:red; padding:20px; margin:20px; border:1px solid red; background:#fff;'>";
    echo "<h3>Connection Error</h3>";
    echo "<p>We're experiencing technical difficulties connecting to the database. Please try again later.</p>";
    echo "<p>Error details (for admin): " . $e->getMessage() . "</p>";
    echo "</div>";
    exit;
}
?>
