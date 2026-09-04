<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;
if (is_file($file)) return false;
$index = rtrim($file, '/') . '/index.php';
if (is_file($index)) { $_SERVER['SCRIPT_FILENAME'] = $index; include $index; return true; }
return false;
