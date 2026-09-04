<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Browse - The Obscured Index</title>
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
nav a.active { color: #ffffff; }
nav a.btn { font-size: 0.75rem; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 9px 16px; border-radius: 999px; font-weight: 600; }
main { flex: 1; width: 100%; max-width: 1100px; margin: 0 auto; padding: 36px 24px 60px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: #c9bffc; text-decoration: none; font-size: 0.85rem; margin-bottom: 14px; }
h1 { font-family: 'Playfair Display', serif; font-weight: 500; color: #ffffff; font-size: 1.9rem; margin: 0 0 6px; }
.subtitle { color: rgba(255,255,255,0.5); font-size: 0.9rem; margin: 0 0 20px; }
.search-bar { display: flex; gap: 10px; margin-bottom: 14px; }
.search-bar input { flex: 1; padding: 13px 18px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.95rem; font-family: inherit; }
.search-bar input:focus { outline: none; border-color: #a29bfe; background: rgba(255,255,255,0.14); }
.search-bar button { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 0.04em; color: #17111f; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 13px 24px; border-radius: 999px; font-weight: 600; border: none; cursor: pointer; }
.filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 28px; }
.filters select { padding: 10px 14px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color: #fff; font-size: 0.82rem; font-family: inherit; cursor: pointer; }
.filters select:focus { outline: none; border-color: #a29bfe; }
.filters select option { background: #0e0a17; }
.source-tabs { display: flex; gap: 0; margin-bottom: 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 4px; width: fit-content; }
.source-tab { font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.05em; padding: 9px 22px; cursor: pointer; color: rgba(255,255,255,0.45); border-radius: 7px; background: none; border: none; transition: all 0.2s; }
.source-tab.active { background: rgba(162,155,254,0.15); color: #c9bffc; box-shadow: 0 1px 4px rgba(0,0,0,0.3); }
.source-tab:hover:not(.active) { color: rgba(255,255,255,0.75); }
.source-section { display: none; }
.source-section.active { display: block; }
.section-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.07); }
.section-title { display: flex; align-items: center; gap: 10px; }
.source-badge { font-size: 0.62rem; padding: 4px 12px; border-radius: 999px; font-weight: 600; font-family: 'Cinzel', serif; letter-spacing: 0.04em; }
.source-badge.mangadex { background: rgba(249,111,111,0.12); color: #f96f6f; border: 1px solid rgba(249,111,111,0.25); }
.source-badge.comick { background: rgba(162,155,254,0.12); color: #a29bfe; border: 1px solid rgba(162,155,254,0.25); }
.source-status { font-size: 0.78rem; color: rgba(255,255,255,0.35); font-style: italic; }
.load-more { display: block; margin: 16px auto 0; font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.04em; padding: 10px 28px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); cursor: pointer; transition: all 0.2s; }
.load-more:hover { border-color: #a29bfe; color: #c9bffc; background: rgba(162,155,254,0.08); }
.load-more:disabled { opacity: 0.3; cursor: default; }
.results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; }
.result-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 6px; overflow: hidden; cursor: pointer; transition: border-color 0.2s, transform 0.2s; }
.result-card:hover { border-color: #a29bfe; transform: translateY(-3px); }
.result-card .cover { aspect-ratio: 3/4; overflow: hidden; background: #17111f; }
.result-card .cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
.result-card .info { padding: 10px 10px 12px; }
.result-card .title { margin: 0 0 3px; font-size: 0.82rem; color: #f5f3fb; font-weight: 600; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.result-card .author { margin: 0 0 4px; font-size: 0.7rem; color: rgba(255,255,255,0.45); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.result-card .status { font-size: 0.65rem; font-family: 'Cinzel', serif; letter-spacing: 0.04em; color: #c9bffc; }
footer { background: rgba(14,10,23,0.95); border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 32px; text-align: center; }
footer p { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(245,243,251,0.5); margin: 0; }
@media (max-width: 480px) { .results-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); } }
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
      <a href="browse.php" class="active">BROWSE</a>
      <a href="../../logout.php">LOGOUT</a>
    </nav>
  </header>

  <main>
    <h1>Browse Manhwa</h1>
    <p class="subtitle">Search across all sources at once. Click any title to add it to your library.</p>

    <div class="search-bar">
      <input type="text" id="search-input" placeholder="Search by title..." onkeydown="if(event.key==='Enter') doSearch()">
      <button onclick="doSearch()">SEARCH</button>
    </div>
    <div class="filters">
      <select id="filter-genre" onchange="doSearch()">
        <option value="">All Genres</option>
        <option value="BL">BL</option>
        <option value="GL">GL</option>
        <option value="Romance">Romance</option>
        <option value="Comedy">Comedy</option>
        <option value="Action">Action</option>
        <option value="Fantasy">Fantasy</option>
        <option value="Horror">Horror</option>
        <option value="Mystery">Mystery</option>
        <option value="Thriller">Thriller</option>
        <option value="Tragedy">Tragedy</option>
        <option value="Psychological">Psychological</option>
        <option value="Slice of Life">Slice of Life</option>
        <option value="Adventure">Adventure</option>
        <option value="Historical">Historical</option>
        <option value="Sci-Fi">Sci-Fi</option>
        <option value="Sports">Sports</option>
        <option value="Mecha">Mecha</option>
        <option value="Wuxia">Wuxia</option>
        <option value="Isekai">Isekai</option>
        <option value="Martial Arts">Martial Arts</option>
        <option value="Supernatural">Supernatural</option>
        <option value="Drama">Drama</option>
        <option value="Crime">Crime</option>
        <option value="Superhero">Superhero</option>
        <option value="Magical Girls">Magical Girls</option>
        <option value="Philosophical">Philosophical</option>
        <option value="Medical">Medical</option>
        <option value="Harem">Harem</option>
        <option value="Reincarnation">Reincarnation</option>
        <option value="School Life">School Life</option>
        <option value="Villainess">Villainess</option>
        <option value="Post-Apocalyptic">Post-Apocalyptic</option>
        <option value="Survival">Survival</option>
        <option value="Demons">Demons</option>
        <option value="Vampires">Vampires</option>
        <option value="Zombies">Zombies</option>
        <option value="Military">Military</option>
        <option value="Mafia">Mafia</option>
        <option value="Time Travel">Time Travel</option>
        <option value="Music">Music</option>
        <option value="Cooking">Cooking</option>
        <option value="Delinquents">Delinquents</option>
        <option value="Office Workers">Office Workers</option>
        <option value="Reverse Harem">Reverse Harem</option>
        <option value="Genderswap">Genderswap</option>
      </select>
      <select id="filter-lang" onchange="doSearch()">
        <option value="en">English</option>
        <option value="">Any Language</option>
        <option value="fr">French</option>
        <option value="es">Spanish</option>
        <option value="pt-br">Portuguese (BR)</option>
        <option value="de">German</option>
        <option value="id">Indonesian</option>
        <option value="tr">Turkish</option>
        <option value="ru">Russian</option>
        <option value="ar">Arabic</option>
      </select>
    </div>

    <div class="source-tabs">
      <button class="source-tab active" data-src="mangadex" onclick="switchTab('mangadex')">MangaDex</button>
      <button class="source-tab" data-src="comick" onclick="switchTab('comick')">ComicK</button>
    </div>

    <div class="source-section active" id="section-mangadex">
      <div class="section-top">
        <div class="section-title"><span class="source-badge mangadex">MangaDex</span></div>
        <span class="source-status" id="status-mangadex">Loading...</span>
      </div>
      <div class="results-grid" id="results-mangadex"></div>
      <button class="load-more" id="more-mangadex" onclick="loadMoreMangaDex()" style="display:none">LOAD MORE</button>
    </div>

    <div class="source-section" id="section-comick">
      <div class="section-top">
        <div class="section-title"><span class="source-badge comick">ComicK</span></div>
        <span class="source-status" id="status-comick">Loading...</span>
      </div>
      <div class="results-grid" id="results-comick"></div>
      <button class="load-more" id="more-comick" onclick="loadMoreComicK()" style="display:none">LOAD MORE</button>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> &mdash; The Obscured Index. All rights reserved.</p>
  </footer>
</div>

<script>
const MDX_CACHE_KEY = 'browse_cache_mangadex_v2';

const COMICK_GENRE_MAP = {
  'BL': 35, 'GL': 31, 'Romance': 264, 'Comedy': 247, 'Action': 244,
  'Fantasy': 252, 'Horror': 256, 'Mystery': 261, 'Thriller': 292,
  'Tragedy': 273, 'Psychological': 263, 'Slice of Life': 269,
  'Adventure': 245, 'Historical': 255, 'Sci-Fi': 266, 'Sports': 271,
  'Mecha': 258, 'Wuxia': 293, 'Isekai': 278, 'Martial Arts': 257,
  'Supernatural': 272, 'Drama': 250, 'Crime': 288, 'Superhero': 303,
  'Magical Girls': 289, 'Harem': 253, 'Reincarnation': 311,
  'School Life': 310, 'Villainess': 322, 'Post-Apocalyptic': 316,
  'Survival': 321, 'Demons': 297, 'Vampires': 323, 'Zombies': 330,
  'Military': 259, 'Mafia': 318, 'Time Travel': 324, 'Music': 260,
  'Cooking': 248, 'Delinquents': 298, 'Office Workers': 319,
  'Reverse Harem': 265, 'Genderswap': 296
};

let allResults = { mangadex: [], comick: [] };
let mdxOffset = 0, comickPage = 1;
let lastQ = '', lastGenre = '', lastLang = 'en';

function switchTab(src) {
  document.querySelectorAll('.source-tab').forEach(t => t.classList.toggle('active', t.dataset.src === src));
  document.querySelectorAll('.source-section').forEach(s => s.classList.toggle('active', s.id === 'section-' + src));
  // Trigger load for that source if not yet loaded
  if (src === 'comick' && allResults.comick.length === 0) loadComicK(lastQ, lastGenre, false);
  if (src === 'mangadex' && allResults.mangadex.length === 0) loadMangaDex(lastQ, lastGenre, lastLang, false);
}

function doSearch() {
  mdxOffset = 0; comickPage = 1;
  allResults = { mangadex: [], comick: [] };
  document.getElementById('results-mangadex').innerHTML = '';
  document.getElementById('results-comick').innerHTML = '';
  document.getElementById('more-mangadex').style.display = 'none';
  document.getElementById('more-comick').style.display = 'none';
  loadAll();
}

async function loadAll() {
  lastQ     = document.getElementById('search-input').value.trim();
  lastGenre = document.getElementById('filter-genre').value;
  lastLang  = document.getElementById('filter-lang').value;
  loadMangaDex(lastQ, lastGenre, lastLang, false);
  loadComicK(lastQ, lastGenre, false);
}

async function loadMangaDex(q, genre, lang, append) {
  const isDefault = !q && !genre && lang === 'en' && mdxOffset === 0;
  if (!append) setStatus('mangadex', 'Loading...');

  if (isDefault && mdxOffset === 0) {
    const cached = sessionStorage.getItem(MDX_CACHE_KEY);
    if (cached) { renderSource('mangadex', JSON.parse(cached), false); return; }
  }

  try {
    const res = await fetch(`search_browse.php?source=mangadex&q=${encodeURIComponent(q)}&genre=${encodeURIComponent(genre)}&lang=${encodeURIComponent(lang)}&offset=${mdxOffset}`);
    const data = await res.json();
    if (isDefault && data.length) sessionStorage.setItem(MDX_CACHE_KEY, JSON.stringify(data));
    renderSource('mangadex', data, append);
    mdxOffset += data.length;
    document.getElementById('more-mangadex').style.display = data.length >= 24 ? 'block' : 'none';
  } catch {
    setStatus('mangadex', 'Failed to load.');
  }
}

async function loadComicK(q, genre, append) {
  if (!append) setStatus('comick', 'Loading...');
  try {
    const res = await fetch(`search_browse.php?source=comick&q=${encodeURIComponent(q)}&genre=${encodeURIComponent(genre)}&page=${comickPage}`);
    let data = await res.json();
    if (genre && COMICK_GENRE_MAP[genre]) {
      const gid = COMICK_GENRE_MAP[genre];
      data = data.filter(r => r.comick_genres && r.comick_genres.includes(gid));
    }
    renderSource('comick', data, append);
    comickPage++;
    document.getElementById('more-comick').style.display = data.length >= 20 ? 'block' : 'none';
  } catch {
    setStatus('comick', 'Failed to load.');
  }
}

function loadMoreMangaDex() {
  const btn = document.getElementById('more-mangadex');
  btn.disabled = true; btn.textContent = 'Loading...';
  loadMangaDex(lastQ, lastGenre, lastLang, true).then(() => { btn.disabled = false; btn.textContent = 'LOAD MORE'; });
}

function loadMoreComicK() {
  const btn = document.getElementById('more-comick');
  btn.disabled = true; btn.textContent = 'Loading...';
  loadComicK(lastQ, lastGenre, true).then(() => { btn.disabled = false; btn.textContent = 'LOAD MORE'; });
}

function renderSource(src, data, append) {
  if (!append) allResults[src] = data;
  else allResults[src] = allResults[src].concat(data);
  const total = allResults[src].length;
  if (!total) { setStatus(src, 'No results found.'); return; }
  setStatus(src, `${total} results`);
  if (append) {
    const startIdx = total - data.length;
    document.getElementById('results-' + src).insertAdjacentHTML('beforeend', data.map((r, i) => cardHtml(src, startIdx + i, r)).join(''));
  } else {
    document.getElementById('results-' + src).innerHTML = data.map((r, i) => cardHtml(src, i, r)).join('');
  }
}

function cardHtml(src, i, r) {
  return `<div class="result-card" onclick="selectResult('${src}', ${i})">
    <div class="cover"><img src="${escHtml(r.cover)}" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.style.display='none'"></div>
    <div class="info">
      <p class="title">${escHtml(r.title)}</p>
      <p class="author">${escHtml(r.author || '')}</p>
      <span class="status">${escHtml(r.status)}</span>
    </div>
  </div>`;
}

function setStatus(src, msg) {
  document.getElementById('status-' + src).textContent = msg;
}

function selectResult(src, i) {
  const r = allResults[src][i];
  const params = new URLSearchParams({ id: r.id, title: r.title, author: r.author || '', description: r.description || '', status: r.status, cover: r.cover, genre: r.genre, source: src });
  window.location.href = 'preview.php?' + params.toString();
}

function escHtml(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

doSearch();
</script>
</body>
</html>
