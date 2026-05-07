<?php
$route = isset($_GET['route']) ? $_GET['route'] : '';

// Bersihkan trailing slashes
$route = trim($route, '/');

// Front Controller Router
switch ($route) {
    case '':
    case 'home':
        require 'views/home.php';
        break;
    case 'music':
    case 'movies':
        // Teruskan handling active state ke app.js dengan URL window.location
        require 'views/player.php';
        break;
    default:
        http_response_code(404);
        echo "<h1 style='text-align:center; color:white; margin-top:20vh;'>404 Not Found</h1>";
        break;
}