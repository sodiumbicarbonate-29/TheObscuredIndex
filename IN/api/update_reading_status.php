<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// Check if required parameters are provided
if (!isset($_POST['manhwa_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$manhwa_id = (int)$_POST['manhwa_id'];
$current_date = date('Y-m-d');

// Check if this is a reread action
if (isset($_POST['reading_status']) && $_POST['reading_status'] == 'Reread') {
    // Get the reading link
    $link_query = "SELECT reading_link FROM Manhwas WHERE manhwa_id = ?";
    $link_stmt = mysqli_prepare($conn, $link_query);
    mysqli_stmt_bind_param($link_stmt, "i", $manhwa_id);
    mysqli_stmt_execute($link_stmt);
    $link_result = mysqli_stmt_get_result($link_stmt);
    $manhwa = mysqli_fetch_assoc($link_result);
    
    // Update reading status to Currently Reading
    $update_query = "UPDATE User_Reading_Status SET 
                    reading_status = 'Currently Reading', 
                    start_reading_date = ?, 
                    finish_reading_date = NULL,
                    last_updated = NOW() 
                    WHERE user_id = ? AND manhwa_id = ?";
    
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "sii", $current_date, $user_id, $manhwa_id);
    $result = mysqli_stmt_execute($stmt);
    
    // Insert into Reread_History table
    $insert_reread_query = "INSERT INTO Reread_History (user_id, manhwa_id, start_date) VALUES (?, ?, ?)";
    $insert_reread_stmt = mysqli_prepare($conn, $insert_reread_query);
    mysqli_stmt_bind_param($insert_reread_stmt, "iis", $user_id, $manhwa_id, $current_date);
    mysqli_stmt_execute($insert_reread_stmt);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'reading_link' => $manhwa['reading_link'] ?? '']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
} else {
    // Regular update for other reading statuses
    $update_query = "UPDATE User_Reading_Status SET 
                    reading_status = ?, 
                    start_reading_date = ?, 
                    last_updated = NOW() 
                    WHERE user_id = ? AND manhwa_id = ?";
    
    $reading_status = $_POST['reading_status'] ?? 'Plan to Read';
    
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssii", $reading_status, $current_date, $user_id, $manhwa_id);
    $result = mysqli_stmt_execute($stmt);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}
?>