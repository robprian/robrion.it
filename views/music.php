<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Hub – Suno AI Player | robrion.my.id</title>
    <meta name="description" content="AI-generated music player powered by Suno AI – neumorphic design with lyric sync and equalizer.">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/assets/js/theme.js"></script>
    <style>
    /* ── MUSIC LAYOUT ── */
    .music-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
    }

    /* ── Album Art ── */
    .album-art-wrap {
        position: relative; width: 200px; height: 200px;
        border-radius: 22px; overflow: hidden; flex-shrink: 0;
        box-shadow: var(--shadow-out); background: var(--bg);
    }
    .album-art-bg {
        position: absolute; inset: 0; background-size: cover;
        background-position: center; filter: blur(8px) brightness(0.6);
        transform: scale(1.1); transition: background-image .5s ease;
    }
    #album-art-img {
        position: absolute; inset: 0; width: 100%; height: 100%;
        object-fit: cover; border-radius: 22px; transition: opacity .4s;
    }
    #album-placeholder {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #1a0533, #2b4590, #00e5ff); border-radius: 22px;
        color: rgba(255,255,255,.5); font-size: 3rem;
    }

    /* ── Player Track Info ── */
    .player-track-info { flex: 1; min-width: 0; }
    .player-track-info h2 {
        font-size: 1.5rem; font-weight: 700; line-height: 1.2;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    #player-artist { color: var(--text-sec); font-size: .88rem; margin-top: 4px; }
    #player-tags { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
    .music-tag {
        font-size: .65rem; padding: 3px 10px; border-radius: 12px;
        background: var(--bg); box-shadow: var(--shadow-sm); color: var(--text-muted); font-weight: 500;
    }

    /* ── EQ Canvas ── */
    .eq-wrap {
        width: 100%; height: 80px; border-radius: var(--radius-sm);
        box-shadow: var(--shadow-in); overflow: hidden; background: var(--bg);
    }
    #eq-canvas { width: 100%; height: 100%; display: block; }

    /* ── Progress ── */
    .progress-wrap { display: flex; flex-direction: column; gap: 6px; }
    .progress-bar {
        height: 8px; background: var(--bg); border-radius: 10px;
        box-shadow: var(--shadow-in); position: relative; cursor: pointer;
    }
    #progress-fill {
        position: absolute; top: 0; left: 0; height: 100%; width: 0%;
        background: var(--gradient-brand); border-radius: 10px;
        box-shadow: 0 0 10px rgba(14,165,233,0.3); pointer-events: none;
    }
    #progress-thumb {
        position: absolute; top: 50%; left: 0%; transform: translate(-50%,-50%);
        width: 16px; height: 16px; background: var(--bg-card); border-radius: 50%;
        box-shadow: var(--shadow-btn); pointer-events: none;
    }
    .time-row { display: flex; justify-content: space-between; font-size: .78rem; color: var(--text-muted); }

    /* ── Controls ── */
    .controls { display: flex; justify-content: center; align-items: center; gap: 14px; }
    .ctrl-btn {
        width: 48px; height: 48px; border-radius: 50%;
        background: var(--bg-card); box-shadow: var(--shadow-btn);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-sec); font-size: 1rem; transition: all .2s;
    }
    .ctrl-btn:hover { box-shadow: var(--shadow-active); color: var(--accent); }
    .ctrl-btn.active-btn { color: var(--accent); box-shadow: var(--shadow-active); }
    .ctrl-play {
        width: 64px; height: 64px; font-size: 1.3rem;
        background: var(--gradient-brand); color: #fff;
        box-shadow: 0 4px 20px rgba(14,165,233,0.35);
    }
    .ctrl-play:hover { box-shadow: 0 6px 28px rgba(14,165,233,0.5); color: #fff; transform: scale(1.05); }

    /* ── Volume ── */
    .vol-wrap { display: flex; align-items: center; gap: 10px; }
    .vol-icon { color: var(--text-muted); font-size: .9rem; cursor: pointer; background: none; }
    .vol-bar {
        flex: 1; height: 6px; background: var(--bg); border-radius: 8px;
        box-shadow: var(--shadow-in); cursor: pointer; position: relative;
    }
    #vol-fill {
        position: absolute; top: 0; left: 0; height: 100%; width: 80%;
        background: var(--gradient-brand); border-radius: 8px; pointer-events: none;
    }

    /* ── Mode toggles ── */
    .mode-row { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
    .mode-btn {
        padding: 6px 14px; border-radius: 10px; font-size: .75rem; font-weight: 600;
        background: var(--bg-card); box-shadow: var(--shadow-sm); color: var(--text-muted);
        display: flex; align-items: center; gap: 5px; transition: all .2s;
    }
    .mode-btn:hover, .mode-btn.active-btn { color: var(--accent); box-shadow: var(--shadow-active); }

    /* ── Lyrics Panel ── */
    .lyrics-panel {
        max-height: 220px; overflow-y: auto; padding: 12px;
        background: var(--bg); border-radius: var(--radius-sm);
        box-shadow: var(--shadow-in); font-size: .82rem; line-height: 1.8;
    }
    .lyric-line { color: var(--text-muted); transition: all .3s; padding: 2px 8px; border-radius: 6px; }
    .lyric-active { color: var(--accent); font-weight: 600; background: rgba(14,165,233,0.08); }
    .lyric-past { opacity: .4; }
    .lyric-empty { text-align: center; color: var(--text-muted); padding: 30px 10px; }

    /* ── Playlist ── */
    .playlist-wrap {
        display: flex; flex-direction: column; gap: 6px;
        max-height: calc(100vh - 200px); overflow-y: auto;
    }
    .playlist-search {
        width: 100%; padding: 10px 14px 10px 36px; border: none; border-radius: 12px;
        background: var(--bg); box-shadow: var(--shadow-in); color: var(--text);
        font-family: inherit; font-size: .82rem; outline: none;
    }
    .playlist-search::placeholder { color: var(--text-muted); }
    .playlist-item {
        display: flex; align-items: center; gap: 10px; padding: 10px 12px;
        border-radius: 12px; cursor: pointer; transition: all .2s;
        border: 1px solid transparent;
    }
    .playlist-item:hover { background: var(--bg); box-shadow: var(--shadow-sm); }
    .playlist-item.active {
        background: var(--bg); box-shadow: var(--shadow-active);
        border-color: rgba(14,165,233,0.2);
    }
    .item-index { font-size: .72rem; color: var(--text-muted); width: 20px; text-align: center; }
    .item-thumb {
        width: 42px; height: 42px; border-radius: 10px;
        background: linear-gradient(135deg,#1a0533,#2b4590); background-size: cover;
        background-position: center; flex-shrink: 0;
    }
    .item-info { flex: 1; min-width: 0; }
    .item-title { font-size: .82rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .item-artist { font-size: .68rem; color: var(--text-muted); }
    .item-duration { font-size: .72rem; color: var(--text-muted); }

    /* ── Responsive ── */
    @media(max-width: 900px) {
        .music-grid { grid-template-columns: 1fr; }
    }
    @media(max-width: 600px) {
        .album-art-wrap { width: 140px; height: 140px; }
        .player-track-info h2 { font-size: 1.15rem; }
        .eq-wrap { height: 60px; }
        .controls { gap: 10px; }
        .ctrl-btn { width: 42px; height: 42px; font-size: .9rem; }
        .ctrl-play { width: 56px; height: 56px; font-size: 1.1rem; }
    }
    </style>
</head>
<body>
<?php $page = 'music'; ?>

<!-- ── TOP BAR ── -->
<header class="top-bar">
    <div class="top-bar-left">
        <a href="/" class="top-bar-logo">ROBRION</a>
        <nav class="top-bar-nav">
            <a href="/"><i class="fa-solid fa-house"></i> Home</a>
            <a href="/music" class="active"><i class="fa-solid fa-headphones-simple"></i> Music Hub</a>
            <a href="/movies"><i class="fa-solid fa-clapperboard"></i> Cinema Box</a>
        </nav>
    </div>
    <div class="top-bar-right">
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fa-solid fa-moon"></i><i class="fa-solid fa-sun"></i>
        </button>
    </div>
</header>

<div class="app-shell">
    <div class="music-grid">

        <!-- ══ LEFT: PLAYER ══ -->
        <div class="flex-col gap-md">

            <!-- Player Card -->
            <div class="neu-card animate-in">
                <div class="section-head">
                    <div class="section-title"><i class="fa-solid fa-headphones-simple"></i> Music Hub Player</div>
                    <span class="live-badge">Suno AI</span>
                </div>

                <!-- Track display -->
                <div style="display:flex;gap:20px;align-items:center;margin-top:8px">
                    <div class="album-art-wrap" id="album-art-wrap">
                        <div class="album-art-bg" id="album-art-bg"></div>
                        <img id="album-art-img" src="" alt="Album" style="display:none">
                        <div id="album-placeholder"><i class="fa-solid fa-music"></i></div>
                    </div>
                    <div class="player-track-info">
                        <h2 id="player-title">Loading...</h2>
                        <div id="player-artist">Suno AI</div>
                        <div id="player-tags"></div>
                    </div>
                </div>

                <!-- EQ -->
                <div class="eq-wrap" style="margin-top:16px">
                    <canvas id="eq-canvas"></canvas>
                </div>

                <!-- Progress -->
                <div class="progress-wrap" style="margin-top:14px">
                    <div class="progress-bar" id="progress-bar" onclick="seekAudio(event)">
                        <div id="progress-fill"></div>
                        <div id="progress-thumb"></div>
                    </div>
                    <div class="time-row">
                        <span id="current-time">0:00</span>
                        <span id="total-time">0:00</span>
                    </div>
                </div>

                <!-- Controls -->
                <div class="controls" style="margin-top:8px">
                    <button class="ctrl-btn" id="btn-shuffle" onclick="toggleShuffle()"><i class="fa-solid fa-shuffle"></i></button>
                    <button class="ctrl-btn" onclick="prevSong()"><i class="fa-solid fa-backward-step"></i></button>
                    <button class="ctrl-btn ctrl-play" onclick="togglePlay(event)"><i class="fa-solid fa-play" id="play-icon"></i></button>
                    <button class="ctrl-btn" onclick="nextSong()"><i class="fa-solid fa-forward-step"></i></button>
                    <button class="ctrl-btn" id="btn-repeat" onclick="toggleRepeat()"><i class="fa-solid fa-repeat"></i></button>
                </div>

                <!-- Volume -->
                <div class="vol-wrap" style="margin-top:8px">
                    <button class="vol-icon" onclick="toggleMute()"><i class="fa-solid fa-volume-high" id="vol-icon"></i></button>
                    <div class="vol-bar" id="vol-bar" onclick="setVolume(event)"><div id="vol-fill"></div></div>
                </div>

                <!-- Mode row -->
                <div class="mode-row" style="margin-top:10px">
                    <button class="mode-btn" onclick="document.getElementById('lyrics-section').scrollIntoView({behavior:'smooth'})">
                        <i class="fa-solid fa-align-left"></i> Lyrics
                    </button>
                    <button class="mode-btn neu-btn" onclick="refreshPlaylist()">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Lyrics Card -->
            <div class="neu-card animate-in" id="lyrics-section">
                <div class="section-head">
                    <div class="section-title"><i class="fa-solid fa-align-left"></i> Lyrics</div>
                </div>
                <div class="lyrics-panel" id="lyrics-panel">
                    <div class="lyric-empty"><i class="fa-regular fa-file-lines"></i><p>Lirik tidak tersedia</p></div>
                </div>
            </div>
        </div>

        <!-- ══ RIGHT: PLAYLIST ══ -->
        <div class="neu-card animate-in" style="align-self:start;position:sticky;top:90px">
            <div class="section-head">
                <div class="section-title"><i class="fa-solid fa-list-music"></i> Playlist</div>
                <span style="font-size:.75rem;color:var(--text-muted)" id="playlist-count">0</span>
            </div>
            <div style="position:relative;margin-bottom:10px">
                <i class="fa-solid fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.8rem"></i>
                <input type="text" class="playlist-search" placeholder="Search songs..." oninput="filterPlaylist(this.value)">
            </div>
            <div class="playlist-wrap" id="playlist-container">
                <div class="loading-state"><i class="fa-solid fa-circle-notch fa-spin"></i> Loading...</div>
            </div>
        </div>

    </div>
</div>

<!-- ── BOTTOM NAV ── -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="/" class="nav-item"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a href="https://n8n.robrion.my.id" target="_blank" class="nav-item"><i class="fa-solid fa-flask"></i><span>AI Lab</span></a>
        <a href="/music" class="nav-item active"><i class="fa-solid fa-headphones-simple"></i><span>Music</span></a>
        <a href="/movies" class="nav-item"><i class="fa-solid fa-clapperboard"></i><span>Cinema</span></a>
        <button class="nav-item" onclick="toggleTheme()"><i class="fa-solid fa-circle-half-stroke"></i><span>Theme</span></button>
    </div>
</nav>

<audio id="audio-player" preload="none"></audio>
<script src="/assets/js/music.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    initMusic();
    const audio = document.getElementById('audio-player');
    const wrap = document.getElementById('album-art-wrap');
    audio?.addEventListener('play', () => wrap?.classList.add('spinning'));
    audio?.addEventListener('pause', () => wrap?.classList.remove('spinning'));
    const img = document.getElementById('album-art-img');
    const ph = document.getElementById('album-placeholder');
    if (img) {
        img.addEventListener('load', () => { img.style.display = 'block'; if(ph) ph.style.display='none'; });
        img.addEventListener('error', () => { img.style.display = 'none'; if(ph) ph.style.display='flex'; });
    }
});
</script>
</body>
</html>
