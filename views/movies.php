<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streaming Hub – Movies & Anime | robrion.my.id</title>
    <meta name="description" content="Nonton drama & anime online dari 9 provider – DramaBox, PineDrama, ReelShort, ShortMax, GoodShort, NetShort, FreeReels, DramaNova & Anime.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/player.css">
    <style>
    /* ── Provider Selector Bar ── */
    .provider-scroll{display:flex;gap:10px;overflow-x:auto;padding:6px 2px 10px;scrollbar-width:none;-webkit-overflow-scrolling:touch}
    .provider-scroll::-webkit-scrollbar{display:none}
    .provider-btn{
        display:flex;align-items:center;gap:8px;padding:10px 18px;border:none;border-radius:16px;
        font-family:inherit;font-size:.82rem;font-weight:600;cursor:pointer;white-space:nowrap;
        background:var(--bg,#e8edf2);box-shadow:var(--shadow-btn,4px 4px 12px #c8ccd0,-4px -4px 12px #fff);
        color:var(--text-light,#67748e);transition:all .25s ease;flex-shrink:0;
    }
    .provider-btn:hover{transform:translateY(-2px);color:var(--text,#2d3748)}
    .provider-btn.active{
        box-shadow:var(--shadow-in,inset 4px 4px 12px #c8ccd0,inset -4px -4px 12px #fff);
        color:#fff;
    }
    .provider-btn .prov-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
    .provider-count{font-size:.68rem;opacity:.7;margin-left:2px}

    /* ── Search bar upgrade ── */
    .search-section{display:flex;gap:10px;align-items:center;margin-bottom:8px}
    .search-section .search-wrap{flex:1}
    .search-btn{
        padding:10px 22px;border:none;border-radius:14px;font-family:inherit;font-weight:600;
        font-size:.85rem;cursor:pointer;color:#fff;background:var(--accent,#667eea);
        box-shadow:0 4px 14px rgba(102,126,234,.35);transition:all .2s;white-space:nowrap;
    }
    .search-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(102,126,234,.45)}

    /* ── Active provider title ── */
    .active-provider-name{
        display:inline-flex;align-items:center;gap:8px;padding:3px 14px;border-radius:10px;
        font-size:.78rem;font-weight:600;color:#fff;vertical-align:middle;
    }
    </style>
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
            <a href="https://github.com/robprian" target="_blank" class="profile-btn" title="GitHub">
                <i class="fa-brands fa-github"></i>
            </a>
        </div>
    </header>

    <!-- MAIN -->
    <main class="main-content movies-layout">

        <!-- PROVIDER SELECTOR BAR -->
        <section class="provider-section" style="margin-bottom:6px">
            <div class="provider-scroll" id="provider-bar">
                <button class="provider-btn active" data-provider="dramabox" onclick="switchProvider('dramabox')">
                    <span class="prov-dot" style="background:#FF6B6B"></span>
                    <i class="fa-solid fa-tv"></i> DramaBox
                </button>
                <button class="provider-btn" data-provider="pinedrama" onclick="switchProvider('pinedrama')">
                    <span class="prov-dot" style="background:#4ECDC4"></span>
                    <i class="fa-solid fa-leaf"></i> PineDrama
                </button>
                <button class="provider-btn" data-provider="reelshort" onclick="switchProvider('reelshort')">
                    <span class="prov-dot" style="background:#FF8C42"></span>
                    <i class="fa-solid fa-film"></i> ReelShort
                </button>
                <button class="provider-btn" data-provider="shortmax" onclick="switchProvider('shortmax')">
                    <span class="prov-dot" style="background:#FFD93D"></span>
                    <i class="fa-solid fa-bolt"></i> ShortMax
                </button>
                <button class="provider-btn" data-provider="goodshort" onclick="switchProvider('goodshort')">
                    <span class="prov-dot" style="background:#6BCB77"></span>
                    <i class="fa-solid fa-thumbs-up"></i> GoodShort
                </button>
                <button class="provider-btn" data-provider="netshort" onclick="switchProvider('netshort')">
                    <span class="prov-dot" style="background:#4D96FF"></span>
                    <i class="fa-solid fa-globe"></i> NetShort
                </button>
                <button class="provider-btn" data-provider="freereels" onclick="switchProvider('freereels')">
                    <span class="prov-dot" style="background:#9B59B6"></span>
                    <i class="fa-solid fa-video"></i> FreeReels
                </button>
                <button class="provider-btn" data-provider="dramanova" onclick="switchProvider('dramanova')">
                    <span class="prov-dot" style="background:#E74C3C"></span>
                    <i class="fa-solid fa-star"></i> DramaNova
                </button>
                <button class="provider-btn" data-provider="anime" onclick="switchProvider('anime')">
                    <span class="prov-dot" style="background:#E91E63"></span>
                    <i class="fa-solid fa-dragon"></i> Anime
                </button>
            </div>
        </section>

        <!-- SEARCH + CATEGORY -->
        <section class="movie-section" id="view-movies">
            <div class="movie-section-header">
                <div>
                    <h2 class="section-title" id="section-provider-title">
                        <i class="fa-solid fa-fire" style="color:var(--accent)"></i>
                        <span class="active-provider-name" id="active-prov-badge" style="background:#FF6B6B">DramaBox</span>
                        Streaming
                    </h2>
                    <p class="section-subtitle" id="section-subtitle">Klik drama untuk nonton • Pilih provider di atas untuk ganti sumber</p>
                </div>
            </div>

            <!-- Search -->
            <div class="search-section">
                <div class="search-wrap">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input type="text" class="search-bar" id="movie-search" placeholder="Cari drama / anime..."
                           onkeydown="if(event.key==='Enter')searchDrama()">
                </div>
                <button class="search-btn" onclick="searchDrama()"><i class="fa-solid fa-search"></i> Cari</button>
            </div>

            <!-- Dynamic category tabs -->
            <div class="movie-tabs" id="category-tabs" style="margin-bottom:18px">
                <!-- populated by switchProvider() -->
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
    // Boot multi-provider system
    switchProvider('dramabox');

    // Also update provider badge color when switching
    const origSwitch = window.switchProvider;
    window.switchProvider = function(key) {
        origSwitch(key);
        const prov = PROVIDERS[key];
        const badge = document.getElementById('active-prov-badge');
        if (badge && prov) {
            badge.style.background = prov.color;
            badge.textContent = prov.name;
        }
    };
    // Re-trigger to set badge
    window.switchProvider('dramabox');

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
