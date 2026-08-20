<?php
/**
 
 * This file handles database connection for both local development
 * and production environments.
 */

$host = $_SERVER['HTTP_HOST'] ?? '';
$is_localhost = (strpos($host, 'localhost') !== false) || 
                (strpos($host, '127.0.0.1') !== false) || 
                empty($host);

if ($is_localhost) {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "TheObscuredIndex";
} else {
    $db_host = "localhost"; 
    $db_user = "s22800098_TheObscuredIndex";
    $db_pass = "TheObscuredIndex";
    $db_name = "s22800098_TheObscuredIndex";
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>