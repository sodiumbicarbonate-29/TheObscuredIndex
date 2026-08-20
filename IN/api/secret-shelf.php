<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if (!isset($_SESSION['secret_shelf_access']) || $_SESSION['secret_shelf_access'] !== true) {
    header("Location: secret-shelf_login.php");
    exit();
}

$access_query = "SELECT * FROM Secret_Shelf_Access WHERE user_id = ?";
$access_stmt = mysqli_prepare($conn, $access_query);
mysqli_stmt_bind_param($access_stmt, "i", $user_id);
mysqli_stmt_execute($access_stmt);
$access_result = mysqli_stmt_get_result($access_stmt);

if (mysqli_num_rows($access_result) == 0) {
    unset($_SESSION['secret_shelf_access']);
    header("Location: secret-shelf_login.php");
    exit();
}

$query = "SELECT sm.*, m.title, m.author, m.status, m.genre, m.cover_image, m.reading_link,
          CASE WHEN m.reading_link IS NOT NULL AND m.reading_link != '' THEN 1 ELSE 0 END as has_reading_link
          FROM Secret_Manhwas sm
          JOIN Manhwas m ON sm.manhwa_id = m.manhwa_id
          WHERE sm.user_id = ? AND m.user_id = ?
          ORDER BY sm.added_date DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total_manhwas = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Shelf - Hidden Collection</title>
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
            --dark-color: #0f0f1a;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--dark-color);
            color: #fff;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(162, 155, 254, 0.1)"/></svg>');
        }
        
        main {
            flex: 1;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .secret-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .secret-header h1 {
            font-size: 3rem;
            color: #fff;
            text-shadow: 0 0 10px rgba(108, 92, 231, 0.8), 0 0 20px rgba(108, 92, 231, 0.4);
            margin-bottom: 15px;
            font-family: 'Times New Roman', serif;
            letter-spacing: 2px;
        }
        
        .secret-header p {
            font-size: 1.2rem;
            color: #a29bfe;
            max-width: 700px;
            margin: 0 auto;
            font-style: italic;
        }
        
        .secret-shelf {
            background: rgba(15, 15, 26, 0.8);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 30px rgba(108, 92, 231, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(108, 92, 231, 0.3);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .secret-shelf::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(108, 92, 231, 0.8), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .secret-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .secret-stats {
            background: rgba(253, 121, 168, 0.1);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #fd79a8;
            border: 1px solid rgba(253, 121, 168, 0.3);
        }
        
        .add-secret {
            background: linear-gradient(135deg, #fd79a8, #e84393);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-secret:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(253, 121, 168, 0.4);
        }
        
        .manhwa-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
        }
        
        .manhwa-card {
            background: rgba(30, 30, 46, 0.8);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .manhwa-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5), 0 0 20px rgba(108, 92, 231, 0.4);
        }
        
        .manhwa-cover {
            height: 280px;
            overflow: hidden;
            position: relative;
        }
        
        .manhwa-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .manhwa-card:hover .manhwa-cover img {
            transform: scale(1.05);
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
            z-index: 2;
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
            padding: 15px;
        }
        
        .manhwa-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #fff;
        }
        
        .manhwa-author {
            font-size: 0.9rem;
            color: #a29bfe;
            margin-bottom: 8px;
        }
        
        .manhwa-genre {
            font-size: 0.8rem;
            color: var(--accent-color);
            margin-bottom: 12px;
            font-style: italic;
        }
        
        .manhwa-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .view-btn, .read-btn {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .view-btn {
            background-color: rgba(108, 92, 231, 0.2);
            color: var(--secondary-color);
            border: 1px solid rgba(108, 92, 231, 0.3);
        }
        
        .view-btn:hover {
            background-color: rgba(108, 92, 231, 0.3);
            transform: translateY(-2px);
        }
        
        .read-btn {
            background-color: rgba(253, 121, 168, 0.2);
            color: var(--accent-color);
            border: 1px solid rgba(253, 121, 168, 0.3);
        }
        
        .read-btn:hover {
            background-color: rgba(253, 121, 168, 0.3);
            transform: translateY(-2px);
        }
        
        .empty-shelf {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-shelf i {
            font-size: 4rem;
            color: rgba(108, 92, 231, 0.3);
            margin-bottom: 20px;
        }
        
        .empty-shelf h3 {
            font-size: 1.8rem;
            color: #a29bfe;
            margin-bottom: 15px;
        }
        
        .empty-shelf p {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 25px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            color: #fff;
            text-shadow: 0 0 8px var(--accent-color);
            transform: translateX(-5px);
        }
        
        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: white;
            pointer-events: none;
            opacity: 0;
            animation: sparkleFloat 2s ease-in-out infinite;
        }
        
        @keyframes sparkleFloat {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-20px) scale(1); opacity: 0; }
        }
        
        @media (max-width: 768px) {
            main {
                padding: 20px 15px;
            }
            
            .secret-header h1 {
                font-size: 2.2rem;
            }
            
            .secret-header p {
                font-size: 1rem;
            }
            
            .secret-shelf {
                padding: 20px;
            }
            
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
            }
            
            .manhwa-cover {
                height: 220px;
            }
        }
        
        @media (max-width: 480px) {
            .secret-header h1 {
                font-size: 1.8rem;
            }
            
            .secret-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
            }
            
            .manhwa-cover {
                height: 200px;
            }
            
            .manhwa-title {
                font-size: 0.95rem;
            }
            
            .manhwa-actions {
                flex-direction: column;
                gap: 8px;
            }
            
            .view-btn, .read-btn {
                text-align: center;
                padding: 8px 0;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/navbarIN.php'; ?>

    <main>
        <div class="secret-header">
            <h1>The Secret Shelf</h1>
            <p>Your hidden collection of special manhwas...</p>
        </div>
        
        <div class="secret-shelf">
            <div class="secret-actions">
                <div class="secret-stats">
                    <strong><?php echo $total_manhwas; ?></strong> manhwas in your secret collection
                </div>
                <a href="add_secret_manhwa.php" class="add-secret"><i class="fas fa-plus"></i> Add to Secret Shelf</a>
            </div>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="manhwa-grid">
                    <?php while ($manhwa = mysqli_fetch_assoc($result)): ?>
                        <div class="manhwa-card">
                            <div class="manhwa-cover">
                                <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                                <span class="manhwa-status status-<?php echo htmlspecialchars($manhwa['status']); ?>"><?php echo htmlspecialchars($manhwa['status']); ?></span>
                            </div>
                            <div class="manhwa-info">
                                <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                                <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                                <p class="manhwa-genre"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></p>
                                <div class="manhwa-actions">
                                    <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>" class="view-btn">View Details</a>
                                    <?php if ($manhwa['has_reading_link']): ?>
                                        <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="read-btn">Read</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-shelf">
                    <i class="fas fa-book-dead"></i>
                    <h3>Your Secret Shelf is Empty</h3>
                    <p>Add your special manhwas to this hidden collection that only you can access.</p>
                    <a href="add_secret_manhwa.php" class="add-secret"><i class="fas fa-plus"></i> Add Your First Secret</a>
                </div>
            <?php endif; ?>
            
            <a href="home.php" class="back-link"><i class="fas fa-arrow-left"></i> Return to your regular collection</a>
        </div>
    </main>

    <?php 
    $root_path = '../../';
    include '../../includes/footer.php'; 
    ?>

    <script>
        function createSparkles() {
            const secretShelf = document.querySelector('.secret-shelf');
            const shelfRect = secretShelf.getBoundingClientRect();
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle';
                    
                    const left = Math.random() * shelfRect.width;
                    sparkle.style.left = `${left + shelfRect.left}px`;
                    sparkle.style.top = `${shelfRect.bottom - 5}px`;
                    
                    const delay = Math.random() * 2;
                    sparkle.style.animationDelay = `${delay}s`;
                    
                    document.body.appendChild(sparkle);
                    
                    setTimeout(() => {
                        sparkle.remove();
                        createSparkle(shelfRect);
                    }, 2000 + delay * 1000);
                }, i * 100);
            }
        }
        
        function createSparkle(shelfRect) {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            
            const left = Math.random() * shelfRect.width;
            sparkle.style.left = `${left + shelfRect.left}px`;
            sparkle.style.top = `${shelfRect.bottom - 5}px`;
            
            const delay = Math.random() * 2;
            sparkle.style.animationDelay = `${delay}s`;
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
                createSparkle(shelfRect);
            }, 2000 + delay * 1000);
        }
        
        window.addEventListener('load', () => {
            createSparkles();
        });
    </script>
</body>
</html>