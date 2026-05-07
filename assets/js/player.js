document.addEventListener('DOMContentLoaded', () => {
    // --- AUDIO PLAYER LOGIC ---
    const audio = document.getElementById('audio-player');
    const playIcon = document.getElementById('play-icon');
    const playerTitle = document.getElementById('player-title');
    const playerArtist = document.getElementById('player-artist');
    const progressFill = document.getElementById('progress-fill');
    const progressBar = document.getElementById('progress-bar');
    const currentTimeEl = document.getElementById('current-time');
    const totalTimeEl = document.getElementById('total-time');
    const playlistCount = document.getElementById('playlist-count');
    const playlistContainer = document.getElementById('playlist-container');
    const visualizerContainer = document.getElementById('visualizer');

    let playlistData = [];
    let currentSongIndex = 0;
    let isPlaying = false;

    fetch('api/playlist.php')
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                playlistData = res.data;
                playlistCount.innerText = playlistData.length;
                renderPlaylist();
                loadSong(0);
            } else {
                playlistContainer.innerHTML = `<p style="text-align: center; color: var(--text-light); margin-top: 20px;">Belum ada file .mp3 di folder: ${res.path_scanned || 'music'}</p>`;
            }
        })
        .catch(err => {
            console.error('Error fetching playlist:', err);
            playlistContainer.innerHTML = `<p style="text-align: center; color: red; margin-top: 20px;">Gagal memuat playlist.</p>`;
        });

    function renderPlaylist() {
        playlistContainer.innerHTML = '';
        playlistData.forEach((song, index) => {
            const item = document.createElement('div');
            item.className = 'playlist-item';
            item.id = `item-${index}`;
            item.onclick = () => loadAndPlay(index);
            
            item.innerHTML = `
                <div class="item-index">${index + 1}</div>
                <div class="item-thumb"></div>
                <div class="item-info">
                    <div class="item-title">${song.title}</div>
                    <div class="item-artist">${song.artist}</div>
                </div>
                <div class="item-duration">--:--</div>
            `;
            playlistContainer.appendChild(item);
        });
    }

    window.loadSong = function(index) {
        currentSongIndex = index;
        const song = playlistData[index];

        playerTitle.innerText = song.title;
        playerArtist.innerText = song.artist;
        audio.src = song.url;

        document.querySelectorAll('.playlist-item').forEach(item => item.classList.remove('active'));
        const activeItem = document.getElementById(`item-${index}`);
        if (activeItem) activeItem.classList.add('active');
    };

    window.loadAndPlay = function(index) {
        loadSong(index);
        playAudio();
    };

    window.togglePlay = function(e) {
        if(e) e.stopPropagation();
        if (playlistData.length === 0) return;
        if (isPlaying) pauseAudio();
        else playAudio();
    };

    function playAudio() {
        audio.play().then(() => {
            isPlaying = true;
            playIcon.classList.replace('fa-play', 'fa-pause');
            if(visualizerContainer) visualizerContainer.classList.add('is-playing');
        }).catch(err => console.error("Error playing audio:", err));
    }

    function pauseAudio() {
        audio.pause();
        isPlaying = false;
        playIcon.classList.replace('fa-pause', 'fa-play');
        if(visualizerContainer) visualizerContainer.classList.remove('is-playing');
    }

    window.nextSong = function() {
        if (playlistData.length === 0) return;
        currentSongIndex = (currentSongIndex + 1) % playlistData.length;
        loadAndPlay(currentSongIndex);
    };

    window.prevSong = function() {
        if (playlistData.length === 0) return;
        currentSongIndex = (currentSongIndex - 1 + playlistData.length) % playlistData.length;
        loadAndPlay(currentSongIndex);
    };

    audio.addEventListener('timeupdate', () => {
        if (audio.duration) {
            const progressPercent = (audio.currentTime / audio.duration) * 100;
            progressFill.style.width = `${progressPercent}%`;

            currentTimeEl.innerText = formatTime(audio.currentTime);
            totalTimeEl.innerText = formatTime(audio.duration);
        }
    });

    audio.addEventListener('ended', nextSong);

    window.seekAudio = function(e) {
        if (!audio.duration) return;
        const width = progressBar.clientWidth;
        const clickX = e.offsetX;
        const duration = audio.duration;
        audio.currentTime = (clickX / width) * duration;
    };

    function formatTime(seconds) {
        if (isNaN(seconds)) return "0:00";
        let min = Math.floor(seconds / 60);
        let sec = Math.floor(seconds % 60);
        if (sec < 10) sec = `0${sec}`;
        return `${min}:${sec}`;
    }

    // --- MOVIE API LOGIC ---
    let moviesLoaded = false;
    const movieContainer = document.getElementById('movie-container');

    window.loadMovies = function() {
        if(moviesLoaded) return;
        movieContainer.innerHTML = '<p style="text-align: center; color: var(--text-light); width: 100%;"><i class="fa-solid fa-circle-notch fa-spin"></i> Memuat data dari API...</p>';
        
        fetch('https://api.sansekai.my.id/api/dramabox/foryou')
            .then(res => res.json())
            .then(data => {
                if(data && data.length > 0) {
                    movieContainer.innerHTML = '';
                    data.forEach((movie) => {
                        const card = document.createElement('div');
                        card.className = 'movie-card';
                        card.onclick = () => showMovieDetail(movie);
                        
                        card.innerHTML = `
                            <div class="thumb" style="background: url('${movie.coverWap}') center/cover; position: relative;">
                                <div style="position: absolute; bottom: 5px; right: 5px; background: rgba(0,0,0,0.7); padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; color: white;">
                                    ${movie.chapterCount} Eps
                                </div>
                            </div>
                            <h3 title="${movie.bookName}">${movie.bookName}</h3>
                            <div style="font-size: 0.7rem; color: var(--text-light); margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${movie.tags ? movie.tags.slice(0, 2).join(' • ') : ''}
                            </div>
                        `;
                        movieContainer.appendChild(card);
                    });
                    moviesLoaded = true;
                } else {
                    movieContainer.innerHTML = `<p style="text-align: center; color: var(--text-light); width: 100%;">Tidak ada data film.</p>`;
                }
            })
            .catch(err => {
                console.error("API Error:", err);
                movieContainer.innerHTML = `<p style="text-align: center; color: red; width: 100%;">Gagal memuat film dari server API.</p>`;
            });
    }

    window.showMovieDetail = function(movie) {
        // Karena endpoint video langsung belum tersedia (butuh fetch allepisode & decrypt url), 
        // kita tampilkan notifikasi sementara atau integrasikan fetch selanjutnya.
        alert("Judul: " + movie.bookName + "\n\nSistem sedang dikembangkan untuk memutar video melalui API:\n- /dramabox/allepisode\n- /dramabox/decrypt\n\nBookID: " + movie.bookId);
    }

    // --- SPA NAVIGATION LOGIC ---
    window.showMusic = function(e) {
        if(e) e.preventDefault();
        document.getElementById('view-playlist').style.display = 'flex';
        document.getElementById('view-movies').style.display = 'none';
        
        document.getElementById('main-grid').style.gridTemplateColumns = '1fr 400px';
        if(window.innerWidth <= 900) {
            document.getElementById('main-grid').style.gridTemplateColumns = '1fr';
        }

        document.getElementById('view-player').classList.remove('minimized');

        document.getElementById('head-nav-music').classList.add('active');
        document.getElementById('head-nav-movie').classList.remove('active');
        document.getElementById('nav-music').classList.add('active');
        document.getElementById('nav-movie').classList.remove('active');
    };

    window.showMovies = function(e) {
        if(e) e.preventDefault();
        document.getElementById('view-playlist').style.display = 'none';
        document.getElementById('view-movies').style.display = 'block';
        
        document.getElementById('main-grid').style.gridTemplateColumns = '1fr';

        // Minimize Audio Player
        document.getElementById('view-player').classList.add('minimized');

        document.getElementById('head-nav-music').classList.remove('active');
        document.getElementById('head-nav-movie').classList.add('active');
        document.getElementById('nav-music').classList.remove('active');
        document.getElementById('nav-movie').classList.add('active');

        loadMovies();
    };

    // --- VIDEO MODAL LOGIC ---
    const videoModal = document.getElementById('video-modal');
    const moviePlayer = document.getElementById('movie-player');
    const movieTitleDisplay = document.getElementById('movie-title-display');

    window.openVideo = function(url, title) {
        // Pause audio if playing
        if(isPlaying) pauseAudio();

        moviePlayer.src = url;
        movieTitleDisplay.innerText = title;
        videoModal.style.display = 'flex';
        moviePlayer.play();
    };

    window.closeVideo = function() {
        moviePlayer.pause();
        moviePlayer.src = '';
        videoModal.style.display = 'none';
    };
});
