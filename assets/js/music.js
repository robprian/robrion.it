// ============================================================
// music.js – Audio Player Logic (Modular)
// ============================================================
const audio        = document.getElementById('audio-player');
const playIcon     = document.getElementById('play-icon');
const playerTitle  = document.getElementById('player-title');
const playerArtist = document.getElementById('player-artist');
const progressFill  = document.getElementById('progress-fill');
const progressThumb = document.getElementById('progress-thumb');
const progressBar   = document.getElementById('progress-bar');
const currentTimeEl = document.getElementById('current-time');
const totalTimeEl   = document.getElementById('total-time');
const playlistCount = document.getElementById('playlist-count');
const playlistEl    = document.getElementById('playlist-container');
const vizContainer  = document.getElementById('visualizer');
const waveViz       = document.getElementById('waveform-viz');
const volFill       = document.getElementById('vol-fill');

let playlistData     = [];
let currentIndex     = 0;
let _isPlaying       = false;
let _shuffleOn       = false;
let _repeatOn        = false;
let _muted           = false;
let _vol             = 0.8;

window.isPlaying = false;

/* ---- Init ---- */
window.initMusic = function () {
    if (!audio) return;
    audio.volume = _vol;

    fetch('api/playlist.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                playlistData = res.data;
                if (playlistCount) playlistCount.innerText = playlistData.length;
                renderPlaylist();
                loadSong(0);
            } else {
                if (playlistEl) playlistEl.innerHTML =
                    `<p class="loading-state">Belum ada file MP3 di folder: ${res.path_scanned || 'music'}</p>`;
            }
        })
        .catch(() => {
            if (playlistEl) playlistEl.innerHTML =
                `<p class="loading-state" style="color:red">Gagal memuat playlist.</p>`;
        });
};

function renderPlaylist () {
    if (!playlistEl) return;
    playlistEl.innerHTML = '';
    playlistData.forEach((song, i) => {
        const el = document.createElement('div');
        el.className = 'playlist-item';
        el.id = `pitem-${i}`;
        el.onclick = () => loadAndPlay(i);
        el.innerHTML = `
            <div class="item-index">${i + 1}</div>
            <div class="item-thumb"></div>
            <div class="item-info">
                <div class="item-title">${song.title}</div>
                <div class="item-artist">${song.artist}</div>
            </div>
            <div class="item-duration">--:--</div>`;
        playlistEl.appendChild(el);
    });
}

window.loadSong = function (index) {
    if (!playlistData.length) return;
    currentIndex = index;
    const song = playlistData[index];
    if (playerTitle)  playerTitle.innerText  = song.title;
    if (playerArtist) playerArtist.innerText = song.artist;
    if (audio) audio.src = song.url;
    document.querySelectorAll('.playlist-item').forEach(el => el.classList.remove('active'));
    const act = document.getElementById(`pitem-${index}`);
    if (act) { act.classList.add('active'); act.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
};

window.loadAndPlay = function (index) { loadSong(index); playAudio(); };

window.togglePlay = function (e) {
    if (e) e.stopPropagation();
    if (!playlistData.length) return;
    _isPlaying ? pauseAudio() : playAudio();
};

window.playAudio = function () {
    if (!audio) return;
    audio.play().then(() => {
        _isPlaying = window.isPlaying = true;
        if (playIcon) { playIcon.classList.remove('fa-play'); playIcon.classList.add('fa-pause'); }
        if (vizContainer) vizContainer.classList.add('is-playing');
        if (waveViz) waveViz.classList.add('is-playing');
    }).catch(console.error);
};

window.pauseAudio = function () {
    if (!audio) return;
    audio.pause();
    _isPlaying = window.isPlaying = false;
    if (playIcon) { playIcon.classList.remove('fa-pause'); playIcon.classList.add('fa-play'); }
    if (vizContainer) vizContainer.classList.remove('is-playing');
    if (waveViz) waveViz.classList.remove('is-playing');
};

window.nextSong = function () {
    if (!playlistData.length) return;
    if (_shuffleOn) {
        let next;
        do { next = Math.floor(Math.random() * playlistData.length); } while (next === currentIndex);
        loadAndPlay(next);
    } else {
        loadAndPlay((currentIndex + 1) % playlistData.length);
    }
};

window.prevSong = function () {
    if (!playlistData.length) return;
    loadAndPlay((currentIndex - 1 + playlistData.length) % playlistData.length);
};

window.toggleShuffle = function () {
    _shuffleOn = !_shuffleOn;
    document.getElementById('btn-shuffle')?.classList.toggle('active-btn', _shuffleOn);
};

window.toggleRepeat = function () {
    _repeatOn = !_repeatOn;
    document.getElementById('btn-repeat')?.classList.toggle('active-btn', _repeatOn);
};

window.seekAudio = function (e) {
    if (!audio || !audio.duration) return;
    const pct = e.offsetX / progressBar.clientWidth;
    audio.currentTime = pct * audio.duration;
};

window.toggleMute = function () {
    if (!audio) return;
    _muted = !_muted;
    audio.muted = _muted;
    const icon = document.getElementById('vol-icon');
    if (icon) icon.className = _muted ? 'fa-solid fa-volume-xmark' : 'fa-solid fa-volume-high';
};

window.setVolume = function (e) {
    if (!audio) return;
    const pct = Math.max(0, Math.min(1, e.offsetX / document.getElementById('vol-bar').clientWidth));
    _vol = pct;
    audio.volume = pct;
    if (volFill) volFill.style.width = (pct * 100) + '%';
};

/* ---- Progress update ---- */
if (audio) {
    audio.addEventListener('timeupdate', () => {
        if (!audio.duration) return;
        const pct = (audio.currentTime / audio.duration) * 100;
        if (progressFill)  progressFill.style.width  = pct + '%';
        if (progressThumb) progressThumb.style.left  = pct + '%';
        if (currentTimeEl) currentTimeEl.innerText = fmt(audio.currentTime);
        if (totalTimeEl)   totalTimeEl.innerText   = fmt(audio.duration);

        // Update playlist item duration once
        const durEl = document.querySelector(`#pitem-${currentIndex} .item-duration`);
        if (durEl && durEl.innerText === '--:--') durEl.innerText = fmt(audio.duration);
    });
    audio.addEventListener('ended', () => {
        if (_repeatOn) { audio.currentTime = 0; playAudio(); }
        else nextSong();
    });
}

function fmt (s) {
    if (isNaN(s)) return '0:00';
    const m = Math.floor(s / 60), sec = Math.floor(s % 60);
    return `${m}:${sec < 10 ? '0' : ''}${sec}`;
}
