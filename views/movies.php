<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drama Box – Movies | robrion.my.id</title>
    <meta name="description" content="Nonton drama online dengan streaming DramaBox API – desain Cinema Flow neumorphism.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/player.css">
</head>
<body>
<div class="app-container">

    <!-- HEADER -->
    <header class="header">
        <a href="/" class="logo">
            <i class="fa-solid fa-clapperboard" style="color:var(--accent)"></i> CINEMA
        </a>
        <nav class="nav-links">
            <a href="/"><i class="fa-solid fa-house"></i> Home</a>
            <a href="/music"><i class="fa-solid fa-music"></i> Music</a>
            <a href="/movies" class="active"><i class="fa-solid fa-clapperboard"></i> Movies</a>
        </nav>
        <div class="header-right">
            <div class="search-wrap">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" class="search-bar" id="movie-search" placeholder="Search drama...">
            </div>
            <a href="https://github.com/robprian" target="_blank" class="profile-btn" title="GitHub">
                <i class="fa-brands fa-github"></i>
            </a>
        </div>
    </header>

    <!-- MAIN -->
    <main class="main-content movies-layout">

        <!-- MOVIE SECTION -->
        <section class="movie-section" id="view-movies">
            <div class="movie-section-header">
                <div>
                    <h2 class="section-title">
                        <i class="fa-solid fa-fire" style="color:var(--accent)"></i> Drama Box
                    </h2>
                    <p class="section-subtitle">Streaming drama via DramaBox API &mdash; klik drama untuk nonton</p>
                </div>
                <div class="movie-tabs">
                    <button class="movie-tab active" onclick="loadDramaCategory('foryou',this)">
                        <i class="fa-solid fa-star"></i> For You
                    </button>
                    <button class="movie-tab" onclick="loadDramaCategory('trending',this)">
                        <i class="fa-solid fa-fire-flame-curved"></i> Trending
                    </button>
                    <button class="movie-tab" onclick="loadDramaCategory('latest',this)">
                        <i class="fa-solid fa-clock-rotate-left"></i> Terbaru
                    </button>
                    <button class="movie-tab" onclick="loadDramaCategory('dubindo',this)">
                        <i class="fa-solid fa-language"></i> Dub Indo
                    </button>
                </div>
            </div>

            <div class="movie-grid" id="movie-container">
                <div class="loading-state" style="grid-column:1/-1;padding:60px 0">
                    <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
                    <span>Memuat drama...</span>
                </div>
            </div>
        </section>

    </main>

    <!-- MINI MUSIC PLAYER BAR (persists from /music) -->
    <div class="mini-player-bar" id="mini-player" style="display:none">
        <div class="mini-player-inner">
            <div class="mini-thumb" id="mini-thumb"></div>
            <div class="mini-info">
                <div class="mini-title" id="mini-title">–</div>
                <div class="mini-artist" id="mini-artist">–</div>
            </div>
            <div class="mini-controls">
                <button class="btn-control" style="width:38px;height:38px;font-size:.9rem" onclick="miniPrev()">
                    <i class="fa-solid fa-backward-step"></i>
                </button>
                <button class="btn-control btn-play" style="width:46px;height:46px;font-size:1rem" onclick="miniToggle()">
                    <i class="fa-solid fa-play" id="mini-play-icon"></i>
                </button>
                <button class="btn-control" style="width:38px;height:38px;font-size:.9rem" onclick="miniNext()">
                    <i class="fa-solid fa-forward-step"></i>
                </button>
            </div>
            <a href="/music" class="icon-btn-sm" title="Buka Music Player" style="margin-left:4px">
                <i class="fa-solid fa-up-right-from-square"></i>
            </a>
            <button class="icon-btn-sm" onclick="closeMini()" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="mini-progress">
            <div class="mini-progress-fill" id="mini-progress-fill"></div>
        </div>
    </div>

    <!-- MOBILE NAV -->
    <nav class="mobile-bottom-nav">
        <button class="nav-btn" onclick="location.href='/'">
            <i class="fa-solid fa-house"></i><span>Home</span>
        </button>
        <button class="nav-btn" onclick="location.href='/music'">
            <i class="fa-solid fa-music"></i><span>Music</span>
        </button>
        <button class="nav-btn active">
            <i class="fa-solid fa-clapperboard"></i><span>Movies</span>
        </button>
        <a href="https://github.com/robprian" target="_blank" class="nav-btn">
            <i class="fa-brands fa-github"></i><span>GitHub</span>
        </a>
    </nav>
