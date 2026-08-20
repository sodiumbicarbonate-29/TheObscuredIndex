<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
mysqli_set_charset($conn, "utf8mb4");

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
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Plan to Read') as plan_count,
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ?) as total_count";
$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, "iiii", $user_id, $user_id, $user_id, $user_id);
mysqli_stmt_execute($stats_stmt);
$stats_result = mysqli_stmt_get_result($stats_stmt);
$stats = mysqli_fetch_assoc($stats_result);

// Get plan to read manhwas
$plan_query = "SELECT m.*, urs.reading_status 
               FROM Manhwas m 
               JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
               WHERE urs.user_id = ? AND urs.reading_status = 'Plan to Read' 
               ORDER BY urs.last_updated DESC 
               LIMIT 5";
$plan_stmt = mysqli_prepare($conn, $plan_query);
mysqli_stmt_bind_param($plan_stmt, "i", $user_id);
mysqli_stmt_execute($plan_stmt);
$plan_result = mysqli_stmt_get_result($plan_stmt);
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
            padding: 0;
            width: 100%;
        }
        
        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Slideshow styles */
        .cover-slideshow {
            width: 100%;
            height: 60vh;
            overflow: hidden;
            position: relative;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .cover-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .cover-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cover-slide.active {
            opacity: 1;
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
            gap: 15px;
            margin-top: 15px;
            flex-wrap: nowrap;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px 10px;
            flex: 1;
            min-width: 0;
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
            overflow: visible;
            min-height: 2.4em;
            word-wrap: break-word;
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
            display: inline-block;
            position: relative;
            top: 2px;
        }
        
        .genre-date-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: nowrap;
        }
        
        .start-date {
            font-size: 0.75rem;
            color: #777;
            background-color: rgba(108, 92, 231, 0.08);
            padding: 2px 6px;
            border-radius: 4px;
            position: relative;
            top: -1px;
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
        
        
        @media (max-width: 768px) {
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .manhwa-cover {
                height: 200px;
            }
            
            .stat-card {
                min-width: 0;
                flex: 1;
            }
            
            .stat-card h3 {
                font-size: 1.4rem;
            }
            
            .stat-card p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <?php
        $slideshow_query = "SELECT cover_image FROM Manhwas ORDER BY RAND() LIMIT 10";
        $slideshow_result = mysqli_query($conn, $slideshow_query);
        
        if (mysqli_num_rows($slideshow_result) > 0):
        ?>
        <div class="cover-slideshow">
            <?php 
            $active = true;
            while ($slide = mysqli_fetch_assoc($slideshow_result)): 
                $cover = !empty($slide['cover_image']) ? '../' . $slide['cover_image'] : '../images/default-cover.jpg';
            ?>
                <div class="cover-slide <?php echo $active ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($cover); ?>" alt="Manhwa Cover">
                </div>
            <?php 
                $active = false;
            endwhile; 
            ?>
        </div>
        <?php endif; ?>
        
        <div class="main-content">
            <div class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Track your manhwa collection</p>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3><?php echo $stats['reading_count']; ?></h3>
                    <p>Reading</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['completed_count']; ?></h3>
                    <p>Completed</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['plan_count']; ?></h3>
                    <p>Plan to Read</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_count']; ?></h3>
                    <p>Total</p>
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
                            <div class="genre-date-container">
                                <p class="manhwa-genre"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></p>
                                <span class="start-date"><?php echo !empty($manhwa['start_reading_date']) ? 'Started: ' . date('M d', strtotime($manhwa['start_reading_date'])) : ''; ?></span>
                            </div>
                            <div class="reading-progress">
                                <?php if (!empty($manhwa['reading_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="read-btn">Read Now</a> 
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
        
        <h2 class="section-title">
            <i class="fas fa-list"></i> Plan to Read
            <a href="library.php?reading_status=Plan+to+Read" class="view-all">View All</a>
        </h2>
        
        <?php if (mysqli_num_rows($plan_result) > 0): ?>
            <div class="manhwa-grid">
                <?php while ($manhwa = mysqli_fetch_assoc($plan_result)): ?>
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
                                <span>Added to plan</span>
                                <?php if (!empty($manhwa['reading_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="read-btn">Read Now</a> 
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
                <i class="fas fa-clipboard-list"></i>
                <h3>No manhwas planned</h3>
                <p>Add manhwas to your reading plan!</p>
            </div>
        <?php endif; ?>
            
        </div>
    </main>

    <?php 
    $root_path = '../';
    include '../includes/footer.php'; 
    ?>

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.cover-slide');
        
        function showSlides() {
            if (slides.length === 0) return;
            
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove('active');
            }
            
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            
            slides[slideIndex - 1].classList.add('active');
            setTimeout(showSlides, 5000); 
        }
        
        if (slides.length > 1) {
            setTimeout(showSlides, 5000);
        }
    </script>
</body>
</html>
