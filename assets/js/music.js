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

window.initMusic = function() {
    fetch('api/playlist.php')
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                playlistData = res.data;
                if(playlistCount) playlistCount.innerText = playlistData.length;
                renderPlaylist();
                loadSong(0);
            } else {
                if(playlistContainer) playlistContainer.innerHTML = `<p style="text-align: center; color: var(--text-light); margin-top: 20px;">Belum ada file .mp3 di folder: ${res.path_scanned || 'music'}</p>`;
            }
        })
        .catch(err => {
            console.error('Error fetching playlist:', err);
            if(playlistContainer) playlistContainer.innerHTML = `<p style="text-align: center; color: red; margin-top: 20px;">Gagal memuat playlist.</p>`;
        });
};

function renderPlaylist() {
    if(!playlistContainer) return;
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

    if(playerTitle) playerTitle.innerText = song.title;
    if(playerArtist) playerArtist.innerText = song.artist;
    if(audio) audio.src = song.url;

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

window.playAudio = function() {
    if(!audio) return;
    audio.play().then(() => {
        isPlaying = true;
        if(playIcon) playIcon.classList.replace('fa-play', 'fa-pause');
        if(visualizerContainer) visualizerContainer.classList.add('is-playing');
    }).catch(err => console.error("Error playing audio:", err));
}

window.pauseAudio = function() {
    if(!audio) return;
    audio.pause();
    isPlaying = false;
    if(playIcon) playIcon.classList.replace('fa-pause', 'fa-play');
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

if(audio) {
    audio.addEventListener('timeupdate', () => {
        if (audio.duration) {
            const progressPercent = (audio.currentTime / audio.duration) * 100;
            if(progressFill) progressFill.style.width = `${progressPercent}%`;

            if(currentTimeEl) currentTimeEl.innerText = formatTime(audio.currentTime);
            if(totalTimeEl) totalTimeEl.innerText = formatTime(audio.duration);
        }
    });

    audio.addEventListener('ended', nextSong);
}

window.seekAudio = function(e) {
    if (!audio || !audio.duration) return;
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
