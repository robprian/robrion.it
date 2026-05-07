// ============================================================
// music.js – Suno AI Music Player
// Features: API fetch + localStorage cache, album art,
//           synced lyrics, Web Audio API equalizer, persistence
// ============================================================

const SUNO_API   = 'https://inisunoapi.vercel.app/api/get';
const CACHE_KEY  = 'waveform_playlist_cache';
const STATE_KEY  = 'waveform_state';
const CACHE_TTL  = 30 * 60 * 1000; // 30 minutes

const audio         = document.getElementById('audio-player');
const playIcon      = document.getElementById('play-icon');
const playerTitle   = document.getElementById('player-title');
const playerArtist  = document.getElementById('player-artist');
const playerTags    = document.getElementById('player-tags');
const progressFill  = document.getElementById('progress-fill');
const progressThumb = document.getElementById('progress-thumb');
const progressBar   = document.getElementById('progress-bar');
const currentTimeEl = document.getElementById('current-time');
const totalTimeEl   = document.getElementById('total-time');
const playlistCount = document.getElementById('playlist-count');
const playlistEl    = document.getElementById('playlist-container');
const albumArtEl    = document.getElementById('album-art-img');
const albumArtBg    = document.getElementById('album-art-bg');
const lyricsEl      = document.getElementById('lyrics-panel');
const eqCanvas      = document.getElementById('eq-canvas');
const volFill       = document.getElementById('vol-fill');

let playlistData = [];
let currentIndex = 0;
let _playing     = false;
let _shuffleOn   = false;
let _repeatOn    = false;
let _muted       = false;
let _vol         = 0.8;
let _lyricLines  = [];
let _lyricTimer  = null;
let _lyricIdx    = 0;

// Web Audio API
let _audioCtx    = null;
let _analyser    = null;
let _source      = null;
let _rafId       = null;

window.isPlaying = false;

// ══════════════════════════════════════════════════
//  INIT & API FETCH
// ══════════════════════════════════════════════════

window.initMusic = function() {
    if (!audio) return;
    audio.volume = _vol;
    audio.crossOrigin = 'anonymous';
    bindAudioEvents();
    loadPlaylistFromCacheOrAPI();
};

async function loadPlaylistFromCacheOrAPI() {
    showPlaylistLoading('Memuat playlist dari Suno AI...');

    // 1) Try cache
    const cached = getCache();
    if (cached) {
        playlistData = cached;
        onPlaylistLoaded();
        // Refresh in background silently
        fetchAllPages().then(fresh => {
            if (fresh.length > 0) { mergeAndSave(fresh); }
        }).catch(() => {});
        return;
    }

    // 2) Fetch fresh
    try {
        const songs = await fetchAllPages();
        if (songs.length > 0) {
            mergeAndSave(songs);
            onPlaylistLoaded();
        } else {
            showPlaylistLoading('Tidak ada lagu ditemukan.', true);
        }
    } catch(e) {
        showPlaylistLoading('Gagal memuat dari API. Coba refresh.', true);
    }
}

async function fetchAllPages() {
    const songs = [];
    let page = 1;
    let hasMore = true;

    showPlaylistLoading(`Mengambil halaman 1...`);

    while (hasMore && page <= 10) { // max 10 pages safety
        try {
            const res = await fetch(`${SUNO_API}?page=${page}`);
            const data = await res.json();
            const items = Array.isArray(data) ? data : (data.data || data.items || data.songs || []);

            if (!items.length) { hasMore = false; break; }

            items.forEach(item => {
                if (item.audio_url && item.status === 'complete') {
                    songs.push(normalizeSong(item));
                }
            });

            if (items.length < 20) { hasMore = false; }
            else { page++; showPlaylistLoading(`Mengambil halaman ${page}...`); }
        } catch(e) {
            hasMore = false;
        }
    }

    return songs;
}

