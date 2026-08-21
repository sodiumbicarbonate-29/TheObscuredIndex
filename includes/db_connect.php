<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (getenv('DATABASE_URL')) {
    $url = parse_url(getenv('DATABASE_URL'));
    $host = $url['host'];
    $user = $url['user'];
    $pass = $url['pass'];
    $name = ltrim($url['path'], '/');
    $port = $url['port'] ?? 3306;
} elseif (getenv('MYSQLHOST') || getenv('MYSQL_HOST')) {
    $host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
    $user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER');
    $pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD');
    $name = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE');
    $port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;
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
