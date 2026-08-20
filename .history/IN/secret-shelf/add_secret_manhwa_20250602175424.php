<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = '';
$error = '';

$access_query = "SELECT * FROM Secret_Shelf_Access WHERE user_id = ?";
$access_stmt = mysqli_prepare($conn, $access_query);
mysqli_stmt_bind_param($access_stmt, "i", $user_id);
mysqli_stmt_execute($access_stmt);
$access_result = mysqli_stmt_get_result($access_stmt);

if (mysqli_num_rows($access_result) == 0) {
    header("Location: secret-shelf_login.php");
    exit();
}

$query = "SELECT m.* FROM Manhwas m
          LEFT JOIN Secret_Manhwas sm ON m.manhwa_id = sm.manhwa_id AND sm.user_id = ?
          WHERE sm.manhwa_id IS NULL
          ORDER BY m.title ASC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_secret'])) {
    $manhwa_ids = isset($_POST['manhwa_ids']) ? $_POST['manhwa_ids'] : [];
    
    if (empty($manhwa_ids)) {
        $error = "Please select at least one manhwa to add to your secret shelf.";
    } else {
        $success_count = 0;
        
        foreach ($manhwa_ids as $manhwa_id) {
            $check_query = "SELECT * FROM Secret_Manhwas WHERE user_id = ? AND manhwa_id = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $manhwa_id);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) == 0) {
                $insert_query = "INSERT INTO Secret_Manhwas (user_id, manhwa_id, added_date) VALUES (?, ?, NOW())";
                $insert_stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $manhwa_id);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $success_count++;
                }
            }
        }
        
        if ($success_count > 0) {
            $message = $success_count . " manhwa" . ($success_count > 1 ? "s" : "") . " added to your secret shelf!";
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $error = "No new manhwas were added to your secret shelf.";
        }
    }
}