function normalizeSong(item) {
    return {
        id:       item.id,
        title:    item.title || 'Untitled',
        artist:   item.tags || item.model_name || 'Suno AI',
        url:      item.audio_url,
        cover:    item.image_url || '',
        lyric:    item.lyric || '',
        tags:     item.tags || '',
        duration: item.duration || 0,
        created:  item.created_at || '',
        model:    item.model_name || '',
    };
}

function mergeAndSave(fresh) {
    // Merge: keep all unique by ID, fresh data wins
    const map = {};
    playlistData.forEach(s => { map[s.id] = s; });
    fresh.forEach(s => { map[s.id] = s; });
    playlistData = Object.values(map);
    setCache(playlistData);
    renderPlaylist();
    if (playlistCount) playlistCount.innerText = playlistData.length;
}

// ── Cache helpers ──
function getCache() {
    try {
        const raw = localStorage.getItem(CACHE_KEY);
        if (!raw) return null;
        const { ts, data } = JSON.parse(raw);
        if (Date.now() - ts > CACHE_TTL) return null;
        return data;
    } catch { return null; }
}

function setCache(data) {
    try {
        localStorage.setItem(CACHE_KEY, JSON.stringify({ ts: Date.now(), data }));
    } catch(e) {
        // Storage full – trim to 100 songs
        try {
            localStorage.setItem(CACHE_KEY, JSON.stringify({ ts: Date.now(), data: data.slice(0, 100) }));
        } catch {}
    }
}

// ══════════════════════════════════════════════════
//  PLAYLIST RENDER
// ══════════════════════════════════════════════════

function onPlaylistLoaded() {
    if (playlistCount) playlistCount.innerText = playlistData.length;
    renderPlaylist();

    // Restore saved state
    const saved = JSON.parse(localStorage.getItem(STATE_KEY) || 'null');
    let startIdx = 0;
    if (saved) {
        if (saved.volume !== undefined) {
            _vol = saved.volume; audio.volume = _vol;
            if (volFill) volFill.style.width = (_vol * 100) + '%';
        }
        // Find saved song by ID (more reliable than index)
        if (saved.songId) {
            const found = playlistData.findIndex(s => s.id === saved.songId);
            if (found >= 0) startIdx = found;
        } else if (saved.index !== undefined) {
            startIdx = Math.min(saved.index, playlistData.length - 1);
        }
    }

    loadSong(startIdx);
    if (saved?.currentTime) {
        audio.addEventListener('loadedmetadata', () => {
            audio.currentTime = saved.currentTime;
        }, { once: true });
    }
}

function showPlaylistLoading(msg, isError = false) {
    if (!playlistEl) return;
    playlistEl.innerHTML = `<div class="loading-state" style="color:${isError?'#f87171':''}">
        ${isError ? '<i class="fa-solid fa-triangle-exclamation"></i>' : '<i class="fa-solid fa-circle-notch fa-spin"></i>'}
        <span>${msg}</span>
    </div>`;
}

function renderPlaylist(filter = '') {
    if (!playlistEl) return;
    playlistEl.innerHTML = '';
    const filtered = filter
        ? playlistData.filter(s => s.title.toLowerCase().includes(filter) || s.tags.toLowerCase().includes(filter))
        : playlistData;

    filtered.forEach((song, i) => {
        const realIdx = playlistData.indexOf(song);
        const el = document.createElement('div');
        el.className = 'playlist-item' + (realIdx === currentIndex ? ' active' : '');
        el.id = `pitem-${realIdx}`;
        el.onclick = () => loadAndPlay(realIdx);
        const dur = song.duration ? fmt(song.duration) : '--:--';
        el.innerHTML = `
            <div class="item-index">${realIdx + 1}</div>
            <div class="item-thumb" style="${song.cover ? `background-image:url('${song.cover}')` : ''}">
                ${!song.cover ? '<i class="fa-solid fa-music"></i>' : ''}
            </div>
            <div class="item-info">
                <div class="item-title">${song.title}</div>
                <div class="item-artist">${song.tags || song.artist || 'Suno AI'}</div>
            </div>
            <div class="item-duration">${dur}</div>`;
        playlistEl.appendChild(el);
    });
}

