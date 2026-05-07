<?php
$config = require_once __DIR__ . '/../config/app.php';
$movie_dir = $config['movie_dir'];

if (!isset($_GET['file'])) {
    http_response_code(400);
    exit("No file specified");
}

$file = basename($_GET['file']);
$path = $movie_dir . DIRECTORY_SEPARATOR . $file;

if (file_exists($path) && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4', 'mkv', 'webm'])) {
    $size = filesize($path);
    $mime = 'video/mp4'; 
    if (str_ends_with(strtolower($file), 'webm')) $mime = 'video/webm';
    if (str_ends_with(strtolower($file), 'mkv')) $mime = 'video/x-matroska';
    
    header("Content-Type: $mime");
    header("Accept-Ranges: bytes");
    
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
        header("Content-Length: $size");
        readfile($path);
    }
    exit;
} else {
    http_response_code(404);
    exit("File not found");
}
