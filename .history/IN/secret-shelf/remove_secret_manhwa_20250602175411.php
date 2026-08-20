<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

$access_query = "SELECT * FROM Secret_Shelf_Access WHERE user_id = ?";
$access_stmt = mysqli_prepare($conn, $access_query);
mysqli_stmt_bind_param($access_stmt, "i", $user_id);
mysqli_stmt_execute($access_stmt);
$access_result = mysqli_stmt_get_result($access_stmt);

if (mysqli_num_rows($access_result) == 0) {
    header("Location: secret-shelf_login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $manhwa_id = (int)$_GET['id'];
    
    $delete_query = "DELETE FROM Secret_Manhwas WHERE user_id = ? AND manhwa_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $manhwa_id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        $message = "Manhwa removed from your secret shelf.";
    } else {
        $error = "Failed to remove manhwa from your secret shelf.";
    }
}

header("Location: secret-shelf.php" . (!empty($message) ? "?message=" . urlencode($message) : "") . (!empty($error) ? "?error=" . urlencode($error) : ""));
exit();
?>