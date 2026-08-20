<?php
/**
 * Database Connection
 * 
 * This file handles database connection for both local development
 * and production environments.
 */

// Detect if we're on localhost or production server
$host = $_SERVER['HTTP_HOST'] ?? '';
$is_localhost = (strpos($host, 'localhost') !== false) || 
                (strpos($host, '127.0.0.1') !== false) || 
                empty($host);

// Set connection parameters based on environment
if ($is_localhost) {
    // Local database connection
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "TheObscuredIndex";
} else {
    // Production database connection
    $db_host = "localhost"; // Usually remains localhost on shared hosting
    $db_user = "s22800098_TheObscuredIndex";
    $db_pass = "TheObscuredIndex";
    $db_name = "s22800098_TheObscuredIndex";
}

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set
mysqli_set_charset($conn, "utf8mb4");
?>