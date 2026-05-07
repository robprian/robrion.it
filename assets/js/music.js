// ============================================================
// music.js – Suno AI Music Player
// Features: API fetch + localStorage cache, album art,
//           synced lyrics, Canvas equalizer, persistence
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
let _rafId       = null;

window.isPlaying = false;

// ══════════════════════════════════════════════════
//  INIT & API FETCH
// ══════════════════════════════════════════════════

window.initMusic = function() {
    if (!audio) return;
    audio.volume = _vol;
    // Do NOT set crossOrigin — Suno CDN doesn't support CORS headers,
    // setting it would PREVENT audio from playing entirely.
    bindAudioEvents();
    loadPlaylistFromCacheOrAPI();
};

async function loadPlaylistFromCacheOrAPI() {
    showPlaylistLoading('Memuat playlist dari Suno AI...');

    // 1) Try cache first for instant display
    const cached = getCache();
    if (cached && cached.length > 0) {
        playlistData = cached;
        onPlaylistLoaded();
        // Refresh in background silently
        fetchAllPages().then(fresh => {
            if (fresh.length > 0) { mergeAndSave(fresh); }
        }).catch(() => {});
        return;
    }

    // 2) Fetch fresh from API
    try {
        const songs = await fetchAllPages();
        if (songs.length > 0) {
            mergeAndSave(songs);
            onPlaylistLoaded();
        } else {
            showPlaylistLoading('Tidak ada lagu ditemukan di API.', true);
        }
    } catch(e) {
        console.error('Fetch playlist error:', e);
        showPlaylistLoading('Gagal memuat dari API. Coba refresh.', true);
    }
}

async function fetchAllPages() {
    const songs = [];
    let page = 1;
    let hasMore = true;

    while (hasMore && page <= 10) {
        showPlaylistLoading(`Mengambil halaman ${page}...`);
        try {
            const res = await fetch(`${SUNO_API}?page=${page}`);
            if (!res.ok) { hasMore = false; break; }
            const data = await res.json();

            // API returns array directly OR wrapped in object
            const items = Array.isArray(data) ? data : (data.data || data.items || data.songs || []);
            if (!Array.isArray(items) || !items.length) { hasMore = false; break; }

            items.forEach(item => {
                if (item.audio_url && item.status === 'complete') {
                    songs.push(normalizeSong(item));
                }
            });

            // If fewer than 20 results, no more pages
            if (items.length < 20) { hasMore = false; }
            else { page++; }
        } catch(e) {
            console.warn('Error fetching page', page, e);
            hasMore = false;
        }
    }

    return songs;
}

