<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveForm Music - Neumorphism Player</title>
    <!-- Modular: CSS dipisahkan ke file tersendiri -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/player.css">
</head>

<body>

    <div class="app-container">
        <!-- HEADER -->
        <header class="header">
            <div class="logo">WAVEFORM MUSIC</div>
            <div class="nav-links">
                <a href="#" class="active">Home</a>
                <a href="#">Library</a>
            </div>
            <div class="header-right">
                <input type="text" class="search-bar" placeholder="Search...">
                <button class="profile-btn"><i class="fa-regular fa-user"></i></button>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- PLAYER SECTION -->
            <section class="player-section">
                <div class="mobile-top-bar">
                    <i class="fa-solid fa-chevron-left"></i>
                    <span>Now Playing</span>
                    <i class="fa-solid fa-bars"></i>
                </div>

                <div class="track-info-large">
                    <div class="album-art">
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
                    <button class="btn-control btn-play" onclick="togglePlay()"><i class="fa-solid fa-play" id="play-icon"></i></button>
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
                        <!-- Visualizer animation dinamis (Responsive di Mobile) -->
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
            <aside class="playlist-section">
                <div class="playlist-header">
                    <span>Current Playlist</span>
                    <span id="playlist-count">0</span>
                </div>
                <!-- Tempat inject playlist via Javascript (SPA Approach) -->
                <div class="playlist-list" id="playlist-container">
                    <p style="text-align: center; color: var(--text-light); margin-top: 20px;">Memuat playlist dari server...</p>
                </div>
            </aside>
        </main>

        <!-- MOBILE BOTTOM NAV -->
        <nav class="mobile-bottom-nav">
            <i class="fa-solid fa-house active"></i>
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-solid fa-list-ul"></i>
            <i class="fa-solid fa-layer-group"></i>
        </nav>
    </div>

    <!-- AUDIO ELEMENT -->
    <audio id="audio-player"></audio>

    <!-- Modular: Script JavaScript terpisah -->
    <script src="assets/js/player.js"></script>
</body>

</html>