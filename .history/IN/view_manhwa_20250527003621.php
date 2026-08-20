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
$message = '';
$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: library.php");
    exit();
}

$manhwa_id = (int)$_GET['id'];

$query = "SELECT * FROM Manhwas WHERE manhwa_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $manhwa_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: library.php");
    exit();
}

$manhwa = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $reading_link = !empty($_POST['reading_link']) ? mysqli_real_escape_string($conn, $_POST['reading_link']) : null;
    
    // Handle cover image upload
    $cover_image = $manhwa['cover_image']; // Keep existing image by default
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['cover_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = '../images/covers/';
            
            // Check if directory exists, create it if it doesn't
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $new_filename = 'cover_' . $manhwa_id . '_' . time() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path)) {
                $cover_image = 'images/covers/' . $new_filename;
            } else {
                $error = "Failed to upload image. Error: " . $_FILES['cover_image']['error'];
            }
        } else {
            $error = "Invalid file type. Allowed: jpg, jpeg, png, gif";
        }
    }
    
    $update_query = "UPDATE Manhwas SET 
                    title = ?, 
                    author = ?, 
                    status = ?, 
                    genre = ?, 
                    description = ?,
                    reading_link = ?,
                    cover_image = ?
                    WHERE manhwa_id = ?";
    
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "sssssssi", $title, $author, $status, $genre, $description, $reading_link, $cover_image, $manhwa_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = "Manhwa updated successfully!";
        
        $query = "SELECT * FROM Manhwas WHERE manhwa_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $manhwa_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $manhwa = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating manhwa: " . mysqli_error($conn);
    }
    
    if (isset($_POST['reading_status']) && !empty($_POST['reading_status'])) {
        $reading_status = mysqli_real_escape_string($conn, $_POST['reading_status']);
        $start_date = !empty($_POST['start_reading_date']) ? $_POST['start_reading_date'] : null;
        $finish_date = !empty($_POST['finish_reading_date']) ? $_POST['finish_reading_date'] : null;
        
        $check_query = "SELECT * FROM User_Reading_Status WHERE user_id = ? AND manhwa_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $manhwa_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $update_query = "UPDATE User_Reading_Status SET 
                            reading_status = ?, 
                            start_reading_date = ?, 
                            finish_reading_date = ?, 
                            last_updated = NOW() 
                            WHERE user_id = ? AND manhwa_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sssii", $reading_status, $start_date, $finish_date, $user_id, $manhwa_id);
            mysqli_stmt_execute($update_stmt);
        } else {
            $insert_query = "INSERT INTO User_Reading_Status 
                            (user_id, manhwa_id, reading_status, start_reading_date, finish_reading_date, last_updated) 
                            VALUES (?, ?, ?, ?, ?, NOW())";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "iisss", $user_id, $manhwa_id, $reading_status, $start_date, $finish_date);
            mysqli_stmt_execute($insert_stmt);
        }
    }
}

