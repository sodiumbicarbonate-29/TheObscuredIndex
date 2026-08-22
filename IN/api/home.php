<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get stats
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Currently Reading') as reading_count,
    (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Done') as completed_count,
    (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Plan to Read') as plan_count,
    (SELECT COUNT(*) FROM Manhwas WHERE user_id = ?) as total_count";
$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, "iiii", $user_id, $user_id, $user_id, $user_id);
mysqli_stmt_execute($stats_stmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stats_stmt));

// Get currently reading
$reading_query = "SELECT m.*, urs.reading_status, urs.start_reading_date 
    FROM Manhwas m 
    JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
    WHERE m.user_id = ? AND urs.reading_status = 'Currently Reading'
    ORDER BY urs.last_updated DESC LIMIT 8";
$reading_stmt = mysqli_prepare($conn, $reading_query);
mysqli_stmt_bind_param($reading_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($reading_stmt);
$reading_result = mysqli_stmt_get_result($reading_stmt);

// Get plan to read
$plan_query = "SELECT m.*, urs.reading_status 
    FROM Manhwas m 
    JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
    WHERE m.user_id = ? AND urs.reading_status = 'Plan to Read'
    ORDER BY urs.last_updated DESC LIMIT 6";
$plan_stmt = mysqli_prepare($conn, $plan_query);
mysqli_stmt_bind_param($plan_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($plan_stmt);
$plan_result = mysqli_stmt_get_result($plan_stmt);

// Get completed
$completed_query = "SELECT m.*, urs.reading_status, urs.finish_reading_date 
    FROM Manhwas m 
    JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
    WHERE m.user_id = ? AND urs.reading_status = 'Done'
    ORDER BY urs.finish_reading_date DESC LIMIT 6";
$completed_stmt = mysqli_prepare($conn, $completed_query);
mysqli_stmt_bind_param($completed_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($completed_stmt);
$completed_result = mysqli_stmt_get_result($completed_stmt);

// Get genres
$genres = ['All', 'BL', 'Straight', 'No Romance'];

// Welcome quotes
$quotes = [
    "Oh no. You again. At this hour?",
    "Back for more forbidden lore, I see.",
    "The shelves missed your nonsense.",
    "Curiosity didn't kill this reader—yet.",
    "Another chapter, another questionable decision.",
];
$welcomeQuote = $quotes[array_rand($quotes)];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'start_reading' && isset($_POST['manhwa_id'])) {
        $manhwa_id = (int)$_POST['manhwa_id'];
        $current_date = date('Y-m-d');
        $update = "UPDATE User_Reading_Status SET reading_status = 'Currently Reading', start_reading_date = ?, last_updated = NOW() WHERE user_id = ? AND manhwa_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "sii", $current_date, $user_id, $manhwa_id);
        echo json_encode(['success' => mysqli_stmt_execute($stmt)]);
        exit();
    }
    
    if ($_POST['action'] == 'mark_done' && isset($_POST['manhwa_id'])) {
        $manhwa_id = (int)$_POST['manhwa_id'];
        $current_date = date('Y-m-d');
        $update = "UPDATE User_Reading_Status SET reading_status = 'Done', finish_reading_date = ?, last_updated = NOW() WHERE user_id = ? AND manhwa_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "sii", $current_date, $user_id, $manhwa_id);
        echo json_encode(['success' => mysqli_stmt_execute($stmt)]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - The Obscured Index</title>
<link rel="icon" type="image/png" href="../../images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
@keyframes twinkle { 0%,100% { opacity: 0; transform: scale(0.4); } 50% { opacity: 1; transform: scale(1); } }
.container { min-height: 100vh; display: flex; flex-direction: column; background: #0e0a17; }
header { position: sticky; top: 0; z-index: 100; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; min-height: 72px; padding: 12px 24px; background: rgba(14, 10, 23, 0.85); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
header a.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
header a.logo img { height: 40px; width: 40px; object-fit: contain; filter: drop-shadow(0 0 6px rgba(162,155,254,0.35)); }
header a.logo span { font-family: 'Cinzel', serif; font-weight: 600; font-size: 1.05rem; color: #f5f3fb; letter-spacing: 0.02em; }
nav { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
nav a { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 0.04em; color: rgba(245,243,251,0.75); text-decoration: none; }
nav a:hover { color: #ffffff; }
nav a.active { color: #ffffff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }
.hero { position: relative; height: 340px; overflow: hidden; }
.hero img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: bottom; }
.hero .overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(14,10,23,0.35) 0%, rgba(14,10,23,0.85) 100%); }
.hero .star { position: absolute; width: 4px; height: 4px; border-radius: 50%; background: #fff; box-shadow: 0 0 6px 2px rgba(255,255,255,0.7); }
.hero .quote { position: absolute; bottom: 24px; left: 50%; transform: translateX(-50%); max-width: 640px; text-align: center; padding: 14px 28px; background: rgba(20,15,32,0.55); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.14); border-radius: 999px; }
.hero .quote p { margin: 0; font-family: 'Playfair Display', serif; font-style: italic; color: #f0ecfb; font-size: 1rem; }
main { flex: 1; width: 100%; max-width: 1200px; margin: 0 auto; padding: 32px 24px 60px; }
h1 { font-family: 'Playfair Display', serif; font-weight: 500; color: #ffffff; font-size: 1.9rem; margin: 0 0 6px; }
h1 a { text-decoration: none; font-size: 1.3rem; vertical-align: middle; }
.subtitle { color: rgba(255,255,255,0.55); margin: 0 0 24px; font-size: 0.95rem; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 14px; margin-bottom: 32px; }
.stat-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 18px; text-align: center; }
.stat-card h3 { font-family: 'Cinzel', serif; color: #c9bffc; font-size: 1.8rem; margin: 0 0 4px; }
.stat-card p { margin: 0; color: rgba(255,255,255,0.6); font-size: 0.75rem; letter-spacing: 0.05em; }
.genres { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 36px; }
.genres button { font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.04em; padding: 8px 16px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.18); cursor: pointer; white-space: nowrap; background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.75); }
.genres button.active { background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; }
.section-title { font-family: 'Cinzel', serif; color: #f0ecfb; font-size: 1.15rem; letter-spacing: 0.03em; margin: 0 0 16px; display: flex; justify-content: space-between; align-items: baseline; }
.section-title a { font-family: 'Segoe UI', sans-serif; font-size: 0.8rem; color: #c9bffc; text-decoration: none; font-weight: 600; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 18px; margin-bottom: 40px; }
.card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; }
.card .cover { height: 180px; background: #241f30; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.card .cover img { width: 100%; height: 100%; object-fit: cover; }
.card .info { padding: 14px; }
.card h3 { margin: 0 0 4px; font-size: 0.92rem; color: #f5f3fb; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card .genre { margin: 0 0 12px; font-size: 0.75rem; color: #c9bffc; font-style: italic; }
.card .actions { display: flex; gap: 8px; }
.btn-read { flex: 1; text-align: center; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.03em; background: linear-gradient(135deg, #a29bfe, #6c5ce7); color: #17111f; padding: 8px; border-radius: 999px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
.btn-done { flex: 1; text-align: center; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.03em; border: 1px solid rgba(255,255,255,0.25); background: transparent; color: rgba(255,255,255,0.75); padding: 8px; border-radius: 999px; font-weight: 600; cursor: pointer; }
.btn-start { display: block; text-align: center; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.03em; background: linear-gradient(135deg, #b8a9f3, #7d71e4); color: #17111f; padding: 8px; border-radius: 999px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
.btn-reread { display: block; text-align: center; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.03em; border: 1px solid rgba(255,255,255,0.25); background: transparent; color: rgba(255,255,255,0.75); padding: 8px; border-radius: 999px; font-weight: 600; text-decoration: none; }
.empty { text-align: center; padding: 40px; color: rgba(255,255,255,0.5); }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(245,243,251,0.5); margin: 0; }
@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
  .card .cover { height: 150px; }
  h1 { font-size: 1.5rem; }
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
      <a href="home.php" class="active">HOME</a>
      <a href="library.php">LIBRARY</a>
      <a href="add_manhwa.php" class="btn">+ ADD NEW</a>
      <a href="../../logout.php">LOGOUT</a>
    </nav>
  </header>

  <div class="hero">
    <img src="../../images/index-background.jpeg" alt="">
    <div class="overlay"></div>
    <div class="star" style="top: 18%; left: 20%; animation: twinkle 3s ease-in-out infinite;"></div>
    <div class="star" style="top: 30%; left: 75%; animation: twinkle 2.6s ease-in-out infinite 0.7s;"></div>
    <div class="quote">
      <p><?php echo htmlspecialchars($welcomeQuote); ?></p>
    </div>
  </div>

  <main>
    <h1>Welcome back, <?php echo htmlspecialchars($username); ?> <a href="secret-shelf_login.php" title="???">🧙‍♀️</a></h1>
    <p class="subtitle">Track your manhwa collection</p>

    <div class="stats">
      <div class="stat-card">
        <h3><?php echo $stats['reading_count']; ?></h3>
        <p>READING</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $stats['completed_count']; ?></h3>
        <p>COMPLETED</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $stats['plan_count']; ?></h3>
        <p>PLAN TO READ</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $stats['total_count']; ?></h3>
        <p>TOTAL</p>
      </div>
    </div>

    <div class="genres">
      <?php foreach ($genres as $g): ?>
      <button class="<?php echo $g === 'All' ? 'active' : ''; ?>" data-genre="<?php echo $g; ?>"><?php echo $g; ?></button>
      <?php endforeach; ?>
    </div>

    <h2 class="section-title">CONTINUE READING <a href="library.php?reading_status=Currently%20Reading">View all</a></h2>
    <?php if (mysqli_num_rows($reading_result) > 0): ?>
    <div class="grid">
      <?php while ($m = mysqli_fetch_assoc($reading_result)): ?>
      <div class="card" data-genre="<?php echo htmlspecialchars($m['genre']); ?>">
        <div class="cover">
          <?php if (!empty($m['cover_image'])): ?>
          <img src="../../<?php echo htmlspecialchars($m['cover_image']); ?>" alt="">
          <?php endif; ?>
        </div>
        <div class="info">
          <h3><?php echo htmlspecialchars($m['title']); ?></h3>
          <p class="genre"><?php echo htmlspecialchars($m['genre'] ?: 'No genre'); ?></p>
          <div class="actions">
            <?php if (!empty($m['reading_link'])): ?>
            <a href="<?php echo htmlspecialchars($m['reading_link']); ?>" target="_blank" class="btn-read">READ</a>
            <?php else: ?>
            <a href="view_manhwa.php?id=<?php echo $m['manhwa_id']; ?>" class="btn-read">VIEW</a>
            <?php endif; ?>
            <button class="btn-done" onclick="markDone(<?php echo $m['manhwa_id']; ?>)">DONE</button>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty">No manhwas currently reading</div>
    <?php endif; ?>

    <h2 class="section-title">PLAN TO READ <a href="library.php?reading_status=Plan%20to%20Read">View all</a></h2>
    <?php if (mysqli_num_rows($plan_result) > 0): ?>
    <div class="grid">
      <?php while ($m = mysqli_fetch_assoc($plan_result)): ?>
      <div class="card" data-genre="<?php echo htmlspecialchars($m['genre']); ?>">
        <div class="cover">
          <?php if (!empty($m['cover_image'])): ?>
          <img src="../../<?php echo htmlspecialchars($m['cover_image']); ?>" alt="">
          <?php endif; ?>
        </div>
        <div class="info">
          <h3><?php echo htmlspecialchars($m['title']); ?></h3>
          <p class="genre"><?php echo htmlspecialchars($m['genre'] ?: 'No genre'); ?></p>
          <?php if (!empty($m['reading_link'])): ?>
          <a href="<?php echo htmlspecialchars($m['reading_link']); ?>" target="_blank" class="btn-start" onclick="startReading(<?php echo $m['manhwa_id']; ?>)">START</a>
          <?php else: ?>
          <a href="view_manhwa.php?id=<?php echo $m['manhwa_id']; ?>" class="btn-start">VIEW</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty">No manhwas planned</div>
    <?php endif; ?>

    <h2 class="section-title" style="margin-top: 40px;">COMPLETED <a href="library.php?reading_status=Done">View all</a></h2>
    <?php if (mysqli_num_rows($completed_result) > 0): ?>
    <div class="grid">
      <?php while ($m = mysqli_fetch_assoc($completed_result)): ?>
      <div class="card" data-genre="<?php echo htmlspecialchars($m['genre']); ?>">
        <div class="cover">
          <?php if (!empty($m['cover_image'])): ?>
          <img src="../../<?php echo htmlspecialchars($m['cover_image']); ?>" alt="">
          <?php endif; ?>
        </div>
        <div class="info">
          <h3><?php echo htmlspecialchars($m['title']); ?></h3>
          <p class="genre"><?php echo htmlspecialchars($m['genre'] ?: 'No genre'); ?></p>
          <?php if (!empty($m['reading_link'])): ?>
          <a href="<?php echo htmlspecialchars($m['reading_link']); ?>" target="_blank" class="btn-reread">REREAD</a>
          <?php else: ?>
          <a href="view_manhwa.php?id=<?php echo $m['manhwa_id']; ?>" class="btn-reread">VIEW</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty">No completed manhwas</div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> — The Obscured Index. All rights reserved.</p>
  </footer>
</div>

<script>
function markDone(id) {
  fetch('home.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=mark_done&manhwa_id=' + id
  }).then(r => r.json()).then(d => { if(d.success) location.reload(); });
}

function startReading(id) {
  fetch('home.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=start_reading&manhwa_id=' + id
  });
}

document.querySelectorAll('.genres button').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.genres button').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const genre = this.dataset.genre;
    document.querySelectorAll('.card').forEach(card => {
      card.style.display = (genre === 'All' || card.dataset.genre === genre) ? 'block' : 'none';
    });
  });
});
</script>
</body>
</html>
