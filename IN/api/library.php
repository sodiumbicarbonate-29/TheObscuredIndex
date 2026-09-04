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
.toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; gap: 12px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 8px; }
.layout-btn { width: 34px; height: 34px; border-radius: 7px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.45); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
.layout-btn:hover, .layout-btn.active { background: rgba(162,155,254,0.12); border-color: rgba(162,155,254,0.4); color: #c9bffc; }
.layout-btn svg { width: 16px; height: 16px; fill: currentColor; }
.size-slider { -webkit-appearance: none; appearance: none; width: 80px; height: 4px; border-radius: 2px; background: rgba(255,255,255,0.12); outline: none; cursor: pointer; }
.size-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 14px; height: 14px; border-radius: 50%; background: #a29bfe; cursor: pointer; }
.size-label { font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 0.04em; color: rgba(255,255,255,0.35); }
/* GRID */
.grid { display: grid; gap: 16px; }
.grid.size-sm { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
.grid.size-md { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
.grid.size-lg { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
.card { text-decoration: none; display: block; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 6px; overflow: hidden; transition: transform 0.2s; }
.card:hover { transform: translateY(-4px); }
.card .cover { position: relative; aspect-ratio: 3/4; background: #241f30; overflow: hidden; }
.card .cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
.card .cover .status-badge { position: absolute; top: 8px; right: 8px; font-family: 'Cinzel', serif; font-size: 0.58rem; letter-spacing: 0.03em; padding: 3px 8px; border-radius: 999px; font-weight: 600; white-space: nowrap; }
.card .info { padding: 10px 10px 12px; }
.card h3 { margin: 0 0 4px; font-size: 0.82rem; color: #f5f3fb; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card .genre { margin: 0 0 4px; font-size: 0.7rem; color: #c9bffc; font-style: italic; }
.card .reading-status { font-size: 0.68rem; color: rgba(255,255,255,0.5); }
/* LIST */
.list { display: flex; flex-direction: column; gap: 8px; }
.list-item { text-decoration: none; display: flex; align-items: center; gap: 14px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 10px 14px; transition: background 0.15s, border-color 0.15s; }
.list-item:hover { background: rgba(255,255,255,0.07); border-color: rgba(162,155,254,0.3); }
.list-item .list-cover { width: 42px; height: 60px; border-radius: 4px; overflow: hidden; background: #241f30; flex-shrink: 0; }
.list-item .list-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
.list-item .list-info { flex: 1; min-width: 0; }
.list-item .list-title { font-size: 0.9rem; font-weight: 600; color: #f5f3fb; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 0 3px; }
.list-item .list-meta { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.list-item .list-genre { font-size: 0.72rem; color: #c9bffc; font-style: italic; }
.list-item .list-reading { font-size: 0.7rem; color: rgba(255,255,255,0.45); }
.list-item .list-badge { font-family: 'Cinzel', serif; font-size: 0.58rem; letter-spacing: 0.03em; padding: 3px 10px; border-radius: 999px; font-weight: 600; white-space: nowrap; flex-shrink: 0; }
.empty { text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; }
.empty p { font-family: 'Playfair Display', serif; font-style: italic; color: rgba(255,255,255,0.6); font-size: 1.1rem; margin: 0; }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(245,243,251,0.5); margin: 0; }
@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
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
      <a href="browse.php">BROWSE</a>
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
        <option value="Action" <?php echo $genre_filter=='Action'?'selected':''; ?>>Action</option>
        <option value="Adventure" <?php echo $genre_filter=='Adventure'?'selected':''; ?>>Adventure</option>
        <option value="BL" <?php echo $genre_filter=='BL'?'selected':''; ?>>BL</option>
        <option value="Comedy" <?php echo $genre_filter=='Comedy'?'selected':''; ?>>Comedy</option>
        <option value="Cooking" <?php echo $genre_filter=='Cooking'?'selected':''; ?>>Cooking</option>
        <option value="Crime" <?php echo $genre_filter=='Crime'?'selected':''; ?>>Crime</option>
        <option value="Delinquents" <?php echo $genre_filter=='Delinquents'?'selected':''; ?>>Delinquents</option>
        <option value="Demons" <?php echo $genre_filter=='Demons'?'selected':''; ?>>Demons</option>
        <option value="Drama" <?php echo $genre_filter=='Drama'?'selected':''; ?>>Drama</option>
        <option value="Fantasy" <?php echo $genre_filter=='Fantasy'?'selected':''; ?>>Fantasy</option>
        <option value="Genderswap" <?php echo $genre_filter=='Genderswap'?'selected':''; ?>>Genderswap</option>
        <option value="GL" <?php echo $genre_filter=='GL'?'selected':''; ?>>GL</option>
        <option value="Harem" <?php echo $genre_filter=='Harem'?'selected':''; ?>>Harem</option>
        <option value="Historical" <?php echo $genre_filter=='Historical'?'selected':''; ?>>Historical</option>
        <option value="Horror" <?php echo $genre_filter=='Horror'?'selected':''; ?>>Horror</option>
        <option value="Isekai" <?php echo $genre_filter=='Isekai'?'selected':''; ?>>Isekai</option>
        <option value="Mafia" <?php echo $genre_filter=='Mafia'?'selected':''; ?>>Mafia</option>
        <option value="Magical Girls" <?php echo $genre_filter=='Magical Girls'?'selected':''; ?>>Magical Girls</option>
        <option value="Martial Arts" <?php echo $genre_filter=='Martial Arts'?'selected':''; ?>>Martial Arts</option>
        <option value="Mecha" <?php echo $genre_filter=='Mecha'?'selected':''; ?>>Mecha</option>
        <option value="Medical" <?php echo $genre_filter=='Medical'?'selected':''; ?>>Medical</option>
        <option value="Military" <?php echo $genre_filter=='Military'?'selected':''; ?>>Military</option>
        <option value="Music" <?php echo $genre_filter=='Music'?'selected':''; ?>>Music</option>
        <option value="Mystery" <?php echo $genre_filter=='Mystery'?'selected':''; ?>>Mystery</option>
        <option value="No Romance" <?php echo $genre_filter=='No Romance'?'selected':''; ?>>No Romance</option>
        <option value="Office Workers" <?php echo $genre_filter=='Office Workers'?'selected':''; ?>>Office Workers</option>
        <option value="Philosophical" <?php echo $genre_filter=='Philosophical'?'selected':''; ?>>Philosophical</option>
        <option value="Post-Apocalyptic" <?php echo $genre_filter=='Post-Apocalyptic'?'selected':''; ?>>Post-Apocalyptic</option>
        <option value="Psychological" <?php echo $genre_filter=='Psychological'?'selected':''; ?>>Psychological</option>
        <option value="Reincarnation" <?php echo $genre_filter=='Reincarnation'?'selected':''; ?>>Reincarnation</option>
        <option value="Reverse Harem" <?php echo $genre_filter=='Reverse Harem'?'selected':''; ?>>Reverse Harem</option>
        <option value="Romance" <?php echo $genre_filter=='Romance'?'selected':''; ?>>Romance</option>
        <option value="Sci-Fi" <?php echo $genre_filter=='Sci-Fi'?'selected':''; ?>>Sci-Fi</option>
        <option value="School Life" <?php echo $genre_filter=='School Life'?'selected':''; ?>>School Life</option>
        <option value="Slice of Life" <?php echo $genre_filter=='Slice of Life'?'selected':''; ?>>Slice of Life</option>
        <option value="Sports" <?php echo $genre_filter=='Sports'?'selected':''; ?>>Sports</option>
        <option value="Superhero" <?php echo $genre_filter=='Superhero'?'selected':''; ?>>Superhero</option>
        <option value="Supernatural" <?php echo $genre_filter=='Supernatural'?'selected':''; ?>>Supernatural</option>
        <option value="Survival" <?php echo $genre_filter=='Survival'?'selected':''; ?>>Survival</option>
        <option value="Thriller" <?php echo $genre_filter=='Thriller'?'selected':''; ?>>Thriller</option>
        <option value="Time Travel" <?php echo $genre_filter=='Time Travel'?'selected':''; ?>>Time Travel</option>
        <option value="Tragedy" <?php echo $genre_filter=='Tragedy'?'selected':''; ?>>Tragedy</option>
        <option value="Vampires" <?php echo $genre_filter=='Vampires'?'selected':''; ?>>Vampires</option>
        <option value="Villainess" <?php echo $genre_filter=='Villainess'?'selected':''; ?>>Villainess</option>
        <option value="Wuxia" <?php echo $genre_filter=='Wuxia'?'selected':''; ?>>Wuxia</option>
        <option value="Zombies" <?php echo $genre_filter=='Zombies'?'selected':''; ?>>Zombies</option>
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
    <div class="toolbar">
      <div class="toolbar-left">
        <button class="layout-btn active" id="btn-grid" onclick="setLayout('grid')" title="Grid">
          <svg viewBox="0 0 16 16"><rect x="0" y="0" width="7" height="7"/><rect x="9" y="0" width="7" height="7"/><rect x="0" y="9" width="7" height="7"/><rect x="9" y="9" width="7" height="7"/></svg>
        </button>
        <button class="layout-btn" id="btn-list" onclick="setLayout('list')" title="List">
          <svg viewBox="0 0 16 16"><rect x="0" y="1" width="16" height="2"/><rect x="0" y="7" width="16" height="2"/><rect x="0" y="13" width="16" height="2"/></svg>
        </button>
        <span class="size-label">SIZE</span>
        <input type="range" class="size-slider" id="size-slider" min="0" max="2" step="1" value="1" oninput="setSize(this.value)">
      </div>
    </div>

    <div class="grid size-md" id="items-grid">
      <?php while ($m = mysqli_fetch_assoc($result)): ?>
      <?php
        $cover_src = !empty($m['cover_image']) ? (str_starts_with($m['cover_image'], 'http') ? htmlspecialchars($m['cover_image']) : '../../' . htmlspecialchars($m['cover_image'])) : '';
        $sc = getStatusColor($m['status']);
      ?>
      <a href="view_manhwa.php?id=<?php echo $m['manhwa_id']; ?>" class="card">
        <div class="cover">
          <?php if ($cover_src): ?><img src="<?php echo $cover_src; ?>" alt="" loading="lazy"><?php endif; ?>
          <span class="status-badge" style="background:<?php echo $sc; ?>;color:#17111f;"><?php echo htmlspecialchars($m['status']); ?></span>
        </div>
        <div class="info">
          <h3><?php echo htmlspecialchars($m['title']); ?></h3>
          <p class="genre"><?php echo htmlspecialchars($m['genre'] ?: 'No genre'); ?></p>
          <?php if (!empty($m['reading_status'])): ?><p class="reading-status"><?php echo htmlspecialchars($m['reading_status']); ?></p><?php endif; ?>
        </div>
      </a>
      <?php endwhile; ?>
    </div>

    <?php
    // Reset result pointer for list view
    mysqli_data_seek($result, 0);
    ?>
    <div class="list" id="items-list" style="display:none">
      <?php while ($m = mysqli_fetch_assoc($result)): ?>
      <?php
        $cover_src = !empty($m['cover_image']) ? (str_starts_with($m['cover_image'], 'http') ? htmlspecialchars($m['cover_image']) : '../../' . htmlspecialchars($m['cover_image'])) : '';
        $sc = getStatusColor($m['status']);
      ?>
      <a href="view_manhwa.php?id=<?php echo $m['manhwa_id']; ?>" class="list-item">
        <div class="list-cover"><?php if ($cover_src): ?><img src="<?php echo $cover_src; ?>" alt="" loading="lazy"><?php endif; ?></div>
        <div class="list-info">
          <p class="list-title"><?php echo htmlspecialchars($m['title']); ?></p>
          <div class="list-meta">
            <span class="list-genre"><?php echo htmlspecialchars($m['genre'] ?: 'No genre'); ?></span>
            <?php if (!empty($m['reading_status'])): ?><span class="list-reading"><?php echo htmlspecialchars($m['reading_status']); ?></span><?php endif; ?>
          </div>
        </div>
        <span class="list-badge" style="background:<?php echo $sc; ?>22;color:<?php echo $sc; ?>;border:1px solid <?php echo $sc; ?>44;"><?php echo htmlspecialchars($m['status']); ?></span>
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
<script>
const SIZES = ['size-sm', 'size-md', 'size-lg'];
let currentLayout = localStorage.getItem('lib_layout') || 'grid';
let currentSize   = parseInt(localStorage.getItem('lib_size') ?? '1');

function setLayout(l) {
  currentLayout = l;
  localStorage.setItem('lib_layout', l);
  applyLayout();
}

function setSize(v) {
  currentSize = parseInt(v);
  localStorage.setItem('lib_size', currentSize);
  applyLayout();
}

function applyLayout() {
  const grid = document.getElementById('items-grid');
  const list = document.getElementById('items-list');
  const slider = document.getElementById('size-slider');
  const btnGrid = document.getElementById('btn-grid');
  const btnList = document.getElementById('btn-list');
  if (!grid) return;

  const isGrid = currentLayout === 'grid';
  grid.style.display = isGrid ? '' : 'none';
  list.style.display  = isGrid ? 'none' : '';
  btnGrid.classList.toggle('active', isGrid);
  btnList.classList.toggle('active', !isGrid);
  slider.style.display = isGrid ? '' : 'none';
  slider.value = currentSize;

  grid.classList.remove(...SIZES);
  grid.classList.add(SIZES[currentSize]);
}

applyLayout();
</script>
</body>
</html>
