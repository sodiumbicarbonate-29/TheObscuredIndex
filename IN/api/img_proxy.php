<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit(); }

$url = $_GET['url'] ?? '';
if (!$url || !str_starts_with($url, 'https://')) { http_response_code(400); exit(); }

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0',
    CURLOPT_REFERER        => 'https://mangadex.org/',
    CURLOPT_HTTPHEADER     => ['Accept: image/webp,image/apng,image/*,*/*'],
]);
$data = curl_exec($ch);
$ct   = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$data || $code !== 200) { http_response_code(404); exit(); }

header('Content-Type: ' . ($ct ?: 'image/jpeg'));
header('Cache-Control: public, max-age=86400');
echo $data;
