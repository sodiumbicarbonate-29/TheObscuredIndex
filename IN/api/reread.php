<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: library.php");
    exit();
}

$manhwa_id = (int)$_GET['id'];

$query = "SELECT m.*, urs.reading_status 
          FROM Manhwas m 
          LEFT JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ? 
          WHERE m.manhwa_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $manhwa_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: library.php");
    exit();
}

$manhwa = mysqli_fetch_assoc($result);

$current_date = date('Y-m-d');
$update_query = "UPDATE User_Reading_Status 
                SET reading_status = 'Reread', 
                    start_reading_date = ?, 
                    finish_reading_date = NULL, 
                    last_updated = NOW() 
                WHERE user_id = ? AND manhwa_id = ?";
$update_stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($update_stmt, "sii", $current_date, $user_id, $manhwa_id);
mysqli_stmt_execute($update_stmt);

header('Location: library.php');
exit();} else {
    header("Location: library.php");
    exit();
}
?>