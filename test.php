<?php
// Simple test file to check database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

try {
    // Database credentials
    $host = 'localhost';
    $dbname = 's22800098_db_theosis';
    $username = 's22800098_db_theosis';
    $password = 'manondo22800098';
    
    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p style='color:green'>Connected successfully to database!</p>";
    echo "<p>Server info: " . $conn->server_info . "</p>";
    echo "<p>Host info: " . $conn->host_info . "</p>";
    
    // Close connection
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Show server information
echo "<h2>Server Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Server Address: " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
?>