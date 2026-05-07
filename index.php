<?php
date_default_timezone_set('Asia/Jakarta');

// Ambil path dari REQUEST_URI (bersih dari query string)
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route  = strtolower(trim($uri, '/'));

switch ($route) {
    case '':
    case 'home':
        require __DIR__ . '/views/home.php';
        break;

    case 'music':
        require __DIR__ . '/views/music.php';
        break;

    case 'movies':
        require __DIR__ . '/views/movies.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}