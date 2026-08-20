<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : 'all';
$reading_status_filter = isset($_GET['reading_status']) ? $_GET['reading_status'] : 'all';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'title_asc';

$query = "SELECT m.*, urs.reading_status, urs.start_reading_date, urs.finish_reading_date 
          FROM Manhwas m 
          LEFT JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = $user_id 
          WHERE 1=1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (title LIKE '%$search%' OR author LIKE '%$search%')";
}

if ($status_filter != 'all') {
    $status_filter = mysqli_real_escape_string($conn, $status_filter);
    $query .= " AND status = '$status_filter'";
}

if ($genre_filter != 'all') {
    $genre_filter = mysqli_real_escape_string($conn, $genre_filter);
    $query .= " AND genre = '$genre_filter'";
}

if ($reading_status_filter != 'all') {
    $reading_status_filter = mysqli_real_escape_string($conn, $reading_status_filter);
    $query .= " AND urs.reading_status = '$reading_status_filter'";
}

switch ($sort_by) {
    case 'title_asc':
        $query .= " ORDER BY title ASC";
        break;
    case 'title_desc':
        $query .= " ORDER BY title DESC";
        break;
    case 'author_asc':
        $query .= " ORDER BY author ASC";
        break;
    case 'author_desc':
        $query .= " ORDER BY author DESC";
        break;
    case 'recent':
        $query .= " ORDER BY upload_date DESC";
        break;
    default:
        $query .= " ORDER BY title ASC";
}

$result = mysqli_query($conn, $query);
$manhwas = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($manhwa = mysqli_fetch_assoc($result)) {
        $manhwas[] = $manhwa;
    }
    echo json_encode(['success' => true, 'manhwas' => $manhwas]);
} else {
    echo json_encode(['success' => true, 'manhwas' => []]);
}
?>