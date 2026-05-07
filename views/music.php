<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveForm – Suno AI Music Player | robrion.my.id</title>
    <meta name="description" content="AI-generated music player powered by Suno AI – desain neumorphism premium dengan lyric sync dan equalizer visualizer.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/player.css">
    <style>
    /* ── Album Art ── */
    .album-art-wrap {
        position: relative;
        width: 200px;
        height: 200px;
        border-radius: 24px;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 8px 8px 24px #b8c0cc, -8px -8px 24px #fff;
        background: var(--bg);
    }
    .album-art-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: blur(8px) brightness(0.6);
        transform: scale(1.1);
        transition: background-image .5s ease;
    }
    .album-art-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 24px;
        transition: opacity .4s;
    }
    .album-art-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        color: var(--text-light);
    }
    /* Spinning animation when playing */
    .album-art-wrap.spinning .album-art-img {
        animation: albumSpin 20s linear infinite;
    }
    @keyframes albumSpin {
        from { transform: rotate(0deg) scale(1.01); }
        to   { transform: rotate(360deg) scale(1.01); }
    }

    /* ── Music Tags ── */
    .music-tag {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: .68rem;
        font-weight: 600;
        background: var(--bg);
        box-shadow: var(--shadow-btn);
        color: var(--accent);
        margin: 2px;
    }
    #player-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 6px;
    }

    /* ── EQ Canvas ── */
    .eq-section {
        width: 100%;
        margin-top: 12px;
        position: relative;
    }
    .eq-label {
        font-size: .68rem;
        font-weight: 600;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }
    #eq-canvas {
        width: 100%;
        height: 72px;
        border-radius: 14px;
        background: var(--bg);
        box-shadow: var(--shadow-in);
        display: block;
    }

    /* ── Lyrics Panel ── */
    .lyrics-section {
        width: 100%;
        margin-top: 16px;
        background: var(--bg);
        border-radius: 18px;
        box-shadow: var(--shadow-in);
        overflow: hidden;
    }
    .lyrics-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px 8px;
        font-size: .78rem;
        font-weight: 600;
        color: var(--text-light);
        border-bottom: 1px solid rgba(0,0,0,.04);
    }
    #lyrics-panel {
        max-height: 180px;
        overflow-y: auto;
        padding: 12px 20px 16px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: var(--accent) transparent;
    }
    .lyric-line {
        font-size: .82rem;
        line-height: 1.85;
        color: var(--text-light);
        text-align: center;
        transition: all .35s ease;
        padding: 2px 0;
        transform: scale(0.95);
    }
    .lyric-line.lyric-active {
        color: var(--accent);
        font-size: .9rem;
        font-weight: 600;
        transform: scale(1.04);
        text-shadow: 0 0 20px rgba(102,126,234,.35);
    }
    .lyric-line.lyric-past {
        opacity: 0.35;
    }
    .lyric-empty {
        text-align: center;
        padding: 20px;
        color: var(--text-light);
        font-size: .8rem;
    }
    .lyric-empty i { display: block; font-size: 1.5rem; margin-bottom: 6px; }

    /* ── Playlist thumbnail ── */
    .item-thumb {
        background-size: cover;
        background-position: center;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .7rem;
        color: var(--text-light);
        flex-shrink: 0;
    }

    /* ── Refresh + load more ── */
    .playlist-actions {
        display: flex;
        gap: 8px;
        padding: 10px 16px;
        border-top: 1px solid rgba(0,0,0,.05);
    }
    .action-btn {
        flex: 1;
        padding: 8px;
        border: none;
        border-radius: 12px;
        font-family: inherit;
        font-size: .72rem;
        font-weight: 600;
        cursor: pointer;
        background: var(--bg);
        box-shadow: var(--shadow-btn);
        color: var(--text-light);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all .2s;
    }
    .action-btn:hover { box-shadow: var(--shadow-in); color: var(--accent); }

    /* ── Responsive layout override ── */
    @media (max-width: 900px) {
        .album-art-wrap { width: 150px; height: 150px; }
    }
    @media (max-width: 600px) {
        .album-art-wrap { width: 120px; height: 120px; }
        #eq-canvas { height: 52px; }
        #lyrics-panel { max-height: 130px; }
    }
    </style>
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
                <input type="text" class="search-bar" id="search-input" placeholder="Search songs..."
                       oninput="filterPlaylist(this.value)">
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
                <div class="album-art-wrap" id="album-art-wrap">
                    <div class="album-art-bg" id="album-art-bg"></div>
                    <img class="album-art-img" id="album-art-img" src="" alt="Album Art"
                         onerror="this.style.display='none'" style="display:none">
                    <div class="album-art-placeholder" id="album-placeholder">
                        <i class="fa-solid fa-music"></i>
                    </div>
                </div>
                <div class="track-details">
                    <h1 id="player-title" class="track-name">Suno AI Music</h1>
                    <p id="player-artist" class="track-artist">Suno AI</p>
                    <div id="player-tags"></div>
                </div>
            </div>

            <!-- EQ Visualizer Canvas -->
            <div class="eq-section">
                <div class="eq-label">
                    <i class="fa-solid fa-wave-square" style="color:var(--accent)"></i>
                    Equalizer
                </div>
                <canvas id="eq-canvas"></canvas>
            </div>

            <!-- Lyrics -->
            <div class="lyrics-section">
                <div class="lyrics-header">
                    <span><i class="fa-regular fa-file-lines" style="color:var(--accent)"></i> Lirik</span>
                    <span id="lyric-status" style="font-size:.65rem;opacity:.6">Auto Sync</span>
                </div>
                <div id="lyrics-panel">
                    <div class="lyric-empty"><i class="fa-regular fa-file-lines"></i><p>Pilih lagu untuk melihat lirik</p></div>
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

            <!-- Volume -->
            <div class="secondary-controls">
                <div class="vol-control">
                    <button class="icon-btn-sm" onclick="toggleMute()">
                        <i class="fa-solid fa-volume-high" id="vol-icon"></i>
                    </button>
                    <div class="progress-bar-bg vol-bar" id="vol-bar" onclick="setVolume(event)">
                        <div class="progress-fill" id="vol-fill" style="width:80%"></div>
                    </div>
                </div>
                <div class="extra-controls">
                    <a href="/movies" class="icon-btn-sm" title="Movies">
                        <i class="fa-solid fa-clapperboard"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- PLAYLIST -->
        <aside class="playlist-section" id="view-playlist">
            <div class="playlist-header">
                <span class="playlist-title"><i class="fa-brands fa-suse" style="color:#00e5ff;margin-right:6px"></i>Suno AI Playlist</span>
                <span class="playlist-badge" id="playlist-count">0</span>
            </div>
            <div class="playlist-list" id="playlist-container">
                <div class="loading-state">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                    <span>Memuat dari Suno AI...</span>
                </div>
            </div>
            <div class="playlist-actions">
                <button class="action-btn" onclick="refreshPlaylist()">
                    <i class="fa-solid fa-rotate"></i> Refresh API
                </button>
                <button class="action-btn" onclick="document.getElementById('search-input').focus()">
                    <i class="fa-solid fa-search"></i> Cari Lagu
                </button>
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

<audio id="audio-player" preload="none" crossorigin="anonymous"></audio>
<script src="/assets/js/music.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    initMusic();

    // Spinning album art when playing/paused
    const audio = document.getElementById('audio-player');
    const wrap  = document.getElementById('album-art-wrap');
    audio?.addEventListener('play',  () => wrap?.classList.add('spinning'));
    audio?.addEventListener('pause', () => wrap?.classList.remove('spinning'));

    // Show/hide placeholder when art loads
    const img = document.getElementById('album-art-img');
    const ph  = document.getElementById('album-placeholder');
    if (img) {
        img.addEventListener('load',  () => { img.style.display = 'block'; if(ph) ph.style.display='none'; });
        img.addEventListener('error', () => { img.style.display = 'none';  if(ph) ph.style.display='flex'; });
    }
});
</script>
</body>
</html>
