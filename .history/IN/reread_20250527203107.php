<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $manhwa_id = (int)$_GET['id'];
    
    $query = "SELECT reading_link FROM Manhwas WHERE manhwa_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $manhwa_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $reading_link = $row['reading_link'];
        $current_date = date('Y-m-d');
        
        // Use direct SQL to avoid data truncation
        $update_query = "UPDATE User_Reading_Status SET 
                        reading_status = 'Currently Reading', 
                        start_reading_date = '$current_date', 
                        finish_reading_date = NULL,
                        last_updated = NOW() 
                        WHERE user_id = $user_id AND manhwa_id = $manhwa_id";
        mysqli_query($conn, $update_query);
        
        if (!empty($reading_link)) {
            echo "<script>
                window.open('$reading_link', '_blank');
                window.location.href = 'library.php';
            </script>";
            exit();
        } else {
            header("Location: view_manhwa.php?id=$manhwa_id");
            exit();
        }
    } else {
        header("Location: library.php");
        exit();
    }
} else {
    header("Location: library.php");
    exit();
}
?>