function normalizeSong(item) {
    return {
        id:       item.id,
        title:    item.title || 'Untitled',
        artist:   'Suno AI',
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
    // Merge: keep unique by ID, fresh data wins
    const map = {};
    playlistData.forEach(s => { map[s.id] = s; });
    fresh.forEach(s => { map[s.id] = s; });
    playlistData = Object.values(map);
    // Sort by created date descending (newest first)
    playlistData.sort((a, b) => new Date(b.created || 0) - new Date(a.created || 0));
    setCache(playlistData);
    renderPlaylist();
    if (playlistCount) playlistCount.innerText = playlistData.length;
}

// ── Cache helpers ──
function getCache() {
    try {
        const raw = localStorage.getItem(CACHE_KEY);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        // Support both { ts, data } format and direct array
        if (Array.isArray(parsed)) return parsed;
        if (parsed && parsed.data) {
            if (Date.now() - (parsed.ts || 0) > CACHE_TTL) return null;
            return parsed.data;
        }
        return null;
    } catch { return null; }
}

function setCache(data) {
    try {
        localStorage.setItem(CACHE_KEY, JSON.stringify({ ts: Date.now(), data }));
    } catch(e) {
        // Storage full – trim
        try {
            localStorage.setItem(CACHE_KEY, JSON.stringify({ ts: Date.now(), data: data.slice(0, 80) }));
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
    playlistEl.innerHTML = `<div class="loading-state" style="${isError?'color:#f87171':''}">
        ${isError ? '<i class="fa-solid fa-triangle-exclamation"></i>' : '<i class="fa-solid fa-circle-notch fa-spin"></i>'}
        <span>${msg}</span>
    </div>`;
}

function renderPlaylist(filter = '') {
    if (!playlistEl) return;
    playlistEl.innerHTML = '';
    const q = filter.toLowerCase();
    const filtered = q
        ? playlistData.filter(s =>
            s.title.toLowerCase().includes(q) ||
            (s.tags||'').toLowerCase().includes(q))
        : playlistData;

    if (!filtered.length) {
        playlistEl.innerHTML = '<div class="loading-state"><i class="fa-solid fa-search"></i> <span>Tidak ada hasil.</span></div>';
        return;
    }

    filtered.forEach((song, _i) => {
        const realIdx = playlistData.indexOf(song);
        const el = document.createElement('div');
        el.className = 'playlist-item' + (realIdx === currentIndex ? ' active' : '');
        el.id = `pitem-${realIdx}`;
        el.onclick = () => loadAndPlay(realIdx);
        const dur = song.duration ? fmt(song.duration) : '--:--';
        el.innerHTML = `
            <div class="item-index">${realIdx + 1}</div>
            <div class="item-thumb" ${song.cover ? `style="background-image:url('${song.cover}')"` : ''}>
                ${!song.cover ? '<i class="fa-solid fa-music" style="color:var(--text-light);font-size:.7rem"></i>' : ''}
            </div>
            <div class="item-info">
                <div class="item-title">${song.title}</div>
                <div class="item-artist">${song.tags || 'Suno AI'}</div>
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
        playerTags.innerHTML = (song.tags || '').split(/[\s,]+/).filter(Boolean)
            .map(t => `<span class="music-tag">${t}</span>`).join('');
    }

    // Album art
    if (albumArtEl) {
        if (song.cover) {
            albumArtEl.src = song.cover;
            albumArtEl.style.display = 'block';
            const ph = document.getElementById('album-placeholder');
            if (ph) ph.style.display = 'none';
        } else {
            albumArtEl.style.display = 'none';
            const ph = document.getElementById('album-placeholder');
            if (ph) ph.style.display = 'flex';
        }
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
    audio.play().then(() => {
        _playing = window.isPlaying = true;
        if (playIcon) playIcon.className = 'fa-solid fa-pause';
        startEQ();
        startLyricSync();
        saveState();
    }).catch(err => {
        console.warn('Play failed:', err);
        // Still start fake EQ for visual feedback
        _playing = window.isPlaying = true;
        if (playIcon) playIcon.className = 'fa-solid fa-pause';
        startEQ();
    });
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
        let n; do { n = Math.floor(Math.random() * playlistData.length); } while (n === currentIndex && playlistData.length > 1);
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
    if (!bar) return;
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
    renderPlaylist(q);
};

// ══════════════════════════════════════════════════
//  LYRICS ENGINE
// ══════════════════════════════════════════════════

function parseLyrics(raw) {
    _lyricLines = [];
    _lyricIdx   = 0;
    if (!raw) return;

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
    const lines = lyricsEl.querySelectorAll('.lyric-line');
    lines.forEach((el, i) => {
        el.classList.remove('lyric-active', 'lyric-past');
        if (i < idx) el.classList.add('lyric-past');
        if (i === idx) {
            el.classList.add('lyric-active');
            el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }
    });
}

// ══════════════════════════════════════════════════
//  CANVAS EQUALIZER (always animated, no Web Audio required)
// ══════════════════════════════════════════════════
// Note: Web Audio API's createMediaElementSource() requires CORS
// headers from the audio server. Suno's CDN does not provide them,
// so we use a visually-driven animated equalizer instead.

function startEQ() {
    stopEQ();
    if (!eqCanvas) return;

    const ctx = eqCanvas.getContext('2d');
    let frame = 0;
    // Persistent bar heights for smoothing
    const barCount = 52;
    const heights = new Array(barCount).fill(0);
    const targets = new Array(barCount).fill(0);
    const peaks   = new Array(barCount).fill(0);

    function draw() {
        _rafId = requestAnimationFrame(draw);
        frame++;

        const W = eqCanvas.width  = eqCanvas.offsetWidth  * (window.devicePixelRatio || 1);
        const H = eqCanvas.height = eqCanvas.offsetHeight * (window.devicePixelRatio || 1);
        ctx.clearRect(0, 0, W, H);

        const gap = 2;
        const barW = (W / barCount) - gap;

        // Generate smooth, musical-looking targets every few frames
        if (frame % 3 === 0) {
            for (let i = 0; i < barCount; i++) {
                // Bass heavy on left, treble on right
                const bassBoost  = Math.max(0, 1 - i / barCount) * 0.3;
                const wave1 = Math.sin(frame * 0.04 + i * 0.5)  * 0.3;
                const wave2 = Math.sin(frame * 0.07 + i * 0.3)  * 0.2;
                const wave3 = Math.sin(frame * 0.11 + i * 0.7)  * 0.15;
                const pulse = Math.sin(frame * 0.02) * 0.1; // global pulse
                const rnd   = (Math.random() - 0.5) * 0.15;
                targets[i] = Math.max(0.04, 0.35 + bassBoost + wave1 + wave2 + wave3 + pulse + rnd);
            }
        }

        for (let i = 0; i < barCount; i++) {
            // Smooth interpolation (spring-like)
            heights[i] += (targets[i] - heights[i]) * 0.18;
            const barH = Math.max(3, heights[i] * H * 0.92);
            const x = i * (barW + gap);

            // Gradient hue: teal(180) → cyan → blue → purple(280)
            const hue   = 180 + (i / barCount) * 100;
            const lum   = 50 + heights[i] * 20;
            const alpha = 0.55 + heights[i] * 0.45;

            // Bar fill
            const grad = ctx.createLinearGradient(x, H, x, H - barH);
            grad.addColorStop(0, `hsla(${hue}, 85%, ${lum}%, ${alpha})`);
            grad.addColorStop(1, `hsla(${hue + 30}, 90%, ${lum + 15}%, ${alpha * 0.7})`);
            ctx.fillStyle = grad;

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(x, H - barH, barW, barH, 3);
            } else {
                ctx.rect(x, H - barH, barW, barH);
            }
            ctx.fill();

            // Peak indicator (falling dot)
            if (barH > peaks[i]) peaks[i] = barH;
            else peaks[i] = Math.max(barH, peaks[i] - 1.2);

            ctx.fillStyle = `hsla(${hue}, 100%, 80%, 0.85)`;
            ctx.beginPath();
            ctx.arc(x + barW / 2, H - peaks[i] - 4, 2.5, 0, Math.PI * 2);
            ctx.fill();

            // Glow reflection at bottom
            ctx.fillStyle = `hsla(${hue}, 85%, ${lum}%, 0.12)`;
            ctx.fillRect(x, H, barW, 4);
        }
    }

    draw();
}

function stopEQ() {
    if (_rafId) { cancelAnimationFrame(_rafId); _rafId = null; }
    if (eqCanvas) {
        const ctx = eqCanvas.getContext('2d');
        if (ctx) ctx.clearRect(0, 0, eqCanvas.width, eqCanvas.height);
    }
}

// ══════════════════════════════════════════════════
//  STATE & EVENTS
// ══════════════════════════════════════════════════

function saveState() {
    if (!playlistData.length) return;
    const song = playlistData[currentIndex];
    try {
        localStorage.setItem(STATE_KEY, JSON.stringify({
            songId:      song?.id,
            index:       currentIndex,
            currentTime: audio ? audio.currentTime : 0,
            volume:      _vol,
            playing:     _playing,
        }));
    } catch {}
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

    audio.addEventListener('error', (e) => {
        console.warn('Audio error:', e);
        // Skip to next song after a delay
        setTimeout(() => {
            if (playlistData.length > 1) nextSong();
        }, 2000);
    });

    // Save state periodically
    setInterval(saveState, 5000);
}

function fmt(s) {
    if (!s || isNaN(s)) return '0:00';
    const m = Math.floor(s / 60), sec = Math.floor(s % 60);
    return `${m}:${sec < 10 ? '0' : ''}${sec}`;
}