// ══════════════════════════════════════════════════
//  PLAYBACK
// ══════════════════════════════════════════════════

window.loadSong = function(index) {
    if (!playlistData.length) return;
    currentIndex = index;
    const song = playlistData[index];

    if (playerTitle)  playerTitle.innerText  = song.title;
    if (playerArtist) playerArtist.innerText = song.model || 'Suno AI';
    if (playerTags) {
        playerTags.innerHTML = (song.tags || '').split(' ').filter(Boolean)
            .map(t => `<span class="music-tag">${t}</span>`).join('');
    }

    // Album art
    if (albumArtEl) {
        albumArtEl.src = song.cover || '';
        albumArtEl.style.display = song.cover ? 'block' : 'none';
    }
    // Blurred background
    if (albumArtBg && song.cover) {
        albumArtBg.style.backgroundImage = `url('${song.cover}')`;
    }

    // Lyrics
    parseLyrics(song.lyric || '');
    renderLyrics();

    if (audio) audio.src = song.url;

    // Playlist highlight
    document.querySelectorAll('.playlist-item').forEach(el => el.classList.remove('active'));
    const act = document.getElementById(`pitem-${index}`);
    if (act) { act.classList.add('active'); act.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }

    saveState();
};

window.loadAndPlay = function(index) { loadSong(index); playAudio(); };

window.togglePlay = function(e) {
    if (e) e.stopPropagation();
    if (!playlistData.length) return;
    _playing ? pauseAudio() : playAudio();
};

window.playAudio = function() {
    if (!audio) return;
    initAudioContext();
    audio.play().then(() => {
        _playing = window.isPlaying = true;
        if (playIcon) playIcon.className = 'fa-solid fa-pause';
        startEQ();
        startLyricSync();
        saveState();
    }).catch(console.error);
};

window.pauseAudio = function() {
    if (!audio) return;
    audio.pause();
    _playing = window.isPlaying = false;
    if (playIcon) playIcon.className = 'fa-solid fa-play';
    stopEQ();
    stopLyricSync();
    saveState();
};

window.nextSong = function() {
    if (!playlistData.length) return;
    if (_shuffleOn) {
        let n; do { n = Math.floor(Math.random() * playlistData.length); } while (n === currentIndex);
        loadAndPlay(n);
    } else {
        loadAndPlay((currentIndex + 1) % playlistData.length);
    }
};

window.prevSong = function() {
    if (!playlistData.length) return;
    if (audio && audio.currentTime > 3) { audio.currentTime = 0; return; }
    loadAndPlay((currentIndex - 1 + playlistData.length) % playlistData.length);
};

window.toggleShuffle = function() {
    _shuffleOn = !_shuffleOn;
    document.getElementById('btn-shuffle')?.classList.toggle('active-btn', _shuffleOn);
};

window.toggleRepeat = function() {
    _repeatOn = !_repeatOn;
    document.getElementById('btn-repeat')?.classList.toggle('active-btn', _repeatOn);
};

window.seekAudio = function(e) {
    if (!audio || !audio.duration) return;
    const rect = progressBar.getBoundingClientRect();
    const x = e.clientX - rect.left;
    audio.currentTime = (x / rect.width) * audio.duration;
};

window.toggleMute = function() {
    if (!audio) return;
    _muted = !_muted; audio.muted = _muted;
    const ic = document.getElementById('vol-icon');
    if (ic) ic.className = _muted ? 'fa-solid fa-volume-xmark' : 'fa-solid fa-volume-high';
};

window.setVolume = function(e) {
    if (!audio) return;
    const bar = document.getElementById('vol-bar');
    _vol = Math.max(0, Math.min(1, e.offsetX / bar.clientWidth));
    audio.volume = _vol;
    if (volFill) volFill.style.width = (_vol * 100) + '%';
    saveState();
};

// Force refresh from API
window.refreshPlaylist = function() {
    localStorage.removeItem(CACHE_KEY);
    playlistData = [];
    loadPlaylistFromCacheOrAPI();
};

