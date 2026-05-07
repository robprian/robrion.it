<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveForm Media – Music & Movies</title>
    <meta name="description" content="WaveForm Media – Pemutar musik dan drama online dengan desain neumorphism premium">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/player.css">
</head>
<body>
    <div class="app-container">

        <!-- ===== DESKTOP HEADER ===== -->
        <header class="header">
            <a href="/" class="logo">
                <i class="fa-solid fa-wave-square" style="color:var(--accent)"></i>
                WAVEFORM MEDIA
            </a>
            <nav class="nav-links">
                <a href="/" id="head-nav-home"><i class="fa-solid fa-house"></i> Home</a>
                <a href="#" class="active" id="head-nav-music" onclick="showView('music', event)"><i class="fa-solid fa-music"></i> Music</a>
                <a href="#" id="head-nav-movie" onclick="showView('movies', event)"><i class="fa-solid fa-film"></i> Movies</a>
            </nav>
            <div class="header-right">
                <div class="search-wrap">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input type="text" class="search-bar" id="search-input" placeholder="Search...">
                </div>
                <button class="profile-btn"><i class="fa-regular fa-user"></i></button>
            </div>
        </header>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main-content" id="main-grid">

            <!-- ====== MUSIC VIEW ====== -->
            <section class="player-section" id="view-player">
                <div class="mobile-top-bar">
                    <button class="icon-btn" onclick="window.location.href='/'">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <span class="top-bar-title">Now Playing</span>
                    <button class="icon-btn"><i class="fa-solid fa-bars"></i></button>
                </div>

                <!-- Album Art & Track Info -->
                <div class="track-info-large">
                    <div class="album-art" id="album-art-wrap"
                         onclick="if(document.getElementById('view-player').classList.contains('minimized')) showView('music', event)">
                        <div id="album-art-inner"></div>
                    </div>
                    <div class="track-details">
                        <h1 id="player-title" class="track-name">Pilih Lagu</h1>
                        <p id="player-artist" class="track-artist">Unknown Artist</p>
                        <div class="waveform-visualizer" id="waveform-viz">
                            <?php for($i=0;$i<32;$i++): ?>
                            <div class="wv-bar"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
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

                <!-- Main Controls -->
                <div class="controls">
                    <button class="btn-control" id="btn-shuffle" onclick="toggleShuffle()" title="Shuffle">
                        <i class="fa-solid fa-shuffle"></i>
                    </button>
                    <button class="btn-control" onclick="prevSong()" title="Previous">
                        <i class="fa-solid fa-backward-step"></i>
                    </button>
                    <button class="btn-control btn-play" id="btn-play" onclick="togglePlay(event)" title="Play/Pause">
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
                        <button class="icon-btn-sm"><i class="fa-solid fa-volume-xmark"></i></button>
                        <button class="icon-btn-sm"><i class="fa-solid fa-desktop"></i></button>
                    </div>
                </div>
            </section>

            <!-- PLAYLIST SECTION -->
            <aside class="playlist-section" id="view-playlist">
                <div class="playlist-header">
                    <span class="playlist-title">Current Playlist</span>
                    <span class="playlist-badge" id="playlist-count">0</span>
                </div>
                <div class="playlist-list" id="playlist-container">
                    <div class="loading-state">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Memuat playlist...</span>
                    </div>
                </div>
            </aside>

            <!-- ====== MOVIE VIEW ====== -->
            <section class="movie-section" id="view-movies" style="display:none;">
                <div class="movie-section-header">
                    <div>
                        <h2 class="section-title"><i class="fa-solid fa-film" style="color:var(--accent)"></i> Drama Box</h2>
                        <p class="section-subtitle">Trending dramas powered by DramaBox API</p>
                    </div>
                    <div class="movie-tabs">
                        <button class="movie-tab active" onclick="loadDramaCategory('foryou', this)">For You</button>
                        <button class="movie-tab" onclick="loadDramaCategory('trending', this)">Trending</button>
                        <button class="movie-tab" onclick="loadDramaCategory('latest', this)">Terbaru</button>
                        <button class="movie-tab" onclick="loadDramaCategory('dubindo', this)">Dub Indo</button>
                    </div>
                </div>
                <div class="movie-grid" id="movie-container">
                    <div class="loading-state" style="width:100%;text-align:center;padding:40px">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Memuat drama...</span>
                    </div>
                </div>
            </section>

        </main>

        <!-- ===== MOBILE BOTTOM NAV ===== -->
        <nav class="mobile-bottom-nav" id="mobile-nav">
            <button class="nav-btn" onclick="window.location.href='/'">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </button>
            <button class="nav-btn active" id="nav-music" onclick="showView('music', event)">
                <i class="fa-solid fa-music"></i>
                <span>Music</span>
            </button>
            <button class="nav-btn" id="nav-movie" onclick="showView('movies', event)">
                <i class="fa-solid fa-film"></i>
                <span>Movies</span>
            </button>
            <button class="nav-btn">
                <i class="fa-solid fa-layer-group"></i>
                <span>Library</span>
            </button>
        </nav>
    </div>

    <!-- ===== VIDEO/EPISODE MODAL ===== -->
    <div class="modal-overlay" id="drama-modal" style="display:none;" onclick="closeDramaModal(event)">
        <div class="drama-modal-content" id="drama-modal-content">
            <button class="modal-close" onclick="closeDramaModal()"><i class="fa-solid fa-xmark"></i></button>
            <div id="drama-modal-body">Loading...</div>
        </div>
    </div>

    <!-- AUDIO ELEMENT -->
    <audio id="audio-player" preload="none"></audio>

    <script src="/assets/js/music.js"></script>
    <script src="/assets/js/movies.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
