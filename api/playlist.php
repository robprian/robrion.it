<?php
header('Content-Type: application/json');
$config = require_once __DIR__ . '/../config/app.php';

$music_dir = $config['music_dir'];
$playlist = [];

if (is_dir($music_dir)) {
    $files = scandir($music_dir);
    $artist_index = 0;

    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext === 'mp3') {
            $title = pathinfo($file, PATHINFO_FILENAME);
            $clean_title = ucwords(str_replace(['_', '-'], ' ', $title));

            $playlist[] = [
                'title' => $clean_title,
                'artist' => $config['default_artists'][$artist_index % count($config['default_artists'])],
                'url' => '/api/stream.php?file=' . urlencode($file)
            ];
            $artist_index++;
        }
    }
}

echo json_encode(['status' => 'success', 'data' => $playlist, 'path_scanned' => $music_dir]);
