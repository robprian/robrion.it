<?php
// Front Controller Router
date_default_timezone_set('Asia/Jakarta');

// Ambil route dari request URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = trim($uri, '/');

// Dispatch routing
switch ($route) {
    case '':
    case 'home':
        require __DIR__ . '/views/home.php';
        break;

    case 'music':
    case 'movies':
        require __DIR__ . '/views/player.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}