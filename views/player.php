<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveForm Media - Neumorphism App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/player.css">
</head>

<body>

    <div class="app-container">
        <!-- HEADER -->
        <header class="header">
            <div class="logo">WAVEFORM MEDIA</div>
            <div class="nav-links">
                <a href="#" class="active" id="head-nav-music" onclick="showMusic(event)">Music</a>
                <a href="#" id="head-nav-movie" onclick="showMovies(event)">Movies</a>
            </div>
            <div class="header-right">
                <input type="text" class="search-bar" placeholder="Search...">
                <button class="profile-btn"><i class="fa-regular fa-user"></i></button>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="main-content" id="main-grid">
            
            <!-- ====== MUSIC VIEW ====== -->
            
            <!-- PLAYER SECTION (Music) -->
            <section class="player-section" id="view-player">
                <div class="mobile-top-bar">
                    <i class="fa-solid fa-chevron-left" onclick="window.location.href='/'"></i>
                    <span>Now Playing</span>
                    <i class="fa-solid fa-bars"></i>
                </div>

                <div class="track-info-large">
                    <div class="album-art" id="album-art-wrap" onclick="if(document.getElementById('view-player').classList.contains('minimized')) showMusic(event)">
                        <div></div>
                    </div>
                    <div class="track-details">
                        <h1 id="player-title">Pilih Lagu</h1>
                        <p id="player-artist">Unknown Artist</p>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-bar-bg" id="progress-bar" onclick="seekAudio(event)">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div class="time-info">
                        <span id="current-time">0:00</span>
                        <span id="total-time">0:00</span>
                    </div>
                </div>

                <div class="controls">
                    <button class="btn-control"><i class="fa-solid fa-shuffle"></i></button>
                    <button class="btn-control" onclick="prevSong()"><i class="fa-solid fa-backward-step"></i></button>
                    <button class="btn-control btn-play" onclick="togglePlay(event)"><i class="fa-solid fa-play" id="play-icon"></i></button>
                    <button class="btn-control" onclick="nextSong()"><i class="fa-solid fa-forward-step"></i></button>
                    <button class="btn-control"><i class="fa-solid fa-repeat"></i></button>
                </div>

                <div class="secondary-controls">
                    <div class="vol-control">
                        <i class="fa-solid fa-volume-high"></i>
                        <div class="progress-bar-bg" style="width: 100%; height: 6px;">
                            <div class="progress-fill" style="width: 70%; box-shadow: none;"></div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 0.9rem; margin-right: 10px;">Visual <i class="fa-solid fa-sliders"></i></span>
                        <div class="visualizer-container" id="visualizer">
                            <div class="visualizer-bar"></div>
                            <div class="visualizer-bar"></div>
                            <div class="visualizer-bar"></div>
                            <div class="visualizer-bar"></div>
                        </div>
                        <span style="font-size: 0.9rem; margin-left: 15px;">Playback <i class="fa-solid fa-toggle-on" style="color: var(--accent-color)"></i></span>
                    </div>
                </div>
            </section>

            <!-- PLAYLIST SECTION -->
            <aside class="playlist-section" id="view-playlist">
                <div class="playlist-header">
                    <span>Current Playlist</span>
                    <span id="playlist-count">0</span>
                </div>
                <div class="playlist-list" id="playlist-container">
                    <p style="text-align: center; color: var(--text-light); margin-top: 20px;">Memuat playlist...</p>
                </div>
            </aside>

            <!-- ====== MOVIE VIEW ====== -->
            <section class="movie-section" id="view-movies" style="display: none;">
                <div class="playlist-header" style="margin-bottom: 20px;">
                    <span style="font-size: 1.5rem;">🎬 Movie Library</span>
                </div>
                <div class="movie-grid" id="movie-container">
                    <p style="text-align: center; color: var(--text-light); width: 100%;">Memuat film...</p>
                </div>
            </section>
        </main>

        <!-- MOBILE BOTTOM NAV -->
        <nav class="mobile-bottom-nav">
            <i class="fa-solid fa-house" onclick="window.location.href='/'"></i>
            <i class="fa-solid fa-music active" id="nav-music" onclick="showMusic(event)"></i>
            <i class="fa-solid fa-film" id="nav-movie" onclick="showMovies(event)"></i>
            <i class="fa-solid fa-layer-group"></i>
        </nav>
    </div>

    <!-- VIDEO MODAL -->
    <div class="video-modal" id="video-modal" style="display: none;">
        <div class="video-content">
            <button class="close-video" onclick="closeVideo()"><i class="fa-solid fa-xmark"></i></button>
            <video id="movie-player" controls preload="none"></video>
            <h3 id="movie-title-display" style="margin-top: 15px; color: white;"></h3>
        </div>
    </div>

    <!-- AUDIO ELEMENT -->
    <audio id="audio-player"></audio>

    <script src="assets/js/music.js"></script>
    <script src="assets/js/movies.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
