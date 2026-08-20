<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "TheObscuredIndex");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get currently reading manhwas
$reading_query = "SELECT m.*, urs.reading_status 
                 FROM Manhwas m 
                 JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
                 WHERE urs.user_id = ? AND urs.reading_status = 'Currently Reading' 
                 ORDER BY urs.last_updated DESC 
                 LIMIT 5";
$reading_stmt = mysqli_prepare($conn, $reading_query);
mysqli_stmt_bind_param($reading_stmt, "i", $user_id);
mysqli_stmt_execute($reading_stmt);
$reading_result = mysqli_stmt_get_result($reading_stmt);

// Get recently completed manhwas
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

// Get new uploads (manhwas added in the last 30 days)
$new_uploads_query = "SELECT * FROM Manhwas 
                     WHERE upload_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                     ORDER BY upload_date DESC 
                     LIMIT 5";
$new_uploads_result = mysqli_query($conn, $new_uploads_query);

// Get reading statistics
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
            margin-bottom: 10px;
        }
        
        .reading-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #666;
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
    <!-- Include navbar styles directly since they're not being loaded -->
    <style>
    /* Header Styling */
    header {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
        padding: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 80px;
        overflow: hidden;
    }

    #logo {
        padding: 0 20px;
    }

    #logo a {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    #logo img {
        height: 120px;
        margin-right: 15px;
        filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4));
        transition: all 0.3s ease;
        margin-top: -20px;
        margin-bottom: -20px;
    }

    #logo a:hover img {
        filter: drop-shadow(0 0 8px rgba(108, 92, 231, 0.7));
        transform: scale(1.05);
    }

    #navbar ul {
        display: flex;
        list-style: none;
        padding-right: 20px;
    }

    #navbar ul li {
        margin-left: 20px;
    }

    #navbar ul li a {
        text-decoration: none;
        color: #6c5ce7;
        font-weight: 600;
        font-size: 1rem;
        transition: color 0.3s;
        position: relative;
    }

    #navbar ul li a:hover {
        color: #a29bfe;
    }

    #navbar ul li a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        background-color: #6c5ce7;
        bottom: -3px;
        left: 0;
        transition: width 0.3s;
    }

    #navbar ul li a:hover::after {
        width: 100%;
    }

    /* Menu toggle for mobile */
    .menu-toggle {
        display: none;
    }

    .menu-icon {
        display: none;
        cursor: pointer;
        padding: 20px;
        position: relative;
        z-index: 2;
    }

    .menu-icon span, 
    .menu-icon span::before, 
    .menu-icon span::after {
        display: block;
        position: absolute;
        width: 25px;
        height: 3px;
        background-color: #6c5ce7;
        transition: all 0.3s;
    }

    .menu-icon span::before {
        content: '';
        top: -8px;
    }

    .menu-icon span::after {
        content: '';
        top: 8px;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        #logo img {
            height: 100px;
            margin-top: -10px;
            margin-bottom: -10px;
        }
    }

    @media (max-width: 768px) {
        header {
            height: auto;
            min-height: 60px;
            flex-wrap: wrap;
            padding: 0;
        }
        
        #logo {
            padding: 10px 20px;
        }
        
        #logo img {
            height: 80px;
            margin-top: 0;
            margin-bottom: 0;
        }
        
        .menu-icon {
            display: inline-block;
            position: absolute;
            right: 10px;
            top: 15px;
        }
        
        #navbar {
            width: 100%;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .menu-toggle:checked ~ #navbar {
            max-height: 200px;
        }
        
        #navbar ul {
            flex-direction: column;
            padding: 0;
            width: 100%;
            background-color: white;
        }
        
        #navbar ul li {
            margin: 0;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #f0f0f0;
        }
    }

    @media (max-width: 480px) {
        #logo img {
            height: 80px;
        }
    }
    </style>

    <main>
        <div class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Track your manhwa collection and discover new titles.</p>
            
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
        
        <div class="filter-tabs">
            <button class="filter-tab active">All</button>
            <button class="filter-tab">Continue Reading</button>
            <button class="filter-tab">Recently Completed</button>
            <button class="filter-tab">New Uploads</button>
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
                            <img src="<?php echo !empty($manhwa['cover_image']) ? htmlspecialchars($manhwa['cover_image']) : 'images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo strtolower($manhwa['status']); ?>"><?php echo $manhwa['status']; ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                            <div class="reading-progress">
                                <span>Currently Reading</span>
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
            <i class="fas fa-check-circle"></i> Recently Completed
            <a href="#" class="view-all">View All</a>
        </h2>
        
        <?php if (mysqli_num_rows($completed_result) > 0): ?>
            <div class="manhwa-grid">
                <?php while ($manhwa = mysqli_fetch_assoc($completed_result)): ?>
                    <div class="manhwa-card">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? htmlspecialchars($manhwa['cover_image']) : 'images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo strtolower($manhwa['status']); ?>"><?php echo $manhwa['status']; ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                            <div class="reading-progress">
                                <span>Completed on <?php echo date('M d, Y', strtotime($manhwa['finish_reading_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-trophy"></i>
                <h3>No completed manhwas yet</h3>
                <p>Finish reading to see your achievements here!</p>
            </div>
        <?php endif; ?>
        
        <h2 class="section-title">
            <i class="fas fa-fire"></i> New Uploads
            <a href="#" class="view-all">View All</a>
        </h2>
        
        <?php if (mysqli_num_rows($new_uploads_result) > 0): ?>
            <div class="manhwa-grid">
                <?php while ($manhwa = mysqli_fetch_assoc($new_uploads_result)): ?>
                    <div class="manhwa-card">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? htmlspecialchars($manhwa['cover_image']) : 'images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo strtolower($manhwa['status']); ?>"><?php echo $manhwa['status']; ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                            <div class="reading-progress">
                                <span>Added <?php echo date('M d, Y', strtotime($manhwa['upload_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <h3>No new uploads</h3>
                <p>Check back later for new manhwa titles!</p>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>

    <script>
        // Filter tabs functionality
        const filterTabs = document.querySelectorAll('.filter-tab');
        const sections = document.querySelectorAll('.section-title, .manhwa-grid, .empty-state');
        
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                filterTabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Show/hide sections based on selected filter
                const filter = tab.textContent.trim();
                
                if (filter === 'All') {
                    sections.forEach(section => section.style.display = '');
                } else {
                    sections.forEach(section => {
                        const sectionTitle = section.previousElementSibling?.textContent || '';
                        if (sectionTitle.includes(filter) || section.previousElementSibling?.textContent?.includes(filter)) {
                            section.style.display = '';
                            if (section.previousElementSibling && section.previousElementSibling.classList.contains('section-title')) {
                                section.previousElementSibling.style.display = '';
                            }
                        } else {
                            section.style.display = 'none';
                            if (section.previousElementSibling && section.previousElementSibling.classList.contains('section-title')) {
                                section.previousElementSibling.style.display = 'none';
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>