<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }

require_once '../../includes/db_connect.php';
$user_id   = $_SESSION['user_id'];
$manhwa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$manhwa_id) { header("Location: library.php"); exit(); }

$q = "SELECT m.*, urs.reading_status, urs.start_reading_date, urs.finish_reading_date, urs.current_chapter
      FROM Manhwas m
      LEFT JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
      WHERE m.manhwa_id = ? AND m.user_id = ?";
$stmt = mysqli_prepare($conn, $q);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $manhwa_id, $user_id);
mysqli_stmt_execute($stmt);
$manhwa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$manhwa) { header("Location: library.php"); exit(); }

$history_stmt = mysqli_prepare($conn, "SELECT * FROM Reread_History WHERE user_id = ? AND manhwa_id = ? ORDER BY start_date DESC");
mysqli_stmt_bind_param($history_stmt, "ii", $user_id, $manhwa_id);
mysqli_stmt_execute($history_stmt);
$history_result = mysqli_stmt_get_result($history_stmt);

// Handle delete
if (isset($_POST['delete'])) {
    $del = mysqli_prepare($conn, "DELETE FROM Manhwas WHERE manhwa_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($del, "ii", $manhwa_id, $user_id);
    mysqli_stmt_execute($del);
    header("Location: library.php"); exit();
}

// Handle update
if (isset($_POST['update'])) {
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $status      = $_POST['status'];
    $genre       = $_POST['genre'];
    $description = trim($_POST['description']);
    $reading_status = $_POST['reading_status'];
    $upd = mysqli_prepare($conn, "UPDATE Manhwas SET title=?, author=?, status=?, genre=?, description=? WHERE manhwa_id=? AND user_id=?");
    mysqli_stmt_bind_param($upd, "sssssii", $title, $author, $status, $genre, $description, $manhwa_id, $user_id);
    mysqli_stmt_execute($upd);
    $upd2 = mysqli_prepare($conn, "UPDATE User_Reading_Status SET reading_status=? WHERE manhwa_id=? AND user_id=?");
    mysqli_stmt_bind_param($upd2, "sii", $reading_status, $manhwa_id, $user_id);
    mysqli_stmt_execute($upd2);
    header("Location: view_manhwa.php?id=" . $manhwa_id); exit();
}

// Handle start reading (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'start_reading') {
    header('Content-Type: application/json');
    $today = date('Y-m-d');
    $upd = mysqli_prepare($conn, "UPDATE User_Reading_Status SET reading_status='Currently Reading', start_reading_date=COALESCE(start_reading_date,?), last_updated=NOW() WHERE manhwa_id=? AND user_id=?");
    mysqli_stmt_bind_param($upd, "sii", $today, $manhwa_id, $user_id);
    echo json_encode(['success' => mysqli_stmt_execute($upd)]);
    exit();
}