</div>

<!-- DRAMA DETAIL MODAL -->
<div class="modal-overlay" id="drama-modal" style="display:none" onclick="closeDramaModal(event)">
    <div class="drama-modal-content" id="drama-modal-content">
        <button class="modal-close" onclick="closeDramaModal()"><i class="fa-solid fa-xmark"></i></button>
        <div id="drama-modal-body"></div>
    </div>
</div>

<!-- Mini player audio -->
<audio id="mini-audio" preload="none"></audio>

<script src="/assets/js/movies.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-load drama
    loadDramaCategory('foryou', document.querySelector('.movie-tab'));

    // Search
    document.getElementById('movie-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.movie-card').forEach(c => {
            const t = c.querySelector('h3')?.innerText.toLowerCase() || '';
            c.style.display = t.includes(q) ? '' : 'none';
        });
    });

    // Mini player from localStorage
    initMiniPlayer();
});

// ── Mini Player Logic (persists music state from /music page) ──
const miniAudio   = document.getElementById('mini-audio');
const miniBar     = document.getElementById('mini-player');
let miniPlaylist  = [];
let miniIndex     = 0;
let miniPlaying   = false;

function initMiniPlayer() {
    const state = JSON.parse(localStorage.getItem('waveform_state') || 'null');
    if (!state || !state.playlist || state.playlist.length === 0) return;

    miniPlaylist = state.playlist;
    miniIndex    = state.index || 0;
    miniAudio.volume = state.volume || 0.8;

    updateMiniUI();
    miniBar.style.display = 'block';

    // Restore playback position
    miniAudio.src = miniPlaylist[miniIndex].url;
    miniAudio.currentTime = state.currentTime || 0;

    if (state.playing) {
        miniAudio.play().then(() => { miniPlaying = true; updateMiniPlayIcon(); }).catch(() => {});
    }

    miniAudio.addEventListener('timeupdate', () => {
        if (!miniAudio.duration) return;
        const pct = (miniAudio.currentTime / miniAudio.duration) * 100;
        document.getElementById('mini-progress-fill').style.width = pct + '%';
    });

    miniAudio.addEventListener('ended', miniNext);
}

function updateMiniUI() {
    if (!miniPlaylist[miniIndex]) return;
    document.getElementById('mini-title').innerText  = miniPlaylist[miniIndex].title;
    document.getElementById('mini-artist').innerText = miniPlaylist[miniIndex].artist;
}

window.miniToggle = function() {
    if (miniPlaying) {
        miniAudio.pause(); miniPlaying = false;
    } else {
        miniAudio.play().then(() => { miniPlaying = true; }).catch(() => {});
    }
    updateMiniPlayIcon();
};

window.miniNext = function() {
    miniIndex = (miniIndex + 1) % miniPlaylist.length;
    miniAudio.src = miniPlaylist[miniIndex].url;
    miniAudio.play().then(() => { miniPlaying = true; updateMiniPlayIcon(); }).catch(() => {});
    updateMiniUI();
};

window.miniPrev = function() {
    miniIndex = (miniIndex - 1 + miniPlaylist.length) % miniPlaylist.length;
    miniAudio.src = miniPlaylist[miniIndex].url;
    miniAudio.play().then(() => { miniPlaying = true; updateMiniPlayIcon(); }).catch(() => {});
    updateMiniUI();
};

window.closeMini = function() {
    miniAudio.pause();
    miniBar.style.display = 'none';
};

function updateMiniPlayIcon() {
    const ic = document.getElementById('mini-play-icon');
    if (!ic) return;
    ic.className = miniPlaying ? 'fa-solid fa-pause' : 'fa-solid fa-play';
}
</script>
</body>
</html>
