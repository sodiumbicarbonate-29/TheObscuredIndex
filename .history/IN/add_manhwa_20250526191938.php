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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $cover_image = '';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['cover_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = 'uploads/covers/' . uniqid() . '.' . $filetype;
            $upload_dir = '../' . $new_filename;
            
            if (!is_dir('../uploads/covers')) {
                mkdir('../uploads/covers', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_dir)) {
                $cover_image = $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG and WEBP files are allowed.";
        }
    }
    
    if (empty($error)) {
        $query = "INSERT INTO Manhwas (title, author, status, description, cover_image) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $title, $author, $status, $description, $cover_image);
        
        if (mysqli_stmt_execute($stmt)) {
            $manhwa_id = mysqli_insert_id($conn);
            
            // Add to user's reading status
            $reading_status = mysqli_real_escape_string($conn, $_POST['reading_status']);
            $reading_query = "INSERT INTO User_Reading_Status (user_id, manhwa_id, reading_status, last_updated) 
                             VALUES (?, ?, ?, NOW())";
            $reading_stmt = mysqli_prepare($conn, $reading_query);
            mysqli_stmt_bind_param($reading_stmt, "iis", $user_id, $manhwa_id, $reading_status);
            mysqli_stmt_execute($reading_stmt);
            
            $message = "Manhwa added successfully!";
            
            header("Location: library.php");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Manhwa - My Manhwa Collection</title>
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
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
        
        .page-header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
        }
        
        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
        }
        
        .back-btn {
            position: absolute;
            left: -10px;
            top: 5px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
            z-index: 10;
        }
        
        .back-btn:hover {
            color: var(--accent-color);
            transform: translateX(-3px);
        }
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.2);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(108, 92, 231, 0.3);
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        }
        
        
        .form-group {
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .form-group.focused {
            transform: translateY(-5px);
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
            text-align: left;
        }
        
        .form-control {
            width: 90%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            margin: 0 auto;
            display: block;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 8px rgba(108, 92, 231, 0.4);
            transform: translateY(-2px);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(108, 92, 231, 0.4);
        }
        
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
        }
        
        .btn-primary:hover::after {
            opacity: 1;
        }
        
        .btn-secondary {
            background-color: #e0e0e0;
            color: var(--text-color);
            border: none;
        }
        
        .btn-secondary:hover {
            background-color: #d0d0d0;
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
        
        .cover-container {
            position: relative;
            width: 200px;
            height: 300px;
            margin: 10px auto;
            border-radius: 10px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 1.5em;
        }
        
        .cover-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .upload-btn i {
            color: white;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
        
        .upload-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
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
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <div class="page-header">
            <a href="library.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
            <h1>Add New Manhwa</h1>
        </div>
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="" method="POST" enctype="multipart/form-data" class="magical-form">
                <div class="form-group">
                    <div class="cover-container" id="cover-container">
                        <i class="fas fa-image"></i>
                        <img id="cover_preview" src="#" alt="Cover preview" style="display: none;">
                        <label for="cover_image" class="upload-btn">
                            <i class="fas fa-camera"></i>
                        </label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <label for="cover_image" class="upload-btn">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp" style="display: none;">
                </div>
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                        <option value="Dropped">Dropped</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="reading_status">Your Reading Status</label>
                    <select id="reading_status" name="reading_status" class="form-control">
                        <option value="Plan to Read">Plan to Read</option>
                        <option value="Currently Reading">Currently Reading</option>
                        <option value="Done">Completed</option>
                        <option value="Dropped">Dropped</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="library.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Manhwa</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('cover_image').addEventListener('change', function(e) {
            const preview = document.getElementById('cover_preview');
            const container = document.getElementById('cover-container');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    container.querySelector('i.fas.fa-image').style.display = 'none';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                container.querySelector('i.fas.fa-image').style.display = 'flex';
            }
        });
        
        // Add magical form effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>