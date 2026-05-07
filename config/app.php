<?php
return [
    'music_dir' => getenv('MUSIC_DIR') ?: (is_dir('/var/www/robrion/robrion') ? '/var/www/robrion/robrion' : __DIR__ . '/../music'),
    'movie_dir' => getenv('MOVIE_DIR') ?: (is_dir('/var/www/robrion/movies') ? '/var/www/robrion/movies' : __DIR__ . '/../movies'),
    'app_name' => 'WaveForm Music & Movies',
    'default_artists' => ['robrionit', 'aprilianingsih', 'Synthetix Wave']
];
