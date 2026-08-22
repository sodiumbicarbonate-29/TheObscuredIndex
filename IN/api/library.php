<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// Get filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$reading_status_filter = isset($_GET['reading_status']) ? $_GET['reading_status'] : 'all';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : 'all';

// Build query
$query = "SELECT m.*, urs.reading_status, urs.start_reading_date, urs.finish_reading_date
          FROM Manhwas m 
          LEFT JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
          WHERE m.user_id = ?";
$params = [$user_id, $user_id];
$types = "ii";

if (!empty($search)) {
    $query .= " AND (m.title LIKE ? OR m.author LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if ($status_filter != 'all') {
    $query .= " AND m.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($reading_status_filter != 'all') {
    $query .= " AND urs.reading_status = ?";
    $params[] = $reading_status_filter;
    $types .= "s";
}

if ($genre_filter != 'all') {
    $query .= " AND m.genre = ?";
    $params[] = $genre_filter;
    $types .= "s";
}

$query .= " ORDER BY m.title ASC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total = mysqli_num_rows($result);

// Status colors
function getStatusColor($status) {
    $colors = [
        'Currently Reading' => '#a29bfe',
        'Done' => '#00b894',
        'Plan to Read' => '#fdcb6e',
        'Ongoing' => '#a29bfe',
        'Completed' => '#00b894',
        'Hiatus' => '#fdcb6e',
        'Dropped' => '#fd79a8'
    ];
    return $colors[$status] ?? '#a29bfe';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Library - The Obscured Index</title>
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
nav a:hover, nav a.active { color: #ffffff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }
main { flex: 1; width: 100%; max-width: 1200px; margin: 0 auto; padding: 36px 24px 60px; }
h1 { font-family: 'Playfair Display', serif; font-weight: 500; color: #ffffff; font-size: 1.9rem; margin: 0 0 6px; }
.subtitle { color: rgba(255,255,255,0.55); margin: 0 0 26px; font-size: 0.95rem; }
.filters { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; }
.filters input, .filters select { flex: 1; min-width: 150px; box-sizing: border-box; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.16); background: rgba(255,255,255,0.06); color: #fff; font-size: 0.9rem; }
.filters input:focus, .filters select:focus { outline: none; border-color: #a29bfe; }
.filters select option { background: #0e0a17; color: #fff; }
.status-filters { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 30px; }
.status-filters a { font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.04em; padding: 8px 16px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.18); text-decoration: none; white-space: nowrap; background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.75); }
.status-filters a.active { background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 18px; }
.card { text-decoration: none; display: block; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; transition: transform 0.2s; }
.card:hover { transform: translateY(-4px); }
.card .cover { position: relative; height: 200px; background: #241f30; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.card .cover img { width: 100%; height: 100%; object-fit: cover; }
.card .cover .status-badge { position: absolute; top: 10px; right: 10px; font-family: 'Cinzel', serif; font-size: 0.62rem; letter-spacing: 0.03em; padding: 4px 10px; border-radius: 999px; font-weight: 600; white-space: nowrap; }
.card .info { padding: 14px; }
.card h3 { margin: 0 0 4px; font-size: 0.9rem; color: #f5f3fb; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card .genre { margin: 0 0 8px; font-size: 0.75rem; color: #c9bffc; font-style: italic; }
.card .reading-status { font-size: 0.7rem; color: rgba(255,255,255,0.6); }
.empty { text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; }
.empty p { font-family: 'Playfair Display', serif; font-style: italic; color: rgba(255,255,255,0.6); font-size: 1.1rem; margin: 0; }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(245,243,251,0.5); margin: 0; }
@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
  .card .cover { height: 180px; }
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
      <a href="library.php" class="active">LIBRARY</a>
      <a href="add_manhwa.php" class="btn">+ ADD NEW</a>
      <a href="../../logout.php">LOGOUT</a>
    </nav>
  </header>

  <main>
    <h1>Your Library</h1>
    <p class="subtitle"><?php echo $total; ?> titles obscured in these halls.</p>

    <form method="GET" class="filters">
      <input type="text" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search); ?>">
      <select name="genre" onchange="this.form.submit()">
        <option value="all">All Genres</option>
        <option value="BL" <?php echo $genre_filter == 'BL' ? 'selected' : ''; ?>>BL</option>
        <option value="Straight" <?php echo $genre_filter == 'Straight' ? 'selected' : ''; ?>>Straight</option>
        <option value="No Romance" <?php echo $genre_filter == 'No Romance' ? 'selected' : ''; ?>>No Romance</option>
      </select>
      <select name="status" onchange="this.form.submit()">
        <option value="all">All Status</option>
        <option value="Ongoing" <?php echo $status_filter == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
        <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
        <option value="Hiatus" <?php echo $status_filter == 'Hiatus' ? 'selected' : ''; ?>>Hiatus</option>
        <option value="Dropped" <?php echo $status_filter == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
      </select>
      <button type="submit" style="padding: 12px 20px; border-radius: 8px; border: none; background: linear-gradient(135deg, #a29bfe, #8a2be2); color: #17111f; font-weight: 600; cursor: pointer;">Search</button>
    </form>

    <div class="status-filters">
      <a href="?<?php echo http_build_query(array_merge($_GET, ['reading_status' => 'all'])); ?>" class="<?php echo $reading_status_filter == 'all' ? 'active' : ''; ?>">All</a>
      <a href="?<?php echo http_build_query(array_merge($_GET, ['reading_status' => 'Currently Reading'])); ?>" class="<?php echo $reading_status_filter == 'Currently Reading' ? 'active' : ''; ?>">Currently Reading</a>
      <a href="?<?php echo http_build_query(array_merge($_GET, ['reading_status' => 'Done'])); ?>" class="<?php echo $reading_status_filter == 'Done' ? 'active' : ''; ?>">Completed</a>
      <a href="?<?php echo http_build_query(array_merge($_GET, ['reading_status' => 'Plan to Read'])); ?>" class="<?php echo $reading_status_filter == 'Plan to Read' ? 'active' : ''; ?>">Plan to Read</a>
    </div>

    <?php if ($total > 0): ?>
    <div class="grid">
      <?php while ($m = mysqli_fetch_assoc($result)): ?>
      <a href="view_manhwa.php?id=<?php echo $m['manhwa_id']; ?>" class="card">
        <div class="cover">
          <?php if (!empty($m['cover_image'])): ?>
          <img src="../../<?php echo htmlspecialchars($m['cover_image']); ?>" alt="">
          <?php endif; ?>
          <span class="status-badge" style="background: <?php echo getStatusColor($m['status']); ?>; color: #17111f;"><?php echo htmlspecialchars($m['status']); ?></span>
        </div>
        <div class="info">
          <h3><?php echo htmlspecialchars($m['title']); ?></h3>
          <p class="genre"><?php echo htmlspecialchars($m['genre'] ?: 'No genre'); ?></p>
          <?php if (!empty($m['reading_status'])): ?>
          <p class="reading-status"><?php echo htmlspecialchars($m['reading_status']); ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty">
      <p>Nothing obscured enough to match that search.</p>
    </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> — The Obscured Index. All rights reserved.</p>
  </footer>
</div>
</body>
</html>
