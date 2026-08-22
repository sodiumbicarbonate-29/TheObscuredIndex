<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$manhwa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$manhwa_id) {
    header("Location: library.php");
    exit();
}

// Get manhwa details
$query = "SELECT m.*, urs.reading_status, urs.start_reading_date, urs.finish_reading_date 
          FROM Manhwas m 
          LEFT JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
          WHERE m.manhwa_id = ? AND m.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $manhwa_id, $user_id);
mysqli_stmt_execute($stmt);
$manhwa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$manhwa) {
    header("Location: library.php");
    exit();
}

// Get reread history
$history_query = "SELECT * FROM Reread_History WHERE user_id = ? AND manhwa_id = ? ORDER BY start_date DESC";
$history_stmt = mysqli_prepare($conn, $history_query);
mysqli_stmt_bind_param($history_stmt, "ii", $user_id, $manhwa_id);
mysqli_stmt_execute($history_stmt);
$history_result = mysqli_stmt_get_result($history_stmt);

// Handle delete
if (isset($_POST['delete'])) {
    $del = mysqli_prepare($conn, "DELETE FROM Manhwas WHERE manhwa_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($del, "ii", $manhwa_id, $user_id);
    mysqli_stmt_execute($del);
    header("Location: library.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $status = $_POST['status'];
    $genre = $_POST['genre'];
    $description = trim($_POST['description']);
    $reading_link = trim($_POST['reading_link']);
    $reading_status = $_POST['reading_status'];
    
    $update = mysqli_prepare($conn, "UPDATE Manhwas SET title=?, author=?, status=?, genre=?, description=?, reading_link=? WHERE manhwa_id=? AND user_id=?");
    mysqli_stmt_bind_param($update, "ssssssii", $title, $author, $status, $genre, $description, $reading_link, $manhwa_id, $user_id);
    mysqli_stmt_execute($update);
    
    $status_update = mysqli_prepare($conn, "UPDATE User_Reading_Status SET reading_status=? WHERE manhwa_id=? AND user_id=?");
    mysqli_stmt_bind_param($status_update, "sii", $reading_status, $manhwa_id, $user_id);
    mysqli_stmt_execute($status_update);
    
    header("Location: view_manhwa.php?id=" . $manhwa_id);
    exit();
}

function getStatusColor($status) {
    $colors = ['Ongoing' => '#a29bfe', 'Completed' => '#00b894', 'Hiatus' => '#fdcb6e', 'Dropped' => '#fd79a8'];
    return $colors[$status] ?? '#a29bfe';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($manhwa['title']); ?> - The Obscured Index</title>
<link rel="icon" type="image/png" href="../../images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { min-height: 100vh; display: flex; flex-direction: column; background: #0e0a17; }
header { position: sticky; top: 0; z-index: 100; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; min-height: 72px; padding: 12px 24px; background: rgba(14, 10, 23, 0.85); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
header a.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
header a.logo img { height: 40px; width: 40px; object-fit: contain; }
header a.logo span { font-family: 'Cinzel', serif; font-weight: 600; font-size: 1.05rem; color: #f5f3fb; }
nav { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
nav a { font-family: 'Cinzel', serif; font-size: 0.8rem; color: rgba(245,243,251,0.75); text-decoration: none; }
nav a:hover { color: #ffffff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }
main { flex: 1; width: 100%; max-width: 900px; margin: 0 auto; padding: 32px 24px 60px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: #c9bffc; text-decoration: none; font-size: 0.85rem; margin-bottom: 18px; }
.card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; }
.card-header { display: flex; flex-wrap: wrap; gap: 28px; padding: 30px; background: linear-gradient(120deg, rgba(162,155,254,0.08), rgba(138,43,226,0.04)); }
.cover { width: 200px; height: 300px; flex-shrink: 0; border-radius: 8px; overflow: hidden; background: #241f30; }
.cover img { width: 100%; height: 100%; object-fit: cover; }
.details { flex: 1; min-width: 240px; }
.details h1 { font-family: 'Playfair Display', serif; font-weight: 500; color: #fff; font-size: 1.9rem; margin: 0 0 16px; }
.meta { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; font-size: 0.88rem; }
.meta-row { display: flex; }
.meta-label { color: rgba(255,255,255,0.5); width: 90px; }
.meta-value { color: #f0ecfb; }
.status-badge { background: #a29bfe; color: #17111f; padding: 3px 12px; border-radius: 999px; font-weight: 600; font-size: 0.75rem; }
.reading-box { background: rgba(255,255,255,0.05); border-radius: 4px; padding: 16px; margin-bottom: 20px; }
.reading-box h3 { font-family: 'Cinzel', serif; font-size: 0.85rem; color: #c9bffc; margin: 0 0 10px; }
.reading-box p { margin: 0 0 6px; color: #f0ecfb; font-size: 0.9rem; }
.reading-box .date { color: rgba(255,255,255,0.5); font-size: 0.8rem; }
.btn-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
.btn-read { text-decoration: none; font-family: 'Cinzel', serif; font-size: 0.72rem; background: linear-gradient(135deg, #a29bfe, #6c5ce7); color: #17111f; padding: 9px 16px; border-radius: 999px; font-weight: 600; }
.btn-edit { font-family: 'Cinzel', serif; font-size: 0.75rem; background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; border: none; padding: 10px 18px; border-radius: 999px; font-weight: 600; cursor: pointer; }
.btn-delete { font-family: 'Cinzel', serif; font-size: 0.75rem; background: rgba(231,76,60,0.15); color: #ff8a80; border: 1px solid rgba(231,76,60,0.4); padding: 10px 18px; border-radius: 999px; font-weight: 600; cursor: pointer; }
.card-body { padding: 28px 30px; }
.card-body h2 { font-family: 'Cinzel', serif; color: #f0ecfb; font-size: 1rem; margin: 0 0 14px; }
.card-body p { color: rgba(255,255,255,0.72); line-height: 1.8; margin: 0; font-size: 0.92rem; }
.history-item { border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 10px; color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.edit-form { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 28px; margin-top: 20px; display: none; }
.edit-form.show { display: block; }
.edit-form h2 { font-family: 'Playfair Display', serif; color: #fff; font-size: 1.3rem; margin: 0 0 20px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-family: 'Cinzel', serif; font-size: 0.7rem; color: rgba(255,255,255,0.8); margin-bottom: 6px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; box-sizing: border-box; padding: 11px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.9rem; font-family: inherit; }
.form-group select { background: #17111f; }
.form-group textarea { min-height: 100px; resize: vertical; }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 16px; }
.btn-cancel { font-family: 'Cinzel', serif; font-size: 0.78rem; background: transparent; color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.25); padding: 10px 20px; border-radius: 999px; font-weight: 600; cursor: pointer; }
.btn-save { font-family: 'Cinzel', serif; font-size: 0.78rem; background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; border: none; padding: 10px 22px; border-radius: 999px; font-weight: 600; cursor: pointer; }
.modal { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 200; }
.modal.show { display: flex; }
.modal-content { background: #1a1526; border: 1px solid rgba(255,255,255,0.14); border-radius: 4px; padding: 30px; max-width: 380px; width: 90%; text-align: center; }
.modal-content h3 { font-family: 'Playfair Display', serif; color: #fff; margin: 0 0 12px; }
.modal-content p { color: rgba(255,255,255,0.65); font-size: 0.9rem; margin: 0 0 22px; }
.btn-confirm-delete { text-decoration: none; font-family: 'Cinzel', serif; font-size: 0.78rem; background: #e74c3c; color: #fff; padding: 10px 20px; border-radius: 999px; font-weight: 600; border: none; cursor: pointer; }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; color: rgba(245,243,251,0.5); margin: 0; }
@media (max-width: 600px) { .cover { width: 150px; height: 225px; } .details h1 { font-size: 1.5rem; } }
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

    <div class="card">
      <div class="card-header">
        <div class="cover">
          <?php if (!empty($manhwa['cover_image'])): ?>
          <img src="../../<?php echo htmlspecialchars($manhwa['cover_image']); ?>" alt="">
          <?php endif; ?>
        </div>

        <div class="details">
          <h1><?php echo htmlspecialchars($manhwa['title']); ?></h1>

          <div class="meta">
            <div class="meta-row"><span class="meta-label">Author</span><span class="meta-value"><?php echo htmlspecialchars($manhwa['author'] ?: 'Unknown'); ?></span></div>
            <div class="meta-row"><span class="meta-label">Status</span><span class="status-badge" style="background: <?php echo getStatusColor($manhwa['status']); ?>"><?php echo htmlspecialchars($manhwa['status']); ?></span></div>
            <div class="meta-row"><span class="meta-label">Genre</span><span class="meta-value" style="color: #c9bffc; font-style: italic;"><?php echo htmlspecialchars($manhwa['genre'] ?: 'No genre'); ?></span></div>
            <div class="meta-row"><span class="meta-label">Added</span><span class="meta-value"><?php echo date('M d, Y', strtotime($manhwa['upload_date'])); ?></span></div>
          </div>

          <div class="reading-box">
            <h3>YOUR READING STATUS</h3>
            <p><?php echo htmlspecialchars($manhwa['reading_status'] ?: 'Not set'); ?></p>
            <?php if (!empty($manhwa['start_reading_date'])): ?>
            <p class="date">Started: <?php echo date('M d, Y', strtotime($manhwa['start_reading_date'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($manhwa['finish_reading_date'])): ?>
            <p class="date">Finished: <?php echo date('M d, Y', strtotime($manhwa['finish_reading_date'])); ?></p>
            <?php endif; ?>
            <div class="btn-group">
              <?php if (!empty($manhwa['reading_link'])): ?>
              <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="btn-read">READ NOW</a>
              <?php endif; ?>
            </div>
          </div>

          <div class="btn-group">
            <button class="btn-edit" onclick="toggleEdit()">EDIT DETAILS</button>
            <button class="btn-delete" onclick="toggleModal()">DELETE</button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <?php if (mysqli_num_rows($history_result) > 0): ?>
        <h2>Rereading History</h2>
        <?php while ($h = mysqli_fetch_assoc($history_result)): ?>
        <div class="history-item">
          Started: <?php echo date('M d, Y', strtotime($h['start_date'])); ?>
          <?php if (!empty($h['finish_date'])): ?> — Finished: <?php echo date('M d, Y', strtotime($h['finish_date'])); ?><?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php endif; ?>

        <?php if (!empty($manhwa['description'])): ?>
        <h2>Description</h2>
        <p><?php echo nl2br(htmlspecialchars($manhwa['description'])); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <form method="POST" class="edit-form" id="editForm">
      <h2>Edit Manhwa</h2>
      <div class="form-group">
        <label>TITLE</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($manhwa['title']); ?>">
      </div>
      <div class="form-group">
        <label>AUTHOR</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($manhwa['author']); ?>">
      </div>
      <div class="form-group">
        <label>STATUS</label>
        <select name="status">
          <option value="Ongoing" <?php echo $manhwa['status'] == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
          <option value="Completed" <?php echo $manhwa['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
          <option value="Hiatus" <?php echo $manhwa['status'] == 'Hiatus' ? 'selected' : ''; ?>>Hiatus</option>
          <option value="Dropped" <?php echo $manhwa['status'] == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
        </select>
      </div>
      <div class="form-group">
        <label>GENRE</label>
        <select name="genre">
          <option value="BL" <?php echo $manhwa['genre'] == 'BL' ? 'selected' : ''; ?>>BL</option>
          <option value="Straight" <?php echo $manhwa['genre'] == 'Straight' ? 'selected' : ''; ?>>Straight</option>
          <option value="No Romance" <?php echo $manhwa['genre'] == 'No Romance' ? 'selected' : ''; ?>>No Romance</option>
        </select>
      </div>
      <div class="form-group">
        <label>READING STATUS</label>
        <select name="reading_status">
          <option value="Plan to Read" <?php echo $manhwa['reading_status'] == 'Plan to Read' ? 'selected' : ''; ?>>Plan to Read</option>
          <option value="Currently Reading" <?php echo $manhwa['reading_status'] == 'Currently Reading' ? 'selected' : ''; ?>>Currently Reading</option>
          <option value="Done" <?php echo $manhwa['reading_status'] == 'Done' ? 'selected' : ''; ?>>Done</option>
        </select>
      </div>
      <div class="form-group">
        <label>READING LINK</label>
        <input type="url" name="reading_link" value="<?php echo htmlspecialchars($manhwa['reading_link']); ?>">
      </div>
      <div class="form-group">
        <label>DESCRIPTION</label>
        <textarea name="description"><?php echo htmlspecialchars($manhwa['description']); ?></textarea>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="toggleEdit()">CANCEL</button>
        <button type="submit" name="update" class="btn-save">SAVE CHANGES</button>
      </div>
    </form>
  </main>

  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <h3>Banish this scroll forever?</h3>
      <p>"<?php echo htmlspecialchars($manhwa['title']); ?>" will vanish into the obscured index — no takebacks, no epilogue, no redemption arc.</p>
      <form method="POST" style="display: flex; gap: 12px; justify-content: center;">
        <button type="button" class="btn-cancel" onclick="toggleModal()">CANCEL</button>
        <button type="submit" name="delete" class="btn-confirm-delete">DELETE</button>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> — The Obscured Index. All rights reserved.</p>
  </footer>
</div>

<script>
function toggleEdit() {
  document.getElementById('editForm').classList.toggle('show');
}
function toggleModal() {
  document.getElementById('deleteModal').classList.toggle('show');
}
</script>
</body>
</html>
