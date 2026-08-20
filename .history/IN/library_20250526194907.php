<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "TheObscuredIndex");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'title_asc';

$query = "SELECT m.*, urs.reading_status 
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
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
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
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-color);
        }
        
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s;
            background-color: white;
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .manhwa-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
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
            font-size: 1.2rem;
            height: auto;
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
        
        .status-Ongoing {
            background-color: var(--primary-color);
        }
        
        .status-Completed {
            background-color: var(--success-color);
        }
        
        .status-Dropped {
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
        
        .reading-status-select {
            padding: 5px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 0.85rem;
            background-color: white;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .reading-status-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .manhwa-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .manhwa-actions a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .manhwa-actions a:hover {
            text-decoration: underline;
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
            
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .manhwa-cover {
                height: 200px;
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
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" onchange="this.form.submit()">
                            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="Ongoing" <?php echo $status_filter == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Dropped" <?php echo $status_filter == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
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
                            <span class="manhwa-status status-<?php echo htmlspecialchars($manhwa['status']); ?>"><?php echo htmlspecialchars($manhwa['status']); ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <p class="manhwa-author"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></p>
                            <div class="manhwa-actions">
                                <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>">View Details</a>
                                <a href="edit_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>">Edit</a>
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
                                <p><?php echo substr(htmlspecialchars($manhwa['description']), 0, 150) . (strlen($manhwa['description']) > 150 ? '...' : ''); ?></p>
                            </div>
                            <div class="manhwa-actions">
                                <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>">View Details</a>
                                <a href="edit_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>">Edit</a>
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

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>

    <script>
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
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
    </script>
</body>
</html>