// Search
window.filterPlaylist = function(q) {
    renderPlaylist(q.toLowerCase());
};

// ══════════════════════════════════════════════════
//  LYRICS ENGINE
// ══════════════════════════════════════════════════

function parseLyrics(raw) {
    _lyricLines = [];
    _lyricIdx   = 0;
    if (!raw) return;

    // Estimate timing: total duration / number of lines
    const lines = raw.split('\n')
        .map(l => l.trim())
        .filter(l => l.length > 0);

    const song = playlistData[currentIndex];
    const totalDur = song?.duration || 180;
    const interval = totalDur / Math.max(lines.length, 1);

    lines.forEach((line, i) => {
        _lyricLines.push({ time: i * interval, text: line });
    });
}

function renderLyrics() {
    if (!lyricsEl) return;
    if (!_lyricLines.length) {
        lyricsEl.innerHTML = '<div class="lyric-empty"><i class="fa-regular fa-file-lines"></i><p>Lirik tidak tersedia</p></div>';
        return;
    }
    lyricsEl.innerHTML = _lyricLines.map((l, i) =>
        `<div class="lyric-line${i === 0 ? ' lyric-active' : ''}" data-li="${i}">${l.text}</div>`
    ).join('');
}

function startLyricSync() {
    stopLyricSync();
    _lyricTimer = setInterval(() => {
        if (!audio || !_lyricLines.length) return;
        const t = audio.currentTime;
        let idx = 0;
        for (let i = 0; i < _lyricLines.length; i++) {
            if (_lyricLines[i].time <= t) idx = i;
        }
        if (idx !== _lyricIdx) {
            _lyricIdx = idx;
            highlightLyric(idx);
        }
    }, 500);
}

function stopLyricSync() {
    if (_lyricTimer) { clearInterval(_lyricTimer); _lyricTimer = null; }
}

function highlightLyric(idx) {
    if (!lyricsEl) return;
    lyricsEl.querySelectorAll('.lyric-line').forEach(el => el.classList.remove('lyric-active', 'lyric-past'));
    const lines = lyricsEl.querySelectorAll('.lyric-line');
    lines.forEach((el, i) => {
        if (i < idx) el.classList.add('lyric-past');
        if (i === idx) {
            el.classList.add('lyric-active');
            el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }
    });
}

// ══════════════════════════════════════════════════
//  WEB AUDIO API EQUALIZER VISUALIZER
// ══════════════════════════════════════════════════

function initAudioContext() {
    if (_audioCtx) return;
    try {
        _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        _analyser = _audioCtx.createAnalyser();
        _analyser.fftSize = 256;
        _analyser.smoothingTimeConstant = 0.8;
        _source = _audioCtx.createMediaElementSource(audio);
        _source.connect(_analyser);
        _analyser.connect(_audioCtx.destination);
    } catch(e) { _audioCtx = null; }
}

function startEQ() {
    stopEQ();
    if (!eqCanvas || !_analyser) { startFakeEQ(); return; }
    const ctx = eqCanvas.getContext('2d');
    const bufLen = _analyser.frequencyBinCount;
    const dataArr = new Uint8Array(bufLen);

    function draw() {
        _rafId = requestAnimationFrame(draw);
        _analyser.getByteFrequencyData(dataArr);

        const W = eqCanvas.width = eqCanvas.offsetWidth * window.devicePixelRatio;
        const H = eqCanvas.height = eqCanvas.offsetHeight * window.devicePixelRatio;
        ctx.clearRect(0, 0, W, H);

        const bars = 64;
        const barW = (W / bars) - 2;
        const step = Math.floor(bufLen / bars);

        for (let i = 0; i < bars; i++) {
            let sum = 0;
            for (let j = 0; j < step; j++) sum += dataArr[i * step + j];
            const avg = sum / step;
            const barH = (avg / 255) * H * 0.9;
            const x = i * (barW + 2);

            // Gradient: teal → purple
            const hue = 180 + (i / bars) * 100;
            const alpha = 0.6 + (avg / 255) * 0.4;
            ctx.fillStyle = `hsla(${hue}, 80%, 60%, ${alpha})`;

            // Rounded bar
            ctx.beginPath();
            ctx.roundRect(x, H - barH, barW, barH, 4);
            ctx.fill();

            // Peak dot
            if (barH > 4) {
                ctx.fillStyle = `hsla(${hue}, 100%, 80%, 0.9)`;
                ctx.beginPath();
                ctx.arc(x + barW / 2, H - barH - 2, 2, 0, Math.PI * 2);
                ctx.fill();
            }
        }
    }
    draw();
}

