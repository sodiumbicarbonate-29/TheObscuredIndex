<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$reading_query = "SELECT m.*, urs.reading_status, urs.start_reading_date 
                 FROM Manhwas m 
                 JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
                 WHERE urs.user_id = ? AND urs.reading_status = 'Currently Reading' 
                 ORDER BY urs.last_updated DESC 
                 LIMIT 5";
$reading_stmt = mysqli_prepare($conn, $reading_query);
mysqli_stmt_bind_param($reading_stmt, "i", $user_id);
mysqli_stmt_execute($reading_stmt);
$reading_result = mysqli_stmt_get_result($reading_stmt);

$completed_query = "SELECT m.*, urs.finish_reading_date 
                   FROM Manhwas m 
                   JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
                   WHERE urs.user_id = ? AND urs.reading_status = 'Done' 
                   ORDER BY urs.finish_reading_date DESC 
                   LIMIT 5";
$completed_stmt = mysqli_prepare($conn, $completed_query);
mysqli_stmt_bind_param($completed_stmt, "i", $user_id);
mysqli_stmt_execute($completed_stmt);
$completed_result = mysqli_stmt_get_result($completed_stmt);

$new_uploads_query = "SELECT * FROM Manhwas 
                     WHERE upload_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                     ORDER BY upload_date DESC 
                     LIMIT 5";
$new_uploads_result = mysqli_query($conn, $new_uploads_query);

$stats_query = "SELECT 
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Currently Reading') as reading_count,
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Done') as completed_count,
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ?) as total_count";
$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, "iii", $user_id, $user_id, $user_id);
mysqli_stmt_execute($stats_stmt);
$stats_result = mysqli_stmt_get_result($stats_stmt);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Manhwa Collection</title>
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
        
        <?php include 'includes/header_styles.php'; ?>
        
        main {
            flex: 1;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .welcome-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-section h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
            border-top: 3px solid var(--primary-color);
        }
        
        .stat-card h3 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: var(--text-color);
            font-size: 0.9rem;
        }
        
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }
        
        .filter-tab {
            background-color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-color);
            transition: all 0.3s;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .filter-tab.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .filter-tab:hover:not(.active) {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .section-title {
            margin: 25px 0 15px;
            color: var(--primary-color);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .manhwa-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
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
            height: 250px;
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
        
        .status-ongoing {
            background-color: var(--primary-color);
        }
        
        .status-completed {
            background-color: var(--success-color);
        }
        
        .status-dropped {
            background-color: var(--accent-color);
        }
        
        .status-hiatus {
            background-color: var(--warning-color);
        }
        
        .manhwa-info {
            padding: 15px;
        }
        
        .manhwa-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-color);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.4em;
        }
        
        .manhwa-author {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }
        
        .manhwa-genre {
            font-size: 0.8rem;
            color: var(--accent-color);
            margin-bottom: 10px;
            font-style: italic;
        }
        
        .reading-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #666;
        }
        
        .read-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .read-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
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
        }
        
        .view-all {
            display: inline-block;
            margin-left: auto;
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        footer {
            background-color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        
        footer p {
            font-size: 0.9rem;
            color: var(--text-color);
        }
        
        @media (max-width: 768px) {
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .manhwa-cover {
                height: 200px;
            }
            
            .stat-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <div class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Track your manhwa collection</p>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3><?php echo $stats['reading_count']; ?></h3>
                    <p>Currently Reading</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['completed_count']; ?></h3>
                    <p>Completed</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_count']; ?></h3>
                    <p>Total Collection</p>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">
            <i class="fas fa-book-open"></i> Continue Reading
            <a href="#" class="view-all">View All</a>
        </h2>
        
        <?php if (mysqli_num_rows($reading_result) > 0): ?>
            <div class="manhwa-grid">
                <?php while ($manhwa = mysqli_fetch_assoc($reading_result)): ?>
                    <div class="manhwa-card">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo strtolower($manhwa['status']); ?>"><?php echo $manhwa['status']; ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                            <p class="manhwa-genre"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></p>
                            <div class="reading-progress">
                                <span><?php echo !empty($manhwa['start_reading_date']) ? 'Started: ' . date('M d, Y', strtotime($manhwa['start_reading_date'])) : 'Recently added'; ?></span>
                                <?php if (!empty($manhwa['reading_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank"class="fas fa-book-open"></i> Read Now

                                <?php else: ?>
                                    <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>" class="read-btn">View</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <h3>No manhwas in progress</h3>
                <p>Start reading to see your collection here!</p>
            </div>
        <?php endif; ?>
         
         
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>

        
</body>
</html>