<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }

require_once '../../includes/db_connect.php';
$user_id = $_SESSION['user_id'];

$title       = trim($_GET['title'] ?? '');
$author      = trim($_GET['author'] ?? '');
$description = trim($_GET['description'] ?? '');
$status      = trim($_GET['status'] ?? '');
$cover       = trim($_GET['cover'] ?? '');
$mangadex_id = trim($_GET['id'] ?? '');
$genre       = trim($_GET['genre'] ?? 'BL');
$source      = trim($_GET['source'] ?? 'mangadex');
$comick_hid  = $source === 'comick' ? $mangadex_id : '';
if ($source === 'comick') $mangadex_id = '';

if (!$title) { header("Location: browse.php"); exit(); }

// Handle direct add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover      = trim($_POST['cover'] ?? '');
    $mangadex_id = trim($_POST['mangadex_id'] ?? '');
    $comick_hid  = trim($_POST['comick_hid'] ?? '');
    $title      = trim($_POST['title'] ?? '');
    $author     = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status     = trim($_POST['status'] ?? '');
    $genre = $_POST['genre'] ?? 'BL';
    $cover_image = '';
    if ($cover) {
        $url = filter_var(preg_replace('/\.\d+\.jpg$/', '', $cover), FILTER_VALIDATE_URL);
        if ($url) $cover_image = $url;
    }
    $ins = mysqli_prepare($conn, "INSERT INTO Manhwas (user_id, title, author, status, genre, description, cover_image, mangadex_id, comick_hid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, "issssssss", $user_id, $title, $author, $status, $genre, $description, $cover_image, $mangadex_id, $comick_hid);
    mysqli_stmt_execute($ins);
    $manhwa_id = mysqli_insert_id($conn);
    $urs = mysqli_prepare($conn, "INSERT INTO User_Reading_Status (user_id, manhwa_id, reading_status) VALUES (?, ?, 'Plan to Read')");
    mysqli_stmt_bind_param($urs, "ii", $user_id, $manhwa_id);
    mysqli_stmt_execute($urs);
    header("Location: view_manhwa.php?id=" . $manhwa_id);
    exit();
}

function statusColor($s) {
    return ['Ongoing'=>'#a29bfe','Completed'=>'#00b894','Hiatus'=>'#fdcb6e','Dropped'=>'#fd79a8'][$s] ?? '#a29bfe';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($title); ?> — The Obscured Index</title>
<link rel="icon" type="image/png" href="../../images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #0e0a17; color: #fff; }
.container { min-height: 100vh; display: flex; flex-direction: column; }
header { position: sticky; top: 0; z-index: 100; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; min-height: 72px; padding: 12px 24px; background: rgba(14,10,23,0.9); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
header a.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
header a.logo img { height: 40px; width: 40px; object-fit: contain; filter: drop-shadow(0 0 6px rgba(162,155,254,0.35)); }
header a.logo span { font-family: 'Cinzel', serif; font-weight: 600; font-size: 1.05rem; color: #f5f3fb; }
nav { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
nav a { font-family: 'Cinzel', serif; font-size: 0.8rem; color: rgba(245,243,251,0.75); text-decoration: none; }
nav a:hover { color: #fff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }
.hero { position: relative; height: 420px; overflow: hidden; }
.hero-blur { position: absolute; inset: 0; background-size: cover; background-position: center; filter: blur(18px) brightness(0.35); transform: scale(1.1); }
.hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 30%, #0e0a17 100%); }
.hero-content { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 32px 20px; gap: 16px; text-align: center; }
.hero-cover { width: 160px; height: 260px; flex-shrink: 0; border-radius: 10px; overflow: hidden; background: #241f30; box-shadow: 0 8px 32px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.1); }
.hero-cover img { width: 100%; height: 100%; object-fit: cover; }
.hero-info h1 { font-family: 'Playfair Display', serif; font-weight: 500; font-size: 2.4rem; margin: 0 0 8px; text-shadow: 0 2px 12px rgba(0,0,0,0.8); }
.hero-info .author { color: rgba(255,255,255,0.6); font-size: 0.9rem; margin: 0 0 12px; }
.badge { font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 0.05em; padding: 4px 12px; border-radius: 999px; font-weight: 600; }
main { flex: 1; width: 100%; max-width: 900px; margin: 0 auto; padding: 28px 24px 60px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: #c9bffc; text-decoration: none; font-size: 0.85rem; margin-bottom: 24px; }
.action-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 28px; }
.btn { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.04em; padding: 10px 20px; border-radius: 999px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-primary { background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; box-shadow: 0 4px 16px rgba(138,43,226,0.35); }
.btn-secondary { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.8); border: 1px solid rgba(255,255,255,0.15); }
.notice { background: rgba(162,155,254,0.07); border: 1px solid rgba(162,155,254,0.2); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: rgba(255,255,255,0.55); font-size: 0.85rem; }
.section h2 { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 0.07em; color: rgba(255,255,255,0.45); margin: 0 0 12px; }
.section p { color: rgba(255,255,255,0.7); line-height: 1.85; font-size: 0.92rem; margin: 0; }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; color: rgba(245,243,251,0.4); margin: 0; }
@media (max-width: 600px) {
  .hero { height: 240px; }
  .hero-cover { width: 100px; height: 150px; }
  .hero-info h1 { font-size: 1.4rem; }
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

<div class="hero">
  <?php if ($cover): ?>
  <div class="hero-blur" style="background-image: url('<?php echo htmlspecialchars($cover); ?>')"></div>
  <?php endif; ?>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-cover">
      <?php if ($cover): ?>
      <img src="<?php echo htmlspecialchars($cover); ?>" alt="" onerror="this.style.display='none'">
      <?php endif; ?>
    </div>
    <div class="hero-info">
      <h1><?php echo htmlspecialchars($title); ?></h1>
      <?php if ($author): ?><p class="author"><?php echo htmlspecialchars($author); ?></p><?php endif; ?>
      <?php if ($status): ?>
      <?php $c = statusColor($status); ?>
      <span class="badge" style="background:<?php echo $c; ?>22;color:<?php echo $c; ?>;border:1px solid <?php echo $c; ?>44;"><?php echo htmlspecialchars($status); ?></span>
      <?php endif; ?>
    </div>
  </div>
</div>

<main>
  <a href="browse.php" class="back-link">&larr; Back to Browse</a>

  <div class="action-bar" style="justify-content:center;flex-direction:column;align-items:center;gap:12px;">
    <form method="POST" style="display:flex;flex-direction:column;align-items:center;gap:10px;">
      <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
      <input type="hidden" name="author" value="<?php echo htmlspecialchars($author); ?>">
      <input type="hidden" name="description" value="<?php echo htmlspecialchars($description); ?>">
      <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
      <input type="hidden" name="cover" value="<?php echo htmlspecialchars($cover); ?>">
      <input type="hidden" name="mangadex_id" value="<?php echo htmlspecialchars($mangadex_id); ?>">
      <input type="hidden" name="comick_hid" value="<?php echo htmlspecialchars($comick_hid); ?>">
      <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
      <button type="submit" class="btn btn-primary">+ ADD TO LIBRARY</button>
    </form>
  </div>

  <div class="notice">This title is not in your library yet. Add it to start tracking your progress.</div>

  <?php if ($description): ?>
  <div class="section">
    <h2>DESCRIPTION</h2>
    <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
  </div>
  <?php endif; ?>
</main>

<footer>
  <p>&copy; <?php echo date('Y'); ?> — The Obscured Index. All rights reserved.</p>
</footer>
</div>
</body>
</html>
