<?php
$config = require_once __DIR__ . '/../config/app.php';
$music_dir = $config['music_dir'];

if (!isset($_GET['file'])) {
    http_response_code(400);
    exit("No file specified");
}

// Mencegah path traversal (membaca file di luar direktori yang diizinkan)
$file = basename($_GET['file']);
$path = $music_dir . DIRECTORY_SEPARATOR . $file;

if (file_exists($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'mp3') {
    $size = filesize($path);
    header('Content-Type: audio/mpeg');
    header('Content-Length: ' . $size);
    header('Accept-Ranges: bytes');
    
    // Mendukung request range untuk seek (penting untuk web player di mobile)
    if (isset($_SERVER['HTTP_RANGE'])) {
        list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
        list($range) = explode(",", $range, 2);
        list($range, $range_end) = explode("-", $range);
        $range = intval($range);
        if (!$range_end) {
            $range_end = $size - 1;
        } else {
            $range_end = intval($range_end);
        }
        $new_length = $range_end - $range + 1;
        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $new_length");
        header("Content-Range: bytes $range-$range_end/$size");
        $f = fopen($path, 'rb');
        fseek($f, $range);
        echo fread($f, $new_length);
        fclose($f);
    } else {
        readfile($path);
    }
    exit;
} else {
    http_response_code(404);
    exit("File not found");
}
