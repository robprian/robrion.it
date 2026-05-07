document.addEventListener('DOMContentLoaded', () => {
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

    // Fetch playlist dari API Backend secara asynchronous (CI/CD / SPA approach)
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
            playlistContainer.innerHTML = `<p style="text-align: center; color: red; margin-top: 20px;">Gagal memuat playlist. Cek koneksi API.</p>`;
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

        // Update UI Playlist Active State
        document.querySelectorAll('.playlist-item').forEach(item => item.classList.remove('active'));
        const activeItem = document.getElementById(`item-${index}`);
        if (activeItem) activeItem.classList.add('active');
    };

    window.loadAndPlay = function(index) {
        loadSong(index);
        playAudio();
    };

    window.togglePlay = function() {
        if (playlistData.length === 0) return;
        if (isPlaying) {
            pauseAudio();
        } else {
            playAudio();
        }
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
});
