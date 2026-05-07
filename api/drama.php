<?php
/**
 * DramaBox API Proxy
 * Mengatasi masalah CORS saat browser fetch ke api.sansekai.my.id
 * 
 * Usage: /api/drama.php?endpoint=foryou
 *        /api/drama.php?endpoint=allepisode&bookId=42000009621
 *        /api/drama.php?endpoint=decrypt&url=xxx
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$base = 'https://api.sansekai.my.id/api/dramabox';

$endpoint = $_GET['endpoint'] ?? '';
if (!$endpoint || !preg_match('/^[a-z]+$/i', $endpoint)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid endpoint']);
    exit;
}

// Build query params (forward all except 'endpoint')
$params = $_GET;
unset($params['endpoint']);
$qs = http_build_query($params);
$url = $base . '/' . $endpoint . ($qs ? '?' . $qs : '');

// Fetch from upstream API
$ctx = stream_context_create([
    'http' => [
        'timeout' => 15,
        'header'  => "Accept: application/json\r\nUser-Agent: Robrion-Proxy/1.0\r\n"
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false
    ]
]);

$response = @file_get_contents($url, false, $ctx);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Upstream API error', 'url' => $url]);
    exit;
}

// Log first episode structure for debugging
$decoded = json_decode($response, true);
if ($endpoint === 'allepisode' && is_array($decoded)) {
    $sample = is_array($decoded) ? (isset($decoded[0]) ? $decoded[0] : null) : null;
    if (!$sample && isset($decoded['data']) && is_array($decoded['data'])) {
        $sample = $decoded['data'][0] ?? null;
    }
    if (!$sample && isset($decoded['chapterList']) && is_array($decoded['chapterList'])) {
        $sample = $decoded['chapterList'][0] ?? null;
    }
    if ($sample) {
        error_log('[DramaProxy] allepisode sample keys: ' . implode(', ', array_keys($sample)));
        error_log('[DramaProxy] allepisode sample: ' . substr(json_encode($sample), 0, 500));
    }
}

echo $response;
