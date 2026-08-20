<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'title_asc';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : 'all';
$reading_status_filter = isset($_GET['reading_status']) ? $_GET['reading_status'] : 'all';

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

$total_query = "SELECT COUNT(*) as total FROM Manhwas";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_manhwas = $total_row['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - My Manhwa Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --light-color: #f5f6fa;
            --accent-color: #fd79a8;
            --success-color: #00b894;
            --warning-color: #fdcb6e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
        }
        
        .library-stats {
            background-color: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
        }
        
        .library-stats strong {
            color: var(--primary-color);
        }
        
        .filters-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .search-bar {
            display: flex;
            margin-bottom: 15px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px 0 0 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-bar input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .search-bar button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .search-bar button:hover {
            background-color: var(--secondary-color);
        }
        
        .filter-options {
            display: flex;
            flex-wrap: nowrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .desktop-filter-row {
            display: flex;
            gap: 15px;
            width: 100%;
        }
        
        .add-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
            position: relative;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background-color: white;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236c5ce7' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        
        .filter-group select:hover {
            border-color: var(--secondary-color);
        }
        
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }
        
        .filter-tag {
            display: inline-flex;
            align-items: center;
            background-color: rgba(108, 92, 231, 0.1);
            color: var(--primary-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .filter-tag i {
            margin-left: 5px;
            cursor: pointer;
        }
        
        .manhwa-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }
        
        .manhwa-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .manhwa-list-item {
            display: flex;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .manhwa-list-item:hover {
            transform: translateY(-3px);
        }
        
        .manhwa-list-item .manhwa-cover {
            width: 120px;
            height: 180px;
            flex-shrink: 0;
        }
        
        .manhwa-list-item .manhwa-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .manhwa-list-item .manhwa-title {
            font-size: 1.1rem;
            height: auto;
        }
        
        .manhwa-description {
            font-size: 0.75rem;
            color: #666;
            margin: 5px 0;
            line-height: 1.4;
        }
        
        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .view-btn {
            background-color: var(--light-color);
            color: var(--text-color);
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .view-btn:hover {
            background-color: #e0e0e0;
        }
        
        .read-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .read-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .view-btn {
            background-color: var(--light-color);
            color: var(--text-color);
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .view-btn.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .manhwa-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .manhwa-card:hover {
            transform: translateY(-5px);
        }
        
        .manhwa-cover {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .manhwa-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .manhwa-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .status-label {
            font-size: 0.7rem;
            color: var(--text-color);
            font-weight: 600;
        }
        
        .status-Ongoing {
            background-color: var(--primary-color);
        }
        
        .status-Completed {
            background-color: var(--success-color);
        }
        
        .status-Dropped {
            background-color: var(--accent-color);
        }
        
        .status-Hiatus {
            background-color: var(--warning-color);
        }
        
        .manhwa-info {
            padding: 10px;
        }
        
        .manhwa-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 3px;
            color: var(--text-color);
            overflow: visible;
            min-height: 2.2em;
            word-wrap: break-word;
        }
        
        .manhwa-author {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 3px;
        }
        
        .manhwa-genre {
            font-size: 0.75rem;
            color: var(--accent-color);
            margin-bottom: 5px;
            font-style: italic;
        }
        
        .genre-dates-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .reading-status-select {
            padding: 6px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 0.75rem;
            background-color: white;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            margin: 5px 0;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236c5ce7' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 0.8em;
        }
        
        .reading-status-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        
        .reading-status-select option {
            padding: 10px;
        }
        
        .reading-dates {
            font-size: 0.7rem;
            color: #666;
            margin-top: 5px;
            background-color: rgba(108, 92, 231, 0.05);
            border-radius: 4px;
            padding: 5px 8px;
        }
        
        .reading-dates.list-inline {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 0;
            background: none;
            padding: 0;
            font-size: 0.7rem;
            line-height: 1.4;
        }
        
        .genre-dates-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .manhwa-genre {
            margin-bottom: 0;
            line-height: 1.5;
            position: relative;
            top: 3px;
        }
        
        .reading-dates span {
            display: block;
            margin-bottom: 4px;
            position: relative;
            padding-left: 18px;
        }
        
        .reading-dates.list-inline span {
            padding-left: 18px;
            margin-bottom: 0;
            display: inline-flex;
            align-items: center;
        }
        
        .reading-dates span:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--primary-color);
            opacity: 0.7;
        }
        
        .reading-dates span:last-child:before {
            background-color: var(--success-color);
        }
        
        .manhwa-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .manhwa-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
        }
        
        .manhwa-actions {
            display: flex;
            gap: 8px;
            margin-top: 5px;
        }
        
        .manhwa-actions a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .read-btn {
            background-color: var(--success-color);
            color: white !important;
            padding: 3px 8px;
            border-radius: 4px;
        }
        
        .manhwa-actions a:hover {
            text-decoration: underline;
        }
        
        .read-btn {
            background-color: var(--success-color);
            color: white !important;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }
        
        .read-btn:hover {
            background-color: #00a382;
            transform: translateY(-2px);
            text-decoration: none !important;
        }
        
        .reread-btn {
            background-color: var(--accent-color);
            color: white !important;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            margin-left: 5px;
        }
        
        .reread-btn:hover {
            background-color: #e84393;
            transform: translateY(-2px);
            text-decoration: none !important;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .empty-state h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .add-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .add-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        
        @media (max-width: 768px) {
            main {
                padding: 15px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .library-stats {
                align-self: flex-start;
            }
            
            .filter-options {
                flex-direction: column;
            }
            
            .desktop-filter-row {
                flex-wrap: wrap;
            }
            
            .mobile-filter-row {
                display: flex;
                gap: 8px;
                margin-bottom: 8px;
            }
            
            .filter-group {
                flex: 1;
                min-width: 0;
            }
            
            .filter-group label {
                font-size: 0.75rem;
                margin-bottom: 4px;
            }
            
            .filter-group select {
                padding: 8px 10px;
                font-size: 0.8rem;
                background-position: right 8px center;
                background-size: 0.9em;
            }
            
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
            
            .manhwa-cover {
                height: 180px;
            }
            
            .manhwa-status {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
            
            .filters-container {
                padding: 15px;
            }
            
            .view-toggle {
                flex: 1;
            }
            
            .add-btn {
                padding: 8px 15px;
            }
        }
        
        @media (max-width: 480px) {
            main {
                padding: 10px;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .library-stats {
                font-size: 0.8rem;
                padding: 8px 12px;
            }
            
            .filters-container {
                padding: 10px;
                margin-bottom: 15px;
            }
            
            .search-bar input {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
            
            .desktop-filter-row {
                flex-direction: column;
                gap: 8px;
            }
            
            .filter-group {
                min-width: 0;
                flex: 1;
            }
            
            .filter-group label {
                font-size: 0.7rem;
                margin-bottom: 3px;
            }
            
            .filter-group select {
                padding: 6px 8px;
                font-size: 0.75rem;
                background-position: right 5px center;
                background-size: 0.8em;
            }
            
            /* Mobile filter row styles */
            .mobile-filter-row .filter-group select {
                padding-right: 18px;
            }
            
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
                gap: 10px;
            }
            
            .manhwa-cover {
                height: 160px;
            }
            
            .manhwa-status {
                padding: 3px 6px;
                font-size: 0.65rem;
                top: 5px;
                right: 5px;
            }
            
            .manhwa-title {
                font-size: 0.8rem;
                min-height: 1.8em;
            }
            
            /* Mobile inline status layout */
            .manhwa-info {
                position: relative;
            }
            
            .manhwa-info-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 5px;
                flex-wrap: nowrap;
            }
            
            .manhwa-author, .manhwa-genre {
                font-size: 0.65rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 50%;
            }
            
            .reading-status-select {
                font-size: 0.65rem;
                padding: 3px 15px 3px 5px;
                height: 24px;
                background-position: right 3px center;
                background-size: 0.7em;
                margin: 3px 0;
            }
            
            .reading-dates {
                font-size: 0.65rem;
                padding: 4px 6px;
            }
            
            .manhwa-actions a {
                font-size: 0.7rem;
            }
            
            .view-btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .add-btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            /* List view adjustments */
            .manhwa-list-item .manhwa-cover {
                width: 80px;
                height: 120px;
            }
            
            .manhwa-list-item .manhwa-info {
                padding: 8px;
            }
            
            .manhwa-list-item .manhwa-title {
                font-size: 0.9rem;
            }
            
            .manhwa-description {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <div class="page-header">
            <h1>My Manhwa Library</h1>
            <div class="library-stats">
                <strong><?php echo $total_manhwas; ?></strong> manhwas in your collection
            </div>
        </div>
        
        <div class="filters-container">
            <form action="" method="GET">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
                
                <div class="filter-options">
                    <div class="desktop-filter-row">
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" onchange="this.form.submit()">
                                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="Ongoing" <?php echo $status_filter == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Hiatus" <?php echo $status_filter == 'Hiatus' ? 'selected' : ''; ?>>Hiatus</option>
                                <option value="Dropped" <?php echo $status_filter == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="genre">Genre</label>
                            <select name="genre" id="genre" onchange="this.form.submit()">
                                <option value="all" <?php echo $genre_filter == 'all' ? 'selected' : ''; ?>>All Genres</option>
                                <option value="BL" <?php echo $genre_filter == 'BL' ? 'selected' : ''; ?>>BL</option>
                                <option value="Straight" <?php echo $genre_filter == 'Straight' ? 'selected' : ''; ?>>Straight</option>
                                <option value="No Romance" <?php echo $genre_filter == 'No Romance' ? 'selected' : ''; ?>>No Romance</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="reading_status">Reading Status</label>
                            <select name="reading_status" id="reading_status" onchange="this.form.submit()">
                                <option value="all" <?php echo $reading_status_filter == 'all' ? 'selected' : ''; ?>>All Reading Statuses</option>
                                <option value="Plan to Read" <?php echo $reading_status_filter == 'Plan to Read' ? 'selected' : ''; ?>>Plan to Read</option>
                                <option value="Currently Reading" <?php echo $reading_status_filter == 'Currently Reading' ? 'selected' : ''; ?>>Currently Reading</option>
                                <option value="Done" <?php echo $reading_status_filter == 'Done' ? 'selected' : ''; ?>>Done</option>
                                <option value="Reread" <?php echo $reading_status_filter == 'Reread' ? 'selected' : ''; ?>>Reread</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="sort">Sort By</label>
                            <select name="sort" id="sort" onchange="this.form.submit()">
                                <option value="title_asc" <?php echo $sort_by == 'title_asc' ? 'selected' : ''; ?>>Title (A-Z)</option>
                                <option value="title_desc" <?php echo $sort_by == 'title_desc' ? 'selected' : ''; ?>>Title (Z-A)</option>
                                <option value="author_asc" <?php echo $sort_by == 'author_asc' ? 'selected' : ''; ?>>Author (A-Z)</option>
                                <option value="author_desc" <?php echo $sort_by == 'author_desc' ? 'selected' : ''; ?>>Author (Z-A)</option>
                                <option value="recent" <?php echo $sort_by == 'recent' ? 'selected' : ''; ?>>Recently Added</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div class="view-toggle">
                <button type="button" class="view-btn active" data-view="grid"><i class="fas fa-th"></i> Grid</button>
                <button type="button" class="view-btn" data-view="list"><i class="fas fa-list"></i> List</button>
            </div>
            <a href="add_manhwa.php" class="add-btn"><i class="fas fa-plus"></i> Add Manhwa</a>
        </div>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="manhwa-grid active-view">
                <?php 
                mysqli_data_seek($result, 0);
                while ($manhwa = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="manhwa-card">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <div class="manhwa-info-row">
                                <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                                <p class="manhwa-genre"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></p>
                            </div>
                            <div class="manhwa-info-row">
                                <span class="status-label"><?php echo htmlspecialchars($manhwa['status']); ?></span>
                                <select class="reading-status-select" data-manhwa-id="<?php echo $manhwa['manhwa_id']; ?>">
                                    <option value="">Reading Status</option>
                                    <option value="Plan to Read" <?php echo $manhwa['reading_status'] == 'Plan to Read' ? 'selected' : ''; ?>>Plan to Read</option>
                                    <option value="Currently Reading" <?php echo $manhwa['reading_status'] == 'Currently Reading' ? 'selected' : ''; ?>>Currently Reading</option>
                                    <option value="Done" <?php echo $manhwa['reading_status'] == 'Done' ? 'selected' : ''; ?>>Done</option>
                                    <option value="Reread" <?php echo $manhwa['reading_status'] == 'Reread' ? 'selected' : ''; ?>>Reread</option>
                                </select>
                            </div>
                            <?php if (!empty($manhwa['start_reading_date']) || !empty($manhwa['finish_reading_date'])): ?>
                            <div class="reading-dates">
                                <?php if (!empty($manhwa['start_reading_date'])): ?>
                                <span>Started: <?php echo date('M d, Y', strtotime($manhwa['start_reading_date'])); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($manhwa['finish_reading_date'])): ?>
                                <span>Finished: <?php echo date('M d, Y', strtotime($manhwa['finish_reading_date'])); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="manhwa-actions">
                                <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="manhwa-list" style="display: none;">
                <?php 
                mysqli_data_seek($result, 0);
                while ($manhwa = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="manhwa-list-item">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo htmlspecialchars($manhwa['status']); ?>"><?php echo htmlspecialchars($manhwa['status']); ?></span>
                        </div>
                        <div class="manhwa-info">
                            <div>
                                <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                                <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                                <div class="genre-dates-container">
                                    <p class="manhwa-genre">
                                        <?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?>
                                    </p>
                                    <?php if (!empty($manhwa['start_reading_date']) || !empty($manhwa['finish_reading_date'])): ?>
                                    <div class="reading-dates list-inline">
                                        <?php if (!empty($manhwa['start_reading_date'])): ?>
                                        <span>Started: <?php echo date('M d', strtotime($manhwa['start_reading_date'])); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($manhwa['finish_reading_date'])): ?>
                                        <span>Finished: <?php echo date('M d', strtotime($manhwa['finish_reading_date'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <p class="manhwa-description"><?php echo substr(htmlspecialchars($manhwa['description']), 0, 100) . (strlen($manhwa['description']) > 100 ? '...' : ''); ?></p>
                                <select class="reading-status-select" data-manhwa-id="<?php echo $manhwa['manhwa_id']; ?>">
                                    <option value="">Reading Status</option>
                                    <option value="Plan to Read" <?php echo $manhwa['reading_status'] == 'Plan to Read' ? 'selected' : ''; ?>>Plan to Read</option>
                                    <option value="Currently Reading" <?php echo $manhwa['reading_status'] == 'Currently Reading' ? 'selected' : ''; ?>>Currently Reading</option>
                                    <option value="Done" <?php echo $manhwa['reading_status'] == 'Done' ? 'selected' : ''; ?>>Done</option>
                                    <option value="Reread" <?php echo $manhwa['reading_status'] == 'Reread' ? 'selected' : ''; ?>>Reread</option>
                                </select>
                            </div>
                            <div class="manhwa-actions">
                                <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-books"></i>
                <h3>Your library is empty</h3>
                <p>Start adding manhwas to your collection!</p>
                <a href="add_manhwa.php" class="add-btn">Add Manhwa</a>
            </div>
        <?php endif; ?>
    </main>

   <?php 
    $root_path = '../';
    include '../includes/footer.php'; 
    ?>

    <style>
        .reread-btn {
            background-color: var(--accent-color);
            color: white !important;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            margin-left: 5px;
        }
        
        .reread-btn:hover {
            background-color: #e84393;
            transform: translateY(-2px);
            text-decoration: none !important;
        }
    </style>
    
    <script>
        document.querySelectorAll('select:not(.reading-status-select)').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
        
        document.querySelectorAll('.reading-status-select').forEach(select => {
            select.addEventListener('change', function() {
                const manhwaId = this.getAttribute('data-manhwa-id');
                const readingStatus = this.value;
                
                if (manhwaId && readingStatus) {
                    this.style.opacity = '0.7';
                    
                    const formData = new FormData();
                    formData.append('manhwa_id', manhwaId);
                    formData.append('reading_status', readingStatus);
                    
                    fetch('update_reading_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.style.opacity = '1';
                        
                        if (data.success) {
                            const container = this.closest('.manhwa-info');
                            
                            let datesDiv = container.querySelector('.reading-dates');
                            if (!datesDiv && (data.start_date || data.finish_date)) {
                                datesDiv = document.createElement('div');
                                datesDiv.className = 'reading-dates';
                                this.insertAdjacentElement('afterend', datesDiv);
                            }
                            
                            if (datesDiv) {
                                let html = '';
                                if (data.start_date) {
                                    const startDate = new Date(data.start_date);
                                    html += `<span>Started: ${startDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>`;
                                }
                                if (data.finish_date) {
                                    const finishDate = new Date(data.finish_date);
                                    html += `<span>Finished: ${finishDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>`;
                                }
                                
                                if (html) {
                                    datesDiv.innerHTML = html;
                                    datesDiv.style.display = 'block';
                                } else if (datesDiv.parentNode) {
                                    datesDiv.parentNode.removeChild(datesDiv);
                                }
                            }
                            
                            // Update the list view dates as well if it exists
                            const listItem = document.querySelector(`.manhwa-list-item [data-manhwa-id="${manhwaId}"]`);
                            if (listItem) {
                                const listContainer = listItem.closest('.manhwa-info');
                                let listDatesDiv = listContainer.querySelector('.reading-dates.list-inline');
                                
                                if (!listDatesDiv && (data.start_date || data.finish_date)) {
                                    const genreDatesContainer = listContainer.querySelector('.genre-dates-container');
                                    if (genreDatesContainer) {
                                        listDatesDiv = document.createElement('div');
                                        listDatesDiv.className = 'reading-dates list-inline';
                                        genreDatesContainer.appendChild(listDatesDiv);
                                    }
                                }
                                
                                if (listDatesDiv) {
                                    let listHtml = '';
                                    if (data.start_date) {
                                        const startDate = new Date(data.start_date);
                                        listHtml += `<span>Started: ${startDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}</span>`;
                                    }
                                    if (data.finish_date) {
                                        const finishDate = new Date(data.finish_date);
                                        listHtml += `<span>Finished: ${finishDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}</span>`;
                                    }
                                    
                                    if (listHtml) {
                                        listDatesDiv.innerHTML = listHtml;
                                        listDatesDiv.style.display = 'inline-flex';
                                    } else if (listDatesDiv.parentNode) {
                                        listDatesDiv.parentNode.removeChild(listDatesDiv);
                                    }
                                }
                            }
                        } else {
                            console.error('Error updating reading status:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.style.opacity = '1';
                    });
                }
            });
        });
        
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.getAttribute('data-view');
                
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                if (view === 'grid') {
                    document.querySelector('.manhwa-grid').style.display = 'grid';
                    document.querySelector('.manhwa-list').style.display = 'none';
                } else {
                    document.querySelector('.manhwa-grid').style.display = 'none';
                    document.querySelector('.manhwa-list').style.display = 'flex';
                }
                
                localStorage.setItem('manhwa-view', view);
            });
        });
        
        const savedView = localStorage.getItem('manhwa-view');
        if (savedView) {
            document.querySelector(`.view-btn[data-view="${savedView}"]`).click();
        }
        
        // Add reread buttons to completed manhwas with reading links
        function addRereadButtons() {
            // Grid view
            document.querySelectorAll('.manhwa-grid .manhwa-card').forEach(card => {
                const select = card.querySelector('.reading-status-select');
                const manhwaId = select.getAttribute('data-manhwa-id');
                const readingStatus = select.value;
                const actions = card.querySelector('.manhwa-actions');
                
                // Check if it's completed and has a reading link
                if (readingStatus === 'Done') {
                    // We'll add the button and let the PHP script handle the check for reading link
                    if (!actions.querySelector('.reread-btn')) {
                        const rereadBtn = document.createElement('a');
                        rereadBtn.href = `reread.php?id=${manhwaId}`;
                        rereadBtn.className = 'reread-btn';
                        rereadBtn.textContent = 'Reread';
                        actions.appendChild(rereadBtn);
                    }
                }
            });
            
            // List view
            document.querySelectorAll('.manhwa-list .manhwa-list-item').forEach(item => {
                const select = item.querySelector('.reading-status-select');
                const manhwaId = select.getAttribute('data-manhwa-id');
                const readingStatus = select.value;
                const actions = item.querySelector('.manhwa-actions');
                
                // Check if it's completed and has a reading link
                if (readingStatus === 'Done') {
                    // We'll add the button and let the PHP script handle the check for reading link
                    if (!actions.querySelector('.reread-btn')) {
                        const rereadBtn = document.createElement('a');
                        rereadBtn.href = `reread.php?id=${manhwaId}`;
                        rereadBtn.className = 'reread-btn';
                        rereadBtn.textContent = 'Reread';
                        actions.appendChild(rereadBtn);
                    }
                }
            });
        }
        
        // Run initially
        addRereadButtons();
        
        // Run when reading status changes
        document.querySelectorAll('.reading-status-select').forEach(select => {
            select.addEventListener('change', function() {
                setTimeout(addRereadButtons, 1000); // Wait for AJAX to complete
            });
        });
    </script>
</body>
</html>