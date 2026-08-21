<?php
if (getenv('DATABASE_URL')) {
    $url = parse_url(getenv('DATABASE_URL'));
    $host = $url['host'];
    $user = $url['user'];
    $pass = $url['pass'];
    $name = ltrim($url['path'], '/');
    $port = $url['port'] ?? 3306;
} elseif (getenv('MYSQL_HOST')) {
    $host = getenv('MYSQL_HOST');
    $user = getenv('MYSQL_USER');
    $pass = getenv('MYSQL_PASSWORD');
    $name = getenv('MYSQL_DATABASE');
    $port = getenv('MYSQL_PORT') ?? 3306;
} else {
    // Local development
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $name = 'TheObscuredIndex';
    $port = 3306;
}

$conn = @mysqli_connect($host, $user, $pass, $name, $port);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    die("Database connection failed");
}

mysqli_set_charset($conn, "utf8mb4");
?>
