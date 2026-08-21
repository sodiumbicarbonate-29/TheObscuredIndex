<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (getenv('RAILWAY_ENVIRONMENT')) {
    $host = 'mysql.railway.internal';
    $user = 'root';
    $pass = 'gENQVThgxoVhLjxlluFgLetrccgOsNwu';
    $name = 'railway';
    $port = 3306;
} else {
    // Local development
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $name = 'theobscuredindex';
    $port = 3306;
}

$conn = @mysqli_connect($host, $user, $pass, $name, $port);

if (!$conn) {
    die("DB Error: " . mysqli_connect_error() . " | Host: $host | User: $user | DB: $name | Port: $port");
}

mysqli_set_charset($conn, "utf8mb4");
?>