// Fake animated EQ when no AudioContext
function startFakeEQ() {
    if (!eqCanvas) return;
    const ctx = eqCanvas.getContext('2d');
    let frame = 0;

    function draw() {
        if (!_playing) return;
        _rafId = requestAnimationFrame(draw);
        frame++;
        const W = eqCanvas.width = eqCanvas.offsetWidth * window.devicePixelRatio;
        const H = eqCanvas.height = eqCanvas.offsetHeight * window.devicePixelRatio;
        ctx.clearRect(0, 0, W, H);
        const bars = 48;
        const barW = (W / bars) - 2;

        for (let i = 0; i < bars; i++) {
            const noise = Math.sin(frame * 0.05 + i * 0.6) * 0.35 +
                          Math.sin(frame * 0.09 + i * 0.4) * 0.25 +
                          Math.random() * 0.15;
            const barH = Math.max(4, (0.3 + noise) * H * 0.85);
            const x = i * (barW + 2);
            const hue = 180 + (i / bars) * 100;
            ctx.fillStyle = `hsla(${hue}, 80%, 60%, 0.75)`;
            ctx.beginPath();
            ctx.roundRect(x, H - barH, barW, barH, 3);
            ctx.fill();
        }
    }
    draw();
}

function stopEQ() {
    if (_rafId) { cancelAnimationFrame(_rafId); _rafId = null; }
    if (eqCanvas) {
        const ctx = eqCanvas.getContext('2d');
        ctx.clearRect(0, 0, eqCanvas.width, eqCanvas.height);
    }
}

// ══════════════════════════════════════════════════
//  STATE & EVENTS
// ══════════════════════════════════════════════════

function saveState() {
    if (!playlistData.length) return;
    const song = playlistData[currentIndex];
    localStorage.setItem(STATE_KEY, JSON.stringify({
        songId:      song?.id,
        index:       currentIndex,
        currentTime: audio ? audio.currentTime : 0,
        volume:      _vol,
        playing:     _playing,
        playlist:    playlistData.slice(0, 50).map(s => ({
            title:  s.title,
            artist: s.artist || s.tags,
            url:    s.url,
            cover:  s.cover,
            id:     s.id,
        })),
    }));
}

function bindAudioEvents() {
    if (!audio) return;
    audio.addEventListener('timeupdate', () => {
        if (!audio.duration) return;
        const pct = (audio.currentTime / audio.duration) * 100;
        if (progressFill)  progressFill.style.width = pct + '%';
        if (progressThumb) progressThumb.style.left  = pct + '%';
        if (currentTimeEl) currentTimeEl.innerText = fmt(audio.currentTime);
        if (totalTimeEl)   totalTimeEl.innerText   = fmt(audio.duration);
    });

    audio.addEventListener('ended', () => {
        if (_repeatOn) { audio.currentTime = 0; playAudio(); }
        else nextSong();
    });

    audio.addEventListener('error', () => {
        console.warn('Audio error, skipping to next.');
        setTimeout(nextSong, 1500);
    });

    // Save state periodically
    setInterval(saveState, 5000);
}

function fmt(s) {
    if (!s || isNaN(s)) return '0:00';
    const m = Math.floor(s / 60), sec = Math.floor(s % 60);
    return `${m}:${sec < 10 ? '0' : ''}${sec}`;
}
