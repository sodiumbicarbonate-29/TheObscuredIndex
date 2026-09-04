<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit(); }

header('Content-Type: application/json');

$query        = trim($_GET['q'] ?? '');
$filter_genre = trim($_GET['genre'] ?? '');
$lang         = trim($_GET['lang'] ?? 'en');
$source       = trim($_GET['source'] ?? 'mangadex');
$offset       = max(0, (int)($_GET['offset'] ?? 0));
$page         = max(1, (int)($_GET['page'] ?? 1));
$results      = [];

function curl_get($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    return $raw;
}

if ($source === 'comick') {
    $url = 'https://api.comick.dev/v1.0/search?limit=100&type=comic&country=kr';
    if ($query) $url .= '&q=' . urlencode($query);
    else        $url .= '&sort=follow&page=' . $page;
    if ($lang && $lang !== 'any') $url .= '&tl=' . urlencode($lang);

    $data = json_decode(curl_get($url), true);
    foreach (($data ?? []) as $m) {
        $title  = $m['title'] ?? '';
        if (!$title) continue;
        $b2key  = $m['md_covers'][0]['b2key'] ?? '';
        $cover  = $b2key ? "https://meo.comick.pictures/{$b2key}" : '';
        $statusMap = [1 => 'Ongoing', 2 => 'Completed', 3 => 'Cancelled', 4 => 'Hiatus'];
        $results[] = [
            'id'             => $m['hid'],
            'title'          => $title,
            'author'         => '',
            'description'    => strip_tags($m['desc'] ?? ''),
            'status'         => $statusMap[$m['status'] ?? 1] ?? 'Ongoing',
            'cover'          => $cover,
            'genre'          => 'No Romance',
            'source'         => 'comick',
            'comick_genres'  => $m['genres'] ?? [],
        ];
    }
    echo json_encode($results);
    exit();
}

// MangaDex
$tagMap = [
    'BL'              => '5920b825-4181-4a17-beeb-9918b0ff7a30',
    'GL'              => 'a3c67850-4684-404e-9b7f-c69850ee5da6',
    'Romance'         => '423e2eae-a7a2-4a8b-ac03-a8351462d71d',
    'Comedy'          => '4d32cc48-9f00-4cca-9b5a-a839f0764984',
    'Action'          => '391b0423-d847-456f-aff0-8b0cfc03066b',
    'Fantasy'         => 'cdc58593-87dd-415e-bbc0-2ec27bf404cc',
    'Horror'          => 'cdad7e68-1419-41dd-bdce-27753074a640',
    'Mystery'         => 'ee968100-4191-4968-93d3-f82d72be7e46',
    'Thriller'        => '07251805-a27e-4d59-b488-f0bfbec15168',
    'Tragedy'         => 'f8f62932-27da-4fe4-8ee1-6779a8c5edba',
    'Psychological'   => '3b60b75c-a2d7-4860-ab56-05f391bb889c',
    'Slice of Life'   => 'e5301a23-ebd9-49dd-a0cb-2add944c7fe9',
    'Adventure'       => '87cc87cd-a395-47af-b27a-93258283bbc6',
    'Historical'      => '33771934-028e-4cb3-8744-691e866a923e',
    'Sci-Fi'          => '256c8bd9-4904-4360-bf4f-508a76d67183',
    'Sports'          => '69964a64-2f90-4d33-beeb-f3ed2875eb4c',
    'Mecha'           => '50880a9d-5440-4732-9afb-8f457127e836',
    'Wuxia'           => 'acc803a4-c95a-4c22-86fc-eb6b582d82a2',
    'Isekai'          => 'ace04997-f6bd-436e-b261-779182193d3d',
    'Martial Arts'    => '799c202e-7daa-44eb-9cf7-8a3c0441531e',
    'Supernatural'    => 'eabc5b4c-6aff-42f3-b657-3e90cbd00b75',
    'Drama'           => 'b9af3a63-f058-46de-a9a0-e0c13906197a',
    'Crime'           => '5ca48985-9a9d-4bd8-be29-80dc0303db72',
    'Superhero'       => '7064a261-a137-4d3a-8848-2d385de3a99c',
    'Magical Girls'   => '81c836c9-914a-4eca-981a-560dad663e73',
    'Philosophical'   => 'b1e97889-25b4-4258-b28b-cd7f4d28ea9b',
    'Medical'         => 'c8cbe35b-1b2b-4a3f-9c37-db84c4514856',
    'Harem'           => 'aafb99c1-7f60-43fa-b75f-fc9502ce29c7',
    'Reincarnation'   => '0bc90acb-ccc1-44ca-a34a-b9f3a73259d0',
    'School Life'     => 'caaa44eb-cd40-4177-b930-79d3ef2afe87',
    'Villainess'      => 'd14322ac-4d6f-4e9b-afd9-629d5f4d8a41',
    'Post-Apocalyptic'=> '9467335a-1b83-4497-9231-765337a00b96',
    'Survival'        => '5fff9cde-849c-4d78-aab0-0d52b2ee1d25',
    'Demons'          => '39730448-9a5f-48a2-85b0-a70db87b1233',
    'Vampires'        => 'd7d1730f-6eb0-4ba6-9437-602cac38664c',
    'Zombies'         => '631ef465-9aba-4afb-b0fc-ea10efe274a8',
    'Military'        => 'ac72833b-c4e9-4878-b9db-6c8a4a99444a',
    'Mafia'           => '85daba54-a71c-4554-8a28-9901a8b0afad',
    'Time Travel'     => '292e862b-2d17-4062-90a2-0356caa4ae27',
    'Music'           => 'f42fbf9e-188a-447b-9fdc-f19dc1e4d685',
    'Cooking'         => 'ea2bc92d-1c26-4930-9b7c-d5c0dc1b6869',
    'Delinquents'     => 'da2d50ca-3018-4cc0-ac7a-6b7d472a29ea',
    'Office Workers'  => '92d6d951-ca5e-429c-ac78-451071cbf064',
    'Reverse Harem'   => '65761a2a-415e-47f3-bef2-a9dababba7a6',
    'Genderswap'      => '2bd2e8d0-f146-434a-9b51-fc9ff2c5fe6a',
];

