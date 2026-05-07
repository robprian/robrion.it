<?php
/**
 * Sansekai API Proxy – supports ALL providers
 * Usage: /api/drama.php?provider=dramabox&endpoint=foryou&bookId=123
 *        /api/drama.php?provider=pinedrama&endpoint=trending
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$base     = 'https://api.sansekai.my.id/api';
$provider = preg_replace('/[^a-z0-9]/i', '', $_GET['provider'] ?? 'dramabox');
$endpoint = preg_replace('/[^a-z0-9\-]/i', '', $_GET['endpoint'] ?? '');

if (!$endpoint) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing endpoint']);
    exit;
}

// Build query (forward all params except provider/endpoint)
$params = $_GET;
unset($params['provider'], $params['endpoint']);
$qs  = http_build_query($params);
$url = "$base/$provider/$endpoint" . ($qs ? "?$qs" : '');

$ctx = stream_context_create([
    'http' => [
        'timeout' => 20,
        'header'  => "Accept: */*\r\nUser-Agent: Robrion-Proxy/2.0\r\n"
    ],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
]);

$response = @file_get_contents($url, false, $ctx);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Upstream API error', 'url' => $url]);
    exit;
}

echo $response;