function statusColor($s) {
    return ['Ongoing'=>'#a29bfe','Completed'=>'#00b894','Hiatus'=>'#fdcb6e','Dropped'=>'#fd79a8'][$s] ?? '#a29bfe';
}
function readingColor($s) {
    return ['Currently Reading'=>'#a29bfe','Done'=>'#00b894','Plan to Read'=>'#fdcb6e'][$s] ?? '#888';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($manhwa['title']); ?> — The Obscured Index</title>
<link rel="icon" type="image/png" href="../../images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;1,500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0e0a17; color: #fff; }
.container { min-height: 100vh; display: flex; flex-direction: column; }

/* NAV */
header { position: sticky; top: 0; z-index: 100; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; min-height: 72px; padding: 12px 24px; background: rgba(14,10,23,0.9); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
header a.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
header a.logo img { height: 40px; width: 40px; object-fit: contain; filter: drop-shadow(0 0 6px rgba(162,155,254,0.35)); }
header a.logo span { font-family: 'Cinzel', serif; font-weight: 600; font-size: 1.05rem; color: #f5f3fb; }
nav { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
nav a { font-family: 'Cinzel', serif; font-size: 0.8rem; color: rgba(245,243,251,0.75); text-decoration: none; }
nav a:hover { color: #fff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }

/* HERO BANNER */
.hero { position: relative; height: 420px; overflow: hidden; }
.hero-blur { position: absolute; inset: 0; background-size: cover; background-position: center; filter: blur(18px) brightness(0.35); transform: scale(1.1); }
.hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 30%, #0e0a17 100%); }
.hero-back { position: absolute; top: 16px; left: 20px; z-index: 10; font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.04em; color: rgba(201,191,252,0.8); text-decoration: none; transition: color 0.2s; }
.hero-back:hover { color: #c9bffc; }
.hero-dots { position: absolute; top: 12px; right: calc(50% - 450px + 16px); z-index: 10; }
.hero-cover-wrap { position: relative; display: inline-block; }
.hero-cover-wrap .dots-menu { position: absolute; top: 6px; right: -14px; }
.hero-content { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 32px 20px; gap: 16px; text-align: center; }
.hero-cover { width: 160px; height: 260px; flex-shrink: 0; border-radius: 10px; overflow: hidden; background: #241f30; box-shadow: 0 8px 32px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1); }
.hero-cover img { width: 100%; height: 100%; object-fit: cover; }
.hero-cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.2); font-size: 2rem; }
.hero-info { flex: 1; padding-bottom: 4px; }
.hero-info h1 { font-family: 'Playfair Display', serif; font-weight: 500; font-size: 2.4rem; margin: 0 0 8px; color: #fff; text-shadow: 0 2px 12px rgba(0,0,0,0.8); }
.hero-info .author { color: rgba(255,255,255,0.6); font-size: 0.9rem; margin: 0 0 12px; }
.badges { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
.badge { font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 0.05em; padding: 4px 12px; border-radius: 999px; font-weight: 600; }

/* MAIN */
main { flex: 1; width: 100%; max-width: 900px; margin: 0 auto; padding: 28px 24px 60px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: #c9bffc; text-decoration: none; font-size: 0.85rem; margin-bottom: 24px; }

/* ACTION BAR */
.action-bar { display: flex; gap: 8px; align-items: center; justify-content: center; flex-wrap: wrap; margin-bottom: 28px; }
.dots-menu { position: relative; }
.dots-btn { background: transparent; border: 1px solid rgba(255,255,255,0.12); color: rgba(255,255,255,0.5); border-radius: 999px; padding: 7px 12px; font-size: 1rem; cursor: pointer; line-height: 1; transition: all 0.15s; }
.dots-btn:hover { border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.8); }
.dots-dropdown { position: absolute; top: calc(100% + 6px); right: 0; background: #1a1526; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; min-width: 130px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); display: none; flex-direction: column; z-index: 100; overflow: hidden; }
.dots-dropdown.open { display: flex; }
.dots-dropdown button { background: transparent; border: none; padding: 11px 16px; text-align: left; font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.04em; color: rgba(255,255,255,0.65); cursor: pointer; transition: background 0.15s; }
.dots-dropdown button:hover { background: rgba(255,255,255,0.06); color: #fff; }
.dots-dropdown button.danger { color: rgba(255,100,80,0.7); }
.dots-dropdown button.danger:hover { background: rgba(231,76,60,0.1); color: #ff8a80; }
.btn { font-family: 'Cinzel', serif; font-size: 0.68rem; letter-spacing: 0.04em; padding: 7px 14px; border-radius: 999px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-primary { background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; }
.btn-primary:hover { opacity: 0.88; }
.btn-secondary { background: transparent; color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.12); }
.btn-secondary:hover { color: rgba(255,255,255,0.8); border-color: rgba(255,255,255,0.25); }
.btn-danger { background: transparent; color: rgba(255,100,80,0.6); border: 1px solid rgba(231,76,60,0.15); }
.btn-danger:hover { color: #ff8a80; border-color: rgba(231,76,60,0.3); }
.btn-success { background: transparent; color: rgba(0,184,148,0.7); border: 1px solid rgba(0,184,148,0.2); }
.btn-success:hover { color: #00b894; border-color: rgba(0,184,148,0.35); }

/* INFO GRID */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
.info-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 16px 20px; }
.info-card .label { font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 0.07em; color: rgba(255,255,255,0.4); margin-bottom: 6px; }
.info-card .value { font-size: 0.92rem; color: #f0ecfb; }

/* READING STATUS CARD */
.reading-card { background: rgba(162,155,254,0.06); border: 1px solid rgba(162,155,254,0.15); border-radius: 10px; padding: 20px; margin-bottom: 24px; }
.reading-card .rc-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
.reading-card .rc-label { font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.07em; color: rgba(255,255,255,0.5); }
.reading-card .rc-status { font-family: 'Cinzel', serif; font-size: 0.8rem; font-weight: 600; padding: 5px 14px; border-radius: 999px; }
.reading-card .rc-dates { display: flex; gap: 20px; flex-wrap: wrap; }
.reading-card .rc-date { font-size: 0.82rem; color: rgba(255,255,255,0.5); }
.reading-card .rc-date span { color: rgba(255,255,255,0.8); margin-left: 4px; }
.reading-card .rc-chapter { font-size: 0.85rem; color: rgba(255,255,255,0.6); margin-top: 10px; }
.reading-card .rc-chapter span { color: #c9bffc; font-weight: 600; }

/* DESCRIPTION */
.section { margin-bottom: 24px; }
.section h2 { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 0.07em; color: rgba(255,255,255,0.45); margin: 0 0 12px; }
.section p { color: rgba(255,255,255,0.7); line-height: 1.85; font-size: 0.92rem; margin: 0; }
.history-item { padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.07); color: rgba(255,255,255,0.6); font-size: 0.85rem; }
.history-item:last-child { border-bottom: none; }

/* EDIT FORM */
.edit-form { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 28px; margin-top: 24px; display: none; }
.edit-form.show { display: block; }
.edit-form h2 { font-family: 'Playfair Display', serif; color: #fff; font-size: 1.3rem; margin: 0 0 22px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-family: 'Cinzel', serif; font-size: 0.68rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.7); margin-bottom: 6px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.07); color: #fff; font-size: 0.9rem; font-family: inherit; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #a29bfe; }
.form-group select { background: #17111f; }
.form-group textarea { min-height: 100px; resize: vertical; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }

/* MODAL */
.modal { position: fixed; inset: 0; background: rgba(0,0,0,0.65); display: none; align-items: center; justify-content: center; z-index: 200; }
.modal.show { display: flex; }
.modal-content { background: #1a1526; border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 32px; max-width: 380px; width: 90%; text-align: center; }
.modal-content h3 { font-family: 'Playfair Display', serif; color: #fff; margin: 0 0 10px; font-size: 1.3rem; }
.modal-content p { color: rgba(255,255,255,0.6); font-size: 0.88rem; margin: 0 0 24px; line-height: 1.6; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }

footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; color: rgba(245,243,251,0.4); margin: 0; }

@media (max-width: 600px) {
  .hero { height: 240px; }
  .hero-cover { width: 100px; height: 150px; }
  .hero-info h1 { font-size: 1.4rem; }
  .info-grid { grid-template-columns: 1fr; }
  .form-row { grid-template-columns: 1fr; }
  .hero-content { padding: 0 16px 20px; gap: 16px; }
}
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
    <a href="browse.php">BROWSE</a>
    <a href="../../logout.php">LOGOUT</a>
  </nav>
</header>

<!-- HERO -->
<div class="hero">
  <?php 
  $cover = !empty($manhwa['cover_image']) 
    ? (str_starts_with($manhwa['cover_image'], 'http') ? '/IN/api/img_proxy.php?url=' . urlencode($manhwa['cover_image']) : '../../' . htmlspecialchars($manhwa['cover_image']))
    : ''; 
  ?>
  <div class="hero-blur" <?php if ($cover): ?>style="background-image: url('<?php echo $cover; ?>')"<?php endif; ?>></div>
  <div class="hero-overlay"></div>
  <a href="browse.php" class="hero-back">&larr; Back to Browse</a>
  <div class="hero-content">
    <div class="hero-cover-wrap">
      <div class="hero-cover">
        <?php if ($cover): ?>
        <img src="<?php echo $cover; ?>" alt="">
        <?php else: ?>
        <div class="hero-cover-placeholder"></div>
        <?php endif; ?>
      </div>
    </div>
    <div class="hero-info">
      <h1><?php echo htmlspecialchars($manhwa['title']); ?></h1>
      <p class="author"><?php echo htmlspecialchars($manhwa['author'] ?: 'Unknown author'); ?></p>
      <div class="badges">
        <span class="badge" style="background: <?php echo statusColor($manhwa['status']); ?>22; color: <?php echo statusColor($manhwa['status']); ?>; border: 1px solid <?php echo statusColor($manhwa['status']); ?>44;"><?php echo htmlspecialchars($manhwa['status']); ?></span>
        <?php if (!empty($manhwa['genre'])): ?>
        <span class="badge" style="background: rgba(201,191,252,0.1); color: #c9bffc; border: 1px solid rgba(201,191,252,0.2);"><?php echo htmlspecialchars($manhwa['genre']); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<main>
  <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
    <div class="dots-menu">
      <button class="dots-btn" onclick="toggleDots()">&#8943;</button>
      <div class="dots-dropdown" id="dotsDropdown">
        <button onclick="toggleEdit(); toggleDots()">Edit</button>
        <button onclick="toggleModal(); toggleDots()" class="danger">Delete</button>
      </div>
    </div>
  </div>
  <!-- ACTION BAR -->
  <div class="action-bar">
    <?php if ($manhwa['reading_status'] === 'Plan to Read'): ?>
    <button class="btn btn-primary" id="start-btn" onclick="startReading()">START READING</button>
    <?php else: ?>
    <a href="reader.php?id=<?php echo $manhwa_id; ?>" class="btn btn-primary">CONTINUE &mdash; CH. <?php echo (int)$manhwa['current_chapter']; ?></a>
    <?php endif; ?>
  </div>

  <!-- READING STATUS CARD -->
  <div class="reading-card">
    <div class="rc-top">
      <span class="rc-label">YOUR PROGRESS</span>
      <span class="rc-status" id="rc-status-badge" style="background: <?php echo readingColor($manhwa['reading_status']); ?>22; color: <?php echo readingColor($manhwa['reading_status']); ?>; border: 1px solid <?php echo readingColor($manhwa['reading_status']); ?>44;">
        <?php echo htmlspecialchars($manhwa['reading_status'] ?: 'Not set'); ?>
      </span>
    </div>
    <div class="rc-dates">
      <?php if (!empty($manhwa['start_reading_date'])): ?>
      <div class="rc-date">Started<span><?php echo date('M d, Y', strtotime($manhwa['start_reading_date'])); ?></span></div>
      <?php endif; ?>
      <?php if (!empty($manhwa['finish_reading_date'])): ?>
      <div class="rc-date">Finished<span><?php echo date('M d, Y', strtotime($manhwa['finish_reading_date'])); ?></span></div>
      <?php endif; ?>
    </div>
    <?php if (!empty($manhwa['current_chapter'])): ?>
    <div class="rc-chapter">Last read chapter <span><?php echo (int)$manhwa['current_chapter']; ?></span></div>
    <?php endif; ?>
  </div>

  <!-- INFO GRID -->
  <div class="info-grid">
    <div class="info-card">
      <div class="label">AUTHOR</div>
      <div class="value"><?php echo htmlspecialchars($manhwa['author'] ?: 'Unknown'); ?></div>
    </div>
    <div class="info-card">
      <div class="label">ADDED</div>
      <div class="value"><?php echo date('M d, Y', strtotime($manhwa['upload_date'])); ?></div>
    </div>
  </div>

  <!-- DESCRIPTION -->
  <?php if (!empty($manhwa['description'])): ?>
  <div class="section">
    <h2>DESCRIPTION</h2>
    <p><?php echo nl2br(htmlspecialchars($manhwa['description'])); ?></p>
  </div>
  <?php endif; ?>

  <!-- REREAD HISTORY -->
  <?php if (mysqli_num_rows($history_result) > 0): ?>
  <div class="section">
    <h2>REREAD HISTORY</h2>
    <?php while ($h = mysqli_fetch_assoc($history_result)): ?>
    <div class="history-item">
      Started <?php echo date('M d, Y', strtotime($h['start_date'])); ?>
      <?php if (!empty($h['finish_date'])): ?> &mdash; Finished <?php echo date('M d, Y', strtotime($h['finish_date'])); ?><?php endif; ?>
    </div>
    <?php endwhile; ?>
  </div>
  <?php endif; ?>

  <!-- EDIT FORM -->
  <div class="edit-form" id="editForm">
    <h2>Edit Manhwa</h2>
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>TITLE</label>
          <input type="text" name="title" value="<?php echo htmlspecialchars($manhwa['title']); ?>">
        </div>
        <div class="form-group">
          <label>AUTHOR</label>
          <input type="text" name="author" value="<?php echo htmlspecialchars($manhwa['author']); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>STATUS</label>
          <select name="status">
            <?php foreach (['Ongoing','Completed','Hiatus','Dropped'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $manhwa['status'] == $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>GENRE</label>
          <select name="genre">
            <?php foreach (['Action','Adventure','BL','Comedy','Drama','Fantasy','GL','Harem','Historical','Horror','Isekai','Martial Arts','Mecha','Military','Mystery','No Romance','Psychological','Romance','Sci-Fi','Slice of Life','Sports','Straight','Supernatural','Thriller','Tragedy','Villainess','Wuxia'] as $g): ?>
            <option value="<?php echo $g; ?>" <?php echo $manhwa['genre'] == $g ? 'selected' : ''; ?>><?php echo $g; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>READING STATUS</label>
        <select name="reading_status">
          <?php foreach (['Plan to Read','Currently Reading','Done'] as $rs): ?>
          <option value="<?php echo $rs; ?>" <?php echo $manhwa['reading_status'] == $rs ? 'selected' : ''; ?>><?php echo $rs; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>DESCRIPTION</label>
        <textarea name="description"><?php echo htmlspecialchars($manhwa['description']); ?></textarea>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="toggleEdit()">CANCEL</button>
        <button type="submit" name="update" class="btn btn-primary">SAVE CHANGES</button>
      </div>
    </form>
  </div>
</main>

<!-- DELETE MODAL -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <h3>Banish this scroll?</h3>
    <p>"<?php echo htmlspecialchars($manhwa['title']); ?>" will vanish from your index — no takebacks, no epilogue.</p>
    <div class="modal-actions">
      <form method="POST">
        <button type="button" class="btn btn-secondary" onclick="toggleModal()">CANCEL</button>
        <button type="submit" name="delete" class="btn btn-danger" style="margin-left:10px;">DELETE</button>
      </form>
    </div>
  </div>
</div>

<footer>
  <p>&copy; <?php echo date('Y'); ?> — The Obscured Index. All rights reserved.</p>
</footer>
</div>

<script>
function toggleEdit() { document.getElementById('editForm').classList.toggle('show'); }
function toggleModal() { document.getElementById('deleteModal').classList.toggle('show'); }
function toggleDots() { document.getElementById('dotsDropdown').classList.toggle('open'); }
document.addEventListener('click', e => {
  const menu = document.querySelector('.dots-menu');
  if (menu && !menu.contains(e.target)) document.getElementById('dotsDropdown').classList.remove('open');
});

function startReading() {
  fetch('view_manhwa.php?id=<?php echo $manhwa_id; ?>', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=start_reading'
  })
  .then(r => r.json())
  .then(d => { if (d.success) window.location.href = 'reader.php?id=<?php echo $manhwa_id; ?>'; });
}
</script>
</body>
</html>
