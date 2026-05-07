<?php
// Konfigurasi aplikasi, bisa disesuaikan dengan Environment Variables (CI/CD friendly)
return [
    // Menggunakan environment variable jika ada, jika tidak periksa path produksi, lalu fallback ke direktori lokal
    'music_dir' => getenv('MUSIC_DIR') ?: (is_dir('/var/www/robrion/robrion') ? '/var/www/robrion/robrion' : __DIR__ . '/../music'),
    'app_name' => 'WaveForm Music',
    'default_artists' => ['robrionit', 'aprilianingsih', 'Synthetix Wave']
];