// Get reading status data
$query = "SELECT urs.reading_status, urs.start_reading_date, urs.finish_reading_date 
          FROM User_Reading_Status urs 
          WHERE urs.user_id = ? AND urs.manhwa_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $manhwa_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $reading_data = mysqli_fetch_assoc($result);
    $manhwa['reading_status'] = $reading_data['reading_status'];
    $manhwa['start_reading_date'] = $reading_data['start_reading_date'];
    $manhwa['finish_reading_date'] = $reading_data['finish_reading_date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($manhwa['title']); ?> - My Manhwa Collection</title>
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
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .back-btn i {
            margin-right: 5px;
        }
        
        .back-btn:hover {
            transform: translateX(-5px);
        }
        
        .manhwa-container {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .manhwa-header {
            display: flex;
            padding: 30px;
            background: linear-gradient(to right, rgba(108, 92, 231, 0.1), rgba(253, 121, 168, 0.05));
        }
        
        .manhwa-cover {
            width: 250px;
            height: 350px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
            position: relative;
        }
        
        .change-cover {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            text-align: center;
            padding: 8px 0;
            font-size: 0.8rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .manhwa-cover:hover .change-cover {
            opacity: 1;
        }
        
        .manhwa-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .manhwa-info {
            margin-left: 30px;
            flex: 1;
        }
        
        .manhwa-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .manhwa-meta {
            margin-bottom: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .meta-label {
            font-weight: 600;
            width: 100px;
            color: var(--text-color);
        }
        
        .meta-value {
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
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
        
        .reading-status {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(108, 92, 231, 0.05);
            border-radius: 8px;
        }
        
        .reading-status h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .reading-dates {
            margin-top: 10px;
        }
        
        .reading-dates span {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .manhwa-body {
            padding: 30px;
        }
        
        .manhwa-description {
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .manhwa-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #e0e0e0;
            color: var(--text-color);
            border: none;
        }
        
        .btn-secondary:hover {
            background-color: #d0d0d0;
        }
        
        .edit-form {
            display: none;
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: opacity 0.5s ease-in-out;
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
        
        .fade-out {
            opacity: 0;
        }
        
        .read-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .read-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <a href="library.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Library</a>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success" id="success-alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" id="error-alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="manhwa-container">
            <div class="manhwa-header">
                <div class="manhwa-cover">
                    <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                    <div class="change-cover" onclick="document.getElementById('edit-btn').click()">
                        <i class="fas fa-camera"></i> Change Cover
                    </div>
                </div>
                <div class="manhwa-info">
                    <h1 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h1>
                    
                    <div class="manhwa-meta">
                        <div class="meta-item">
                            <span class="meta-label">Author:</span>
                            <span class="meta-value"><?php echo !empty($manhwa['author']) ? htmlspecialchars($manhwa['author']) : 'Unknown Author'; ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Status:</span>
                            <span class="status-badge status-<?php echo htmlspecialchars($manhwa['status']); ?>"><?php echo htmlspecialchars($manhwa['status']); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Genre:</span>
                            <span class="meta-value"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'Not specified'; ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Added on:</span>
                            <span class="meta-value"><?php echo date('F j, Y', strtotime($manhwa['upload_date'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="reading-status">
                        <h3>Your Reading Status</h3>
                        <div class="meta-item">
                            <span class="meta-label">Status:</span>
                            <span class="meta-value"><?php echo !empty($manhwa['reading_status']) ? htmlspecialchars($manhwa['reading_status']) : 'Not started'; ?></span>
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
                        
                        <?php if (!empty($manhwa['reading_link'])): ?>
                        <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="read-btn">
                            <i class="fas fa-book-open"></i> Read Now
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="manhwa-actions">
                        <button id="edit-btn" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Details</button>
                        <a href="library.php" class="btn btn-secondary">Back to Library</a>
                    </div>
                </div>
            </div>
            
            <div class="manhwa-body">
                <h2>Description</h2>
                <div class="manhwa-description">
                    <?php echo !empty($manhwa['description']) ? nl2br(htmlspecialchars($manhwa['description'])) : 'No description available.'; ?>
                </div>
                
                <div id="edit-form" class="edit-form">
                    <h2>Edit Manhwa</h2>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($manhwa['title']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" id="author" name="author" class="form-control" value="<?php echo htmlspecialchars($manhwa['author']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="Ongoing" <?php echo $manhwa['status'] == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="Completed" <?php echo $manhwa['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Dropped" <?php echo $manhwa['status'] == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <select id="genre" name="genre" class="form-control">
                                <option value="Yaoi" <?php echo $manhwa['genre'] == 'Yaoi' ? 'selected' : ''; ?>>Yaoi</option>
                                <option value="Straight" <?php echo $manhwa['genre'] == 'Straight' ? 'selected' : ''; ?>>Straight</option>
                                <option value="No Romance" <?php echo $manhwa['genre'] == 'No Romance' ? 'selected' : ''; ?>>No Romance</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="reading_status">Your Reading Status</label>
                            <select id="reading_status" name="reading_status" class="form-control">
                                <option value="Plan to Read" <?php echo $manhwa['reading_status'] == 'Plan to Read' ? 'selected' : ''; ?>>Plan to Read</option>
                                <option value="Currently Reading" <?php echo $manhwa['reading_status'] == 'Currently Reading' ? 'selected' : ''; ?>>Currently Reading</option>
                                <option value="Done" <?php echo $manhwa['reading_status'] == 'Done' ? 'selected' : ''; ?>>Done</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_reading_date">Start Reading Date</label>
                            <input type="date" id="start_reading_date" name="start_reading_date" class="form-control" value="<?php echo !empty($manhwa['start_reading_date']) ? $manhwa['start_reading_date'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="finish_reading_date">Finish Reading Date</label>
                            <input type="date" id="finish_reading_date" name="finish_reading_date" class="form-control" value="<?php echo !empty($manhwa['finish_reading_date']) ? $manhwa['finish_reading_date'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="reading_link">Reading Link</label>
                            <input type="url" id="reading_link" name="reading_link" class="form-control" value="<?php echo !empty($manhwa['reading_link']) ? htmlspecialchars($manhwa['reading_link']) : ''; ?>" placeholder="https://example.com/read/manhwa-title">
                            <small>Enter the URL where this manhwa can be read online</small>
                        </div>
                        
                                <div class="form-group">
                            <label for="cover_image">Cover Image</label>
                            <input type="file" id="cover_image" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/gif">
                            <small>Upload a new cover image (JPG, PNG, or GIF)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($manhwa['description']); ?></textarea>
                        </div>
                        
                        <div class="manhwa-actions">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" id="cancel-btn" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('edit-btn').addEventListener('click', function() {
            document.getElementById('edit-form').style.display = 'block';
            this.style.display = 'none';
        });
        
        document.getElementById('cancel-btn').addEventListener('click', function() {
            document.getElementById('edit-form').style.display = 'none';
            document.getElementById('edit-btn').style.display = 'inline-block';
        });
        
        // Force image refresh by adding timestamp to URL
        function refreshImage(imgElement) {
            const src = imgElement.src;
            imgElement.src = src + '?t=' + new Date().getTime();
        }
        
        // Auto-hide alerts after 3 seconds
        function hideAlerts() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.classList.add('fade-out');
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 5000);
            }
        }
        
        <?php if (!empty($message) && strpos($message, 'updated successfully') !== false): ?>
        window.onload = function() {
            const coverImg = document.querySelector('.manhwa-cover img');
            refreshImage(coverImg);
            hideAlerts();
        };
        <?php else: ?>
        window.onload = function() {
            hideAlerts();
        };
        <?php endif; ?>
    </script>
</body>
</html>