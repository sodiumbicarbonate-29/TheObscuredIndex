<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }

require_once '../../includes/db_connect.php';
$user_id   = $_SESSION['user_id'];
$manhwa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$manhwa_id) { header("Location: library.php"); exit(); }

$stmt = mysqli_prepare($conn, "SELECT m.*, urs.reading_status, urs.current_chapter, urs.start_reading_date
    FROM Manhwas m
    LEFT JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id AND urs.user_id = ?
    WHERE m.manhwa_id = ? AND m.user_id = ?");
mysqli_stmt_bind_param($stmt, "iii", $user_id, $manhwa_id, $user_id);
mysqli_stmt_execute($stmt);
$manhwa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$manhwa) { header("Location: library.php"); exit(); }

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'update_chapter') {
        $chapter = max(0, (int)$_POST['chapter']);
        $new_status = ($manhwa['reading_status'] === 'Done') ? 'Done' : 'Currently Reading';
        $upd = mysqli_prepare($conn, "UPDATE User_Reading_Status SET current_chapter=?, reading_status=?, start_reading_date=COALESCE(start_reading_date, ?), last_updated=NOW() WHERE manhwa_id=? AND user_id=?");
        $today = date('Y-m-d');
        mysqli_stmt_bind_param($upd, "issii", $chapter, $new_status, $today, $manhwa_id, $user_id);
        echo json_encode(['success' => mysqli_stmt_execute($upd), 'chapter' => $chapter, 'status' => $new_status]);
        exit();
    }

    if ($action === 'mark_done') {
        $finish = date('Y-m-d');
        $upd = mysqli_prepare($conn, "UPDATE User_Reading_Status SET reading_status='Done', finish_reading_date=?, last_updated=NOW() WHERE manhwa_id=? AND user_id=?");
        mysqli_stmt_bind_param($upd, "sii", $finish, $manhwa_id, $user_id);
        echo json_encode(['success' => mysqli_stmt_execute($upd)]);
        exit();
    }

    if ($action === 'get_chapters') {
        $mdx_id = $manhwa['mangadex_id'] ?? '';
        if (!$mdx_id) { echo json_encode(['error' => 'no_id']); exit(); }

        $url = "https://api.mangadex.org/manga/{$mdx_id}/feed?translatedLanguage[]=en&order[chapter]=asc&limit=500&contentRating[]=safe&contentRating[]=suggestive&contentRating[]=erotica&contentRating[]=pornographic";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Origin: https://mangadex.org',
                'Referer: https://mangadex.org/',
            ],
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $raw = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($raw, true);
        if (!$data || !isset($data['data'])) {
            echo json_encode(['error' => 'fetch_failed', 'http_code' => $http_code, 'raw' => substr($raw, 0, 300)]);
            exit();
        }
        $chapters = [];
        $seen = [];
        foreach (($data['data'] ?? []) as $c) {
            $num = $c['attributes']['chapter'] ?? '0';
            if (isset($seen[$num])) continue; // deduplicate
            $seen[$num] = true;
            $chapters[] = ['id' => $c['id'], 'chapter' => $num, 'title' => $c['attributes']['title'] ?? ''];
        }
        echo json_encode($chapters);
        exit();
    }

    if ($action === 'get_pages') {
        $chapter_id = $_POST['chapter_id'] ?? '';
        if (!$chapter_id) { echo json_encode(['error' => 'no_chapter']); exit(); }

        $ch = curl_init("https://api.mangadex.org/at-home/server/{$chapter_id}");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Origin: https://mangadex.org',
                'Referer: https://mangadex.org/',
            ],
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($raw, true);
        $base = $data['baseUrl'] ?? '';
        $hash = $data['chapter']['hash'] ?? '';
        $pages = array_map(fn($p) => "{$base}/data/{$hash}/{$p}", $data['chapter']['data'] ?? []);
        echo json_encode($pages);
        exit();
    }

    echo json_encode(['success' => false]);
    exit();
}

$current_chapter = (int)($manhwa['current_chapter'] ?? 0);
$has_mangadex = !empty($manhwa['mangadex_id']);
$has_comick   = !empty($manhwa['comick_hid']);
$has_source   = $has_mangadex || $has_comick;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="referrer" content="no-referrer">
<title>Reading: <?php echo htmlspecialchars($manhwa['title']); ?></title>
<link rel="icon" type="image/png" href="../../images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background: #0e0a17; color: #fff; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

/* TOPBAR */
.topbar { display: flex; align-items: center; gap: 12px; padding: 0 20px; height: 56px; background: rgba(14,10,23,0.98); border-bottom: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; position: relative; }
.topbar a.back { font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.04em; color: rgba(201,191,252,0.8); text-decoration: none; white-space: nowrap; transition: color 0.2s; }
.topbar a.back:hover { color: #c9bffc; }
.topbar .divider { width: 1px; height: 20px; background: rgba(255,255,255,0.1); flex-shrink: 0; }
.topbar .title { font-family: 'Playfair Display', serif; font-size: 1rem; color: #f5f3fb; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.topbar .status-badge { font-family: 'Cinzel', serif; font-size: 0.62rem; letter-spacing: 0.06em; padding: 4px 14px; border-radius: 999px; background: rgba(162,155,254,0.12); color: #a29bfe; border: 1px solid rgba(162,155,254,0.25); white-space: nowrap; flex-shrink: 0; }
.topbar .status-badge.done { background: rgba(0,184,148,0.12); color: #00b894; border-color: rgba(0,184,148,0.25); }
.topbar .divider-v { width: 1px; height: 20px; background: rgba(255,255,255,0.08); flex-shrink: 0; }
.ch-controls { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.ch-btn { width: 28px; height: 28px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.8); font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; line-height: 1; }
.ch-btn:hover { background: rgba(162,155,254,0.15); border-color: rgba(162,155,254,0.5); color: #c9bffc; }
.ch-input { width: 52px; padding: 5px 6px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.06); color: #fff; font-size: 0.85rem; text-align: center; font-family: inherit; }
.ch-input:focus { outline: none; border-color: #a29bfe; background: rgba(162,155,254,0.08); }
.done-btn { font-family: 'Cinzel', serif; font-size: 0.6rem; letter-spacing: 0.05em; padding: 4px 10px; border-radius: 999px; border: 1px solid rgba(0,184,148,0.25); background: transparent; color: rgba(0,184,148,0.7); font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; flex-shrink: 0; }
.done-btn:hover { background: rgba(0,184,148,0.1); color: #00b894; border-color: rgba(0,184,148,0.4); }
.saved-msg { font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 0.04em; color: #00b894; display: none; white-space: nowrap; }
.toggle-btn { width: 34px; height: 34px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.6); cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; transition: all 0.15s; flex-shrink: 0; }
.toggle-btn:hover, .toggle-btn.open { background: rgba(162,155,254,0.1); border-color: rgba(162,155,254,0.4); color: #c9bffc; }
.toggle-btn span { display: block; width: 16px; height: 2px; background: currentColor; border-radius: 2px; }

/* CHAPTER DROPDOWN */
.chapter-dropdown { position: absolute; top: 56px; right: 20px; width: 280px; max-height: 420px; background: #0e0a17; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.6); overflow-y: auto; z-index: 200; display: none; flex-direction: column; }
.chapter-dropdown.open { display: flex; }
.chapter-dropdown::-webkit-scrollbar { width: 4px; }
.chapter-dropdown::-webkit-scrollbar-track { background: transparent; }
.chapter-dropdown::-webkit-scrollbar-thumb { background: rgba(162,155,254,0.2); border-radius: 4px; }
.ch-list-header { padding: 12px 16px; font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 0.08em; color: rgba(255,255,255,0.3); border-bottom: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; }
.ch-item { padding: 12px 18px; font-size: 0.82rem; color: rgba(255,255,255,0.55); cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.04); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; transition: all 0.15s; }
.ch-item:hover { background: rgba(162,155,254,0.08); color: rgba(255,255,255,0.9); }
.ch-item.active { background: rgba(162,155,254,0.14); color: #c9bffc; border-left: 2px solid #a29bfe; padding-left: 16px; }
.ch-loading { padding: 20px 16px; color: rgba(255,255,255,0.25); font-size: 0.8rem; font-style: italic; }

/* PAGES AREA */
.pages-area { flex: 1; overflow-y: auto; background: #0a0a0a; display: flex; flex-direction: column; align-items: center; padding: 0; min-height: 0; }
.pages-area::-webkit-scrollbar { width: 6px; }
.pages-area::-webkit-scrollbar-track { background: #0a0a0a; }
.pages-area::-webkit-scrollbar-thumb { background: rgba(162,155,254,0.2); border-radius: 4px; }
.pages-area img { width: 100%; max-width: 780px; display: block; }
.page-msg { color: rgba(255,255,255,0.25); font-size: 0.95rem; margin-top: 80px; font-family: 'Playfair Display', serif; font-style: italic; }
.page-loading { display: flex; flex-direction: column; align-items: center; gap: 14px; margin-top: 80px; }
.spinner { width: 32px; height: 32px; border: 2px solid rgba(162,155,254,0.15); border-top-color: #a29bfe; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.page-loading span { font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.25); }

/* NO SOURCE */
.no-mdx { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px; }
.no-mdx p { font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.05rem; color: rgba(255,255,255,0.35); }
.no-mdx a { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.04em; color: #c9bffc; text-decoration: none; border: 1px solid rgba(162,155,254,0.3); padding: 10px 24px; border-radius: 999px; transition: background 0.2s; }
.no-mdx a:hover { background: rgba(162,155,254,0.08); }
</style>
</head>
<body>

<div class="topbar">
  <a href="view_manhwa.php?id=<?php echo $manhwa_id; ?>" class="back">&larr; Back</a>
  <div class="divider"></div>
  <span class="title"><?php echo htmlspecialchars($manhwa['title']); ?></span>
  <input type="hidden" id="ch-input" value="<?php echo $current_chapter; ?>">
  <span class="saved-msg" id="saved-msg">Saved</span>
  <?php if ($has_source): ?>
  <div class="divider-v"></div>
  <button class="toggle-btn" id="toggle-btn" onclick="toggleChapters()" title="Chapters"><span></span><span></span><span></span></button>
  <?php endif; ?>
</div>

<?php if ($has_source): ?>
<div class="chapter-dropdown" id="chapter-list">
  <div class="ch-list-header" style="display:flex;justify-content:space-between;align-items:center;">
    <span>CHAPTERS</span>
    <button class="done-btn" onclick="markDone()" id="done-btn">MARK DONE</button>
  </div>
  <div class="ch-loading">Loading...</div>
</div>
<?php endif; ?>

<div class="reader-body" style="display:flex;flex:1;overflow:hidden;min-height:0;">
<?php if ($has_source): ?>
  <div class="pages-area" id="pages-area">
    <p class="page-msg">Select a chapter to start reading.</p>
  </div>
<?php else: ?>
  <div class="no-mdx">
    <p>This title has no reading source attached.</p>
    <a href="browse.php">Browse</a>
  </div>
<?php endif; ?>
</div>

<script>
const manhwaId = <?php echo $manhwa_id; ?>;
<?php if ($has_source): ?>

const SOURCE = '<?php echo $has_comick ? 'comick' : 'mangadex'; ?>';
const COMICK_HID = '<?php echo htmlspecialchars($manhwa['comick_hid'] ?? ''); ?>';

let chapters = [];
let activeChapterId = null;
const CHAPTERS_KEY = 'chapters_<?php echo $manhwa_id; ?>';

const cached = sessionStorage.getItem(CHAPTERS_KEY);
if (cached) {
  initChapters(JSON.parse(cached));
} else {
  loadChapterList();
}

async function loadChapterList() {
  try {
    let data;
    if (SOURCE === 'comick') {
      const res = await fetch(`https://api.comick.dev/comic/${COMICK_HID}/chapters?lang=en&limit=500&page=1`);
      const json = await res.json();
      data = (json.chapters ?? []).map(c => ({ id: c.hid, chapter: String(c.chap ?? '0'), title: c.title ?? '' }));
    } else {
      const res = await fetch('reader.php?id=' + manhwaId, {
      data = await res.json();
      if (!Array.isArray(data) || data.error) { document.getElementById('chapter-list').innerHTML = '<div class="ch-list-header">CHAPTERS</div><div class="ch-loading">No chapters found. (' + (data.error || 'empty') + ')</div>'; return; }
    }
    sessionStorage.setItem(CHAPTERS_KEY, JSON.stringify(data));
    initChapters(data);
  } catch(e) {
    document.getElementById('chapter-list').innerHTML = '<div class="ch-list-header">CHAPTERS</div><div class="ch-loading">Failed to load chapters.</div>';
  }
}

function initChapters(data) {
  chapters = data;
  const items = chapters.map((c, i) =>
    `<div class="ch-item" id="ch-${c.id}" onclick="loadChapter(${i})">Ch. ${c.chapter}${c.title ? ' &mdash; ' + c.title : ''}</div>`
  ).join('');
  document.getElementById('chapter-list').innerHTML = '<div class="ch-list-header" style="display:flex;justify-content:space-between;align-items:center;"><span>CHAPTERS</span><button class="done-btn" onclick="markDone()" id="done-btn">MARK DONE</button></div>' + items;
  const savedIdx = chapters.findIndex(c => parseFloat(c.chapter) >= <?php echo $current_chapter ?: 0; ?>);
  if (!chapters.length) { document.getElementById('chapter-list').innerHTML = '<div class="ch-list-header">CHAPTERS</div><div class="ch-loading">No chapters found.</div>'; return; }
  loadChapter(savedIdx >= 0 ? savedIdx : 0);
}

async function loadChapter(i) {
  const c = chapters[i];
  if (activeChapterId) document.getElementById('ch-' + activeChapterId)?.classList.remove('active');
  activeChapterId = c.id;
  document.getElementById('ch-' + c.id).classList.add('active');
  document.getElementById('pages-area').innerHTML = '<div class="page-loading"><div class="spinner"></div><span>LOADING PAGES</span></div>';
  document.getElementById('ch-input').value = c.chapter;
  saveChapter();

  try {
    let pages;
    if (SOURCE === 'comick') {
      const res = await fetch(`https://api.comick.dev/chapter/${c.id}`);
      const json = await res.json();
      pages = (json.chapter?.images ?? []).map(img => `https://meo.comick.pictures/${img.b2key}`);
    } else {
      const res = await fetch('reader.php?id=' + manhwaId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_pages&chapter_id=' + encodeURIComponent(c.id)
      });
      pages = await res.json();
    }
    if (!pages.length) { document.getElementById('pages-area').innerHTML = '<p class="page-msg">No pages found.</p>'; return; }
    document.getElementById('pages-area').innerHTML = pages.map(p => `<img src="${p}" loading="lazy" style="display:block">`).join('');
    document.getElementById('pages-area').scrollTop = 0;
  } catch(e) {
    document.getElementById('pages-area').innerHTML = '<p class="page-msg">Failed to load pages.</p>';
  }
}

<?php endif; ?>

function toggleChapters() {
  const list = document.getElementById('chapter-list');
  const btn = document.getElementById('toggle-btn');
  list.classList.toggle('open');
  btn.classList.toggle('open', list.classList.contains('open'));
  localStorage.setItem('chapters_panel', list.classList.contains('open') ? '1' : '0');
}

// Restore panel state
(function() {
  const state = localStorage.getItem('chapters_panel');
  if (state === '1') {
    document.getElementById('chapter-list')?.classList.add('open');
    document.getElementById('toggle-btn')?.classList.add('open');
  }
})();

// Close dropdown when clicking outside
document.addEventListener('click', e => {
  const list = document.getElementById('chapter-list');
  const btn = document.getElementById('toggle-btn');
  if (list && btn && !list.contains(e.target) && !btn.contains(e.target)) {
    list.classList.remove('open');
    btn.classList.remove('open');
    localStorage.setItem('chapters_panel', '0');
  }
});

function adjustChapter(delta) {
  const input = document.getElementById('ch-input');
  input.value = Math.max(0, (parseInt(input.value) || 0) + delta);
}

function saveChapter() {
  const chapter = parseInt(document.getElementById('ch-input').value) || 0;
  fetch('reader.php?id=' + manhwaId, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=update_chapter&chapter=' + chapter
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      const msg = document.getElementById('saved-msg');
      document.getElementById('status-badge').textContent = d.status;
      msg.style.display = 'inline';
      setTimeout(() => msg.style.display = 'none', 2000);
    }
  });
}

function markDone() {
  fetch('reader.php?id=' + manhwaId, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=mark_done'
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      const badge = document.getElementById('status-badge');
      badge.textContent = 'Done';
      badge.className = 'status-badge done';
      const msg = document.getElementById('saved-msg');
      msg.textContent = 'Marked done';
      msg.style.display = 'inline';
      setTimeout(() => { msg.style.display = 'none'; msg.textContent = 'Saved'; }, 2500);
    }
  });
}

document.addEventListener('keydown', e => {
  if (e.target.tagName === 'INPUT') return;
  if (e.key === '+' || e.key === '=') adjustChapter(1);
  if (e.key === '-') adjustChapter(-1);
  if (e.key === 's') saveChapter();
});
</script>
</body>
</html>
