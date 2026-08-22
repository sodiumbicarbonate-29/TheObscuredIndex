<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $status = $_POST['status'];
    $genre = $_POST['genre'];
    $reading_status = $_POST['reading_status'];
    $reading_link = trim($_POST['reading_link']);
    $description = trim($_POST['description']);
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $finish_date = !empty($_POST['finish_date']) ? $_POST['finish_date'] : null;
    
    if (empty($title)) {
        $error = "Title is required";
    } else {
        // Check if already exists
        $check = mysqli_prepare($conn, "SELECT manhwa_id FROM Manhwas WHERE title = ? AND user_id = ?");
        mysqli_stmt_bind_param($check, "si", $title, $user_id);
        mysqli_stmt_execute($check);
        if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
            $error = "This manhwa already exists in your library";
        } else {
            // Handle cover upload
            $cover_image = "";
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
                $upload_dir = "../../uploads/covers/";
                $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $upload_dir . $filename)) {
                    $cover_image = "uploads/covers/" . $filename;
                }
            }
            
            // Insert manhwa
            $insert = mysqli_prepare($conn, "INSERT INTO Manhwas (user_id, title, author, status, genre, description, cover_image, reading_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert, "isssssss", $user_id, $title, $author, $status, $genre, $description, $cover_image, $reading_link);
            
            if (mysqli_stmt_execute($insert)) {
                $manhwa_id = mysqli_insert_id($conn);
                
                // Insert reading status
                $status_insert = mysqli_prepare($conn, "INSERT INTO User_Reading_Status (user_id, manhwa_id, reading_status, start_reading_date, finish_reading_date) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($status_insert, "iisss", $user_id, $manhwa_id, $reading_status, $start_date, $finish_date);
                mysqli_stmt_execute($status_insert);
                
                header("Location: library.php");
                exit();
            } else {
                $error = "Failed to add manhwa";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Manhwa - The Obscured Index</title>
<link rel="icon" type="image/png" href="../../images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { min-height: 100vh; display: flex; flex-direction: column; background: #0e0a17; }
header { position: sticky; top: 0; z-index: 100; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; min-height: 72px; padding: 12px 24px; background: rgba(14, 10, 23, 0.85); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
header a.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
header a.logo img { height: 40px; width: 40px; object-fit: contain; filter: drop-shadow(0 0 6px rgba(162,155,254,0.35)); }
header a.logo span { font-family: 'Cinzel', serif; font-weight: 600; font-size: 1.05rem; color: #f5f3fb; letter-spacing: 0.02em; }
nav { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
nav a { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 0.04em; color: rgba(245,243,251,0.75); text-decoration: none; }
nav a:hover { color: #ffffff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }
main { flex: 1; width: 100%; max-width: 640px; margin: 0 auto; padding: 36px 24px 60px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: #c9bffc; text-decoration: none; font-size: 0.85rem; margin-bottom: 14px; }
h1 { font-family: 'Playfair Display', serif; font-weight: 500; color: #ffffff; font-size: 1.9rem; margin: 0 0 26px; }
.form-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 32px; }
.cover-upload { display: flex; justify-content: center; margin-bottom: 26px; }
.cover-upload label { width: 160px; height: 240px; border: 2px dashed rgba(255,255,255,0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: rgba(255,255,255,0.5); font-size: 0.85rem; text-align: center; background-size: cover; background-position: center; }
.cover-upload input { display: none; }
.form-group { margin-bottom: 18px; }
.form-group label { display: block; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.8); margin-bottom: 6px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.9rem; font-family: inherit; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #a29bfe; background: rgba(255,255,255,0.14); }
.form-group select { background: #17111f; }
.form-group select option { background: #17111f; }
.form-group textarea { min-height: 120px; resize: vertical; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-actions { display: flex; gap: 14px; justify-content: center; margin-top: 24px; }
.btn-cancel { text-align: center; text-decoration: none; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: rgba(255,255,255,0.75); border: 1px solid rgba(255,255,255,0.25); padding: 13px 28px; border-radius: 999px; font-weight: 600; background: transparent; cursor: pointer; }
.btn-submit { text-align: center; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: #17111f; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 13px 28px; border-radius: 999px; font-weight: 600; border: none; cursor: pointer; box-shadow: 0 8px 24px rgba(138,43,226,0.4); }
.error { background: rgba(231, 76, 60, 0.2); border: 1px solid rgba(231, 76, 60, 0.5); color: #ff6b6b; padding: 12px; border-radius: 8px; margin-bottom: 18px; text-align: center; }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(245,243,251,0.5); margin: 0; }
@media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="container">
  <header>
    <a href="home.php" class="logo">
      <img src="../../images/logo3.png" alt="Logo">
      <span>The Obscured Index</span>
    </a>
    <nav>
      <a href="home.php">HOME</a>
      <a href="library.php">LIBRARY</a>
      <a href="add_manhwa.php" class="btn">+ ADD NEW</a>
      <a href="../../logout.php">LOGOUT</a>
    </nav>
  </header>

  <main>
    <a href="library.php" class="back-link">&larr; Back to Library</a>
    <h1>Add a New Manhwa</h1>

    <?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST" enctype="multipart/form-data">
        <div class="cover-upload">
          <label id="cover-label">
            <span>Drop cover art</span>
            <input type="file" name="cover" accept="image/*" onchange="previewCover(this)">
          </label>
        </div>

        <div class="form-group">
          <label>TITLE</label>
          <input type="text" name="title" placeholder="Enter manhwa title" required>
        </div>

        <div class="form-group">
          <label>AUTHOR</label>
          <input type="text" name="author" placeholder="Enter author's name">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>STATUS</label>
            <select name="status">
              <option value="Ongoing">Ongoing</option>
              <option value="Completed">Completed</option>
              <option value="Hiatus">Hiatus</option>
              <option value="Dropped">Dropped</option>
            </select>
          </div>
          <div class="form-group">
            <label>GENRE</label>
            <select name="genre">
              <option value="BL">BL</option>
              <option value="Straight">Straight</option>
              <option value="No Romance">No Romance</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>YOUR READING STATUS</label>
          <select name="reading_status" id="reading_status" onchange="toggleDates()">
            <option value="Plan to Read">Plan to Read</option>
            <option value="Currently Reading">Currently Reading</option>
            <option value="Done">Done</option>
          </select>
        </div>

        <div class="form-group" id="start_date_group" style="display: none;">
          <label>START READING DATE</label>
          <input type="date" name="start_date">
        </div>

        <div class="form-group" id="finish_date_group" style="display: none;">
          <label>FINISH READING DATE</label>
          <input type="date" name="finish_date">
        </div>

        <div class="form-group">
          <label>READING LINK</label>
          <input type="url" name="reading_link" placeholder="https://example.com/read/manhwa-title">
        </div>

        <div class="form-group">
          <label>DESCRIPTION</label>
          <textarea name="description" placeholder="Enter the manhwa's description here..."></textarea>
        </div>

        <div class="form-actions">
          <a href="library.php" class="btn-cancel">CANCEL</a>
          <button type="submit" class="btn-submit">ADD MANHWA</button>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> — The Obscured Index. All rights reserved.</p>
  </footer>
</div>

<script>
function previewCover(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const label = document.getElementById('cover-label');
      label.style.backgroundImage = 'url(' + e.target.result + ')';
      label.querySelector('span').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function toggleDates() {
  const status = document.getElementById('reading_status').value;
  document.getElementById('start_date_group').style.display = (status === 'Currently Reading' || status === 'Done') ? 'block' : 'none';
  document.getElementById('finish_date_group').style.display = status === 'Done' ? 'block' : 'none';
}
</script>
</body>
</html>
