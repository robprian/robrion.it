<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player – WaveForm | robrion.my.id</title>
    <meta name="description" content="WaveForm Music Player – Putar koleksi musik dengan desain neumorphism premium.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/player.css">
</head>
<body>
<div class="app-container">

    <!-- HEADER -->
    <header class="header">
        <a href="/" class="logo">
            <i class="fa-solid fa-wave-square" style="color:var(--accent)"></i> WAVEFORM
        </a>
        <nav class="nav-links">
            <a href="/"><i class="fa-solid fa-house"></i> Home</a>
            <a href="/music" class="active"><i class="fa-solid fa-music"></i> Music</a>
            <a href="/movies"><i class="fa-solid fa-clapperboard"></i> Movies</a>
        </nav>
        <div class="header-right">
            <div class="search-wrap">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" class="search-bar" id="search-input" placeholder="Search songs...">
            </div>
            <a href="https://github.com/robprian" target="_blank" class="profile-btn" title="GitHub">
                <i class="fa-brands fa-github"></i>
            </a>
        </div>
    </header>

    <!-- MAIN -->
    <main class="main-content">

        <!-- PLAYER -->
        <section class="player-section" id="view-player">
            <div class="mobile-top-bar">
                <button class="icon-btn" onclick="history.back()">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <span class="top-bar-title">Now Playing</span>
                <a href="/movies" class="icon-btn" title="Movies">
                    <i class="fa-solid fa-clapperboard"></i>
                </a>
            </div>

            <!-- Album Art + Track Info -->
            <div class="track-info-large">
                <div class="album-art" id="album-art-wrap">
                    <div id="album-art-inner"></div>
                </div>
                <div class="track-details">
                    <h1 id="player-title" class="track-name">Pilih Lagu</h1>
                    <p id="player-artist" class="track-artist">Unknown Artist</p>
                    <div class="waveform-visualizer" id="waveform-viz">
                        <?php for($i=0;$i<32;$i++): ?><div class="wv-bar"></div><?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div class="progress-container">
                <div class="progress-bar-bg" id="progress-bar" onclick="seekAudio(event)">
                    <div class="progress-fill" id="progress-fill"></div>
                    <div class="progress-thumb" id="progress-thumb"></div>
                </div>
                <div class="time-info">
                    <span id="current-time">0:00</span>
                    <span id="total-time">0:00</span>
                </div>
            </div>

            <!-- Controls -->
            <div class="controls">
                <button class="btn-control" id="btn-shuffle" onclick="toggleShuffle()" title="Shuffle">
                    <i class="fa-solid fa-shuffle"></i>
                </button>
                <button class="btn-control" onclick="prevSong()" title="Previous">
                    <i class="fa-solid fa-backward-step"></i>
                </button>
                <button class="btn-control btn-play" id="btn-play" onclick="togglePlay(event)">
                    <i class="fa-solid fa-play" id="play-icon"></i>
                </button>
                <button class="btn-control" onclick="nextSong()" title="Next">
                    <i class="fa-solid fa-forward-step"></i>
                </button>
                <button class="btn-control" id="btn-repeat" onclick="toggleRepeat()" title="Repeat">
                    <i class="fa-solid fa-repeat"></i>
                </button>
            </div>

            <!-- Secondary Controls -->
            <div class="secondary-controls">
                <div class="vol-control">
                    <button class="icon-btn-sm" onclick="toggleMute()">
                        <i class="fa-solid fa-volume-high" id="vol-icon"></i>
                    </button>
                    <div class="progress-bar-bg vol-bar" id="vol-bar" onclick="setVolume(event)">
                        <div class="progress-fill" id="vol-fill" style="width:80%"></div>
                    </div>
                </div>
                <div class="visualizer-row">
                    <span class="ctrl-label"><i class="fa-solid fa-sliders"></i> Visual</span>
                    <div class="visualizer-container" id="visualizer">
                        <div class="visualizer-bar"></div>
                        <div class="visualizer-bar"></div>
                        <div class="visualizer-bar"></div>
                        <div class="visualizer-bar"></div>
                        <div class="visualizer-bar"></div>
                    </div>
                    <span class="ctrl-label">Playback</span>
                    <label class="toggle-switch">
                        <input type="checkbox" checked id="crossfade-toggle">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="extra-controls">
                    <a href="/movies" class="icon-btn-sm" title="Buka Movies">
                        <i class="fa-solid fa-clapperboard"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- PLAYLIST -->
        <aside class="playlist-section" id="view-playlist">
            <div class="playlist-header">
                <span class="playlist-title">Current Playlist</span>
                <span class="playlist-badge" id="playlist-count">0</span>
            </div>
            <div class="playlist-list" id="playlist-container">
                <div class="loading-state">
                    <i class="fa-solid fa-circle-notch fa-spin"></i><span>Memuat playlist...</span>
                </div>
            </div>
        </aside>

    </main>

    <!-- MOBILE NAV -->
    <nav class="mobile-bottom-nav">
        <button class="nav-btn" onclick="location.href='/'">
            <i class="fa-solid fa-house"></i><span>Home</span>
        </button>
        <button class="nav-btn active">
            <i class="fa-solid fa-music"></i><span>Music</span>
        </button>
        <button class="nav-btn" onclick="location.href='/movies'">
            <i class="fa-solid fa-clapperboard"></i><span>Movies</span>
        </button>
        <a href="https://github.com/robprian" target="_blank" class="nav-btn">
            <i class="fa-brands fa-github"></i><span>GitHub</span>
        </a>
    </nav>
</div>

<audio id="audio-player" preload="none"></audio>
<script src="/assets/js/music.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    initMusic();

    // Search filter
    document.getElementById('search-input')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.playlist-item').forEach(el => {
            const txt = el.querySelector('.item-title')?.innerText.toLowerCase() || '';
            el.style.display = txt.includes(q) ? '' : 'none';
        });
    });
});
</script>
</body>
</html>