if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $manhwa_id = (int)$_GET['add'];
    
    $check_query = "SELECT * FROM Secret_Manhwas WHERE user_id = ? AND manhwa_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $manhwa_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO Secret_Manhwas (user_id, manhwa_id, added_date) VALUES (?, ?, NOW())";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $manhwa_id);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $message = "Manhwa added to your secret shelf!";
        } else {
            $error = "Failed to add manhwa to your secret shelf.";
        }
    } else {
        $error = "This manhwa is already in your secret shelf.";
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Secret Shelf</title>
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
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }
        
        .secret-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .secret-header h1 {
            font-size: 2.5rem;
            color: #fff;
            text-shadow: 0 0 10px rgba(108, 92, 231, 0.8), 0 0 20px rgba(108, 92, 231, 0.4);
            margin-bottom: 15px;
            font-family: 'Times New Roman', serif;
            letter-spacing: 2px;
        }
        
        .secret-header p {
            font-size: 1.1rem;
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
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(0, 184, 148, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(253, 121, 168, 0.1);
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
        }
        
        .search-filter {
            margin-bottom: 25px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(108, 92, 231, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(253, 121, 168, 0.3);
        }
        
        .manhwa-list {
            margin-top: 20px;
        }
        
        .manhwa-item {
            display: flex;
            align-items: center;
            background: rgba(30, 30, 46, 0.6);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(108, 92, 231, 0.2);
            transition: all 0.3s;
        }
        
        .manhwa-item:hover {
            background: rgba(30, 30, 46, 0.8);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .manhwa-checkbox {
            margin-right: 15px;
            width: 20px;
            height: 20px;
            accent-color: var(--accent-color);
        }
        
        .manhwa-cover-small {
            width: 60px;
            height: 90px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .manhwa-cover-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .manhwa-details {
            flex: 1;
        }
        
        .manhwa-title-small {
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 5px;
        }
        
        .manhwa-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.8rem;
            color: #a29bfe;
        }
        
        .manhwa-meta span {
            display: inline-flex;
            align-items: center;
        }
        
        .manhwa-meta i {
            margin-right: 5px;
            font-size: 0.75rem;
        }
        
        .manhwa-add {
            background: rgba(253, 121, 168, 0.2);
            color: var(--accent-color);
            border: 1px solid rgba(253, 121, 168, 0.3);
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            margin-left: 10px;
            flex-shrink: 0;
        }
        
        .manhwa-add:hover {
            background: rgba(253, 121, 168, 0.3);
            transform: translateY(-2px);
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #fd79a8, #e84393);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(253, 121, 168, 0.4);
        }
        
        .btn-secondary {
            background: rgba(108, 92, 231, 0.2);
            color: var(--secondary-color);
            border: 1px solid rgba(108, 92, 231, 0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(108, 92, 231, 0.3);
            transform: translateY(-3px);
        }
        
        .select-all-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            background: rgba(30, 30, 46, 0.6);
            padding: 10px 15px;
            border-radius: 8px;
        }
        
        .select-all-checkbox {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            accent-color: var(--accent-color);
        }
        
        .select-all-label {
            font-weight: 600;
            color: #a29bfe;
        }
        
        .empty-list {
            text-align: center;
            padding: 40px 20px;
        }
        
        .empty-list i {
            font-size: 3rem;
            color: rgba(108, 92, 231, 0.3);
            margin-bottom: 15px;
        }
        
        .empty-list h3 {
            font-size: 1.5rem;
            color: #a29bfe;
            margin-bottom: 10px;
        }
        
        .empty-list p {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            main {
                padding: 20px 15px;
            }
            
            .secret-header h1 {
                font-size: 2rem;
            }
            
            .secret-header p {
                font-size: 1rem;
            }
            
            .secret-shelf {
                padding: 20px;
            }
            
            .manhwa-item {
                padding: 12px;
            }
            
            .manhwa-cover-small {
                width: 50px;
                height: 75px;
            }
        }
        
        @media (max-width: 480px) {
            .secret-header h1 {
                font-size: 1.8rem;
            }
            
            .manhwa-item {
                flex-wrap: wrap;
            }
            
            .manhwa-checkbox {
                margin-right: 10px;
            }
            
            .manhwa-cover-small {
                width: 40px;
                height: 60px;
                margin-right: 10px;
            }
            
            .manhwa-details {
                width: calc(100% - 75px);
            }
            
            .manhwa-add {
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
                text-align: center;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <div class="secret-header">
            <h1>Add to Secret Shelf</h1>
            <p>Select manhwas from your library to add to your secret collection...</p>
        </div>
        
        <div class="secret-shelf">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="search-filter">
                <input type="text" id="search-input" class="search-input" placeholder="Search your manhwas...">
            </div>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <form action="" method="POST">
                    <div class="select-all-container">
                        <input type="checkbox" id="select-all" class="select-all-checkbox">
                        <label for="select-all" class="select-all-label">Select All</label>
                    </div>
                    
                    <div class="manhwa-list">
                        <?php while ($manhwa = mysqli_fetch_assoc($result)): ?>
                            <div class="manhwa-item">
                                <input type="checkbox" name="manhwa_ids[]" value="<?php echo $manhwa['manhwa_id']; ?>" class="manhwa-checkbox">
                                <div class="manhwa-cover-small">
                                    <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                                </div>
                                <div class="manhwa-details">
                                    <h3 class="manhwa-title-small"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                                    <div class="manhwa-meta">
                                        <span><i class="fas fa-user"></i> <?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown'; ?></span>
                                        <span><i class="fas fa-tag"></i> <?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></span>
                                        <span><i class="fas fa-circle-info"></i> <?php echo htmlspecialchars($manhwa['status']); ?></span>
                                    </div>
                                </div>
                                <a href="?add=<?php echo $manhwa['manhwa_id']; ?>" class="manhwa-add">Add</a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="form-actions">
                        <a href="secret-shelf.php" class="btn btn-secondary">Back to Secret Shelf</a>
                        <button type="submit" name="add_to_secret" class="btn btn-primary">Add Selected to Secret Shelf</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="empty-list">
                    <i class="fas fa-book"></i>
                    <h3>No Manhwas Available</h3>
                    <p>All your manhwas are already in your secret shelf or you haven't added any manhwas to your library yet.</p>
                    <div class="form-actions">
                        <a href="secret-shelf.php" class="btn btn-secondary">Back to Secret Shelf</a>
                        <a href="add_manhwa.php" class="btn btn-primary">Add New Manhwa</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php 
    $root_path = '../';
    include '../includes/footer.php'; 
    ?>

    <script>
        const searchInput = document.getElementById('search-input');
        const manhwaItems = document.querySelectorAll('.manhwa-item');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            manhwaItems.forEach(item => {
                const title = item.querySelector('.manhwa-title-small').textContent.toLowerCase();
                const author = item.querySelector('.manhwa-meta').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || author.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        const selectAllCheckbox = document.getElementById('select-all');
        const manhwaCheckboxes = document.querySelectorAll('.manhwa-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            manhwaCheckboxes.forEach(checkbox => {
                if (checkbox.closest('.manhwa-item').style.display !== 'none') {
                    checkbox.checked = isChecked;
                }
            });
        });
        
        manhwaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllState);
        });
        
        function updateSelectAllState() {
            const visibleCheckboxes = Array.from(manhwaCheckboxes).filter(
                checkbox => checkbox.closest('.manhwa-item').style.display !== 'none'
            );
            
            const allChecked = visibleCheckboxes.every(checkbox => checkbox.checked);
            const someChecked = visibleCheckboxes.some(checkbox => checkbox.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
    </script>
</body>
</html>