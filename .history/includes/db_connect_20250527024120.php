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
$config = $db_config[$environment];

$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
