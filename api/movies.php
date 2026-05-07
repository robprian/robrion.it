<?php
header('Content-Type: application/json');
$config = require_once __DIR__ . '/../config/app.php';

$movie_dir = $config['movie_dir'];
$movies = [];

if (is_dir($movie_dir)) {
    $files = scandir($movie_dir);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'mkv', 'webm'])) {
            $title = pathinfo($file, PATHINFO_FILENAME);
            $clean_title = ucwords(str_replace(['_', '-'], ' ', $title));

            $movies[] = [
                'title' => $clean_title,
                'url' => 'api/stream_movie.php?file=' . urlencode($file)
            ];
        }
    }
}

echo json_encode(['status' => 'success', 'data' => $movies, 'path_scanned' => $movie_dir]);