$isDefault = !$query && !$filter_genre && $lang === 'en' && $offset === 0;
$limit = $isDefault ? 24 : 100;
$base = 'https://api.mangadex.org/manga?originalLanguage[]=ko&includes[]=cover_art&includes[]=author&limit=' . $limit . '&offset=' . $offset . '&hasAvailableChapters=true&contentRating[]=safe&contentRating[]=suggestive&contentRating[]=erotica&contentRating[]=pornographic';
$base .= $isDefault ? '&order[followedCount]=desc' : '&order[relevance]=desc';
if ($lang) $base .= '&availableTranslatedLanguage[]=' . urlencode($lang);
if ($filter_genre && isset($tagMap[$filter_genre])) $base .= '&includedTags[]=' . $tagMap[$filter_genre];
$url = $query ? $base . '&title=' . urlencode($query) : $base;

$data = json_decode(curl_get($url), true);
foreach (($data['data'] ?? []) as $m) {
    $attr  = $m['attributes'];
    $title = $attr['title']['en'] ?? $attr['title'][array_key_first($attr['title'] ?? [''])] ?? '';
    if (!$title) continue;
    $author = $coverId = '';
    foreach (($m['relationships'] ?? []) as $rel) {
        if ($rel['type'] === 'author')    $author  = $rel['attributes']['name'] ?? '';
        if ($rel['type'] === 'cover_art') $coverId = $rel['attributes']['fileName'] ?? '';
    }
    $statusMap = ['ongoing' => 'Ongoing', 'completed' => 'Completed', 'hiatus' => 'Hiatus', 'cancelled' => 'Dropped'];
    $tagNames = array_map(fn($t) => strtolower($t['attributes']['name']['en'] ?? ''), $attr['tags'] ?? []);
    $priority = ["boys' love"=>'BL','bl'=>'BL',"girls' love"=>'GL','gl'=>'GL','romance'=>'Romance','harem'=>'Harem','villainess'=>'Villainess','isekai'=>'Isekai','psychological'=>'Psychological','horror'=>'Horror','thriller'=>'Thriller','tragedy'=>'Tragedy','mystery'=>'Mystery','slice of life'=>'Slice of Life','comedy'=>'Comedy','sports'=>'Sports','mecha'=>'Mecha','wuxia'=>'Wuxia','martial arts'=>'Martial Arts','military'=>'Military','sci-fi'=>'Sci-Fi','supernatural'=>'Supernatural','fantasy'=>'Fantasy','historical'=>'Historical','adventure'=>'Adventure','action'=>'Action'];
    $detected_genre = 'No Romance';
    foreach ($priority as $tag => $mapped) {
        if (in_array($tag, $tagNames)) { $detected_genre = $mapped; break; }
    }
    $results[] = [
        'id'          => $m['id'],
        'title'       => $title,
        'author'      => $author,
        'description' => strip_tags($attr['description']['en'] ?? ''),
        'status'      => $statusMap[$attr['status']] ?? 'Ongoing',
        'cover'       => $coverId ? "https://uploads.mangadex.org/covers/{$m['id']}/{$coverId}.256.jpg" : '',
        'genre'       => $detected_genre,
        'source'      => 'mangadex',
    ];
}

echo json_encode($results);
