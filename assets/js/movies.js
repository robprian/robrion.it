// ============================================================
// movies.js – Drama/Movie logic using Sansekai API via PHP proxy
// ============================================================
const DRAMA_API    = '/api/drama.php?endpoint=';
const DRAMA_DIRECT = 'https://api.sansekai.my.id/api/dramabox';

let _moviesLoaded   = false;
let _currentDrama   = null;
let _allEpisodes    = [];
let _categoryCache  = {};

/* ---- Load category ---- */
window.loadDramaCategory = function (cat, tabEl) {
    document.querySelectorAll('.movie-tab').forEach(t => t.classList.remove('active'));
    if (tabEl) tabEl.classList.add('active');

    const container = document.getElementById('movie-container');
    if (!container) return;

    if (_categoryCache[cat]) {
        renderDramaGrid(_categoryCache[cat], container);
        return;
    }

    container.innerHTML = `<div class="loading-state" style="grid-column:1/-1;padding:60px 0">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
        <span>Memuat drama...</span></div>`;

    fetch(`${DRAMA_API}${cat}`)
        .then(r => r.json())
        .then(data => {
            const list = Array.isArray(data) ? data : (data.data || data.result || []);
            _categoryCache[cat] = list;
            renderDramaGrid(list, container);
            _moviesLoaded = true;
        })
        .catch(err => {
            console.error('loadDramaCategory error:', err);
            container.innerHTML = `<p style="color:red;grid-column:1/-1;text-align:center;padding:40px">
                Gagal memuat data dari server API.</p>`;
        });
};

window.loadMovies = function () {
    if (_moviesLoaded) return;
    loadDramaCategory('foryou', document.querySelector('.movie-tab'));
};

/* ---- Render drama cards ---- */
function renderDramaGrid (list, container) {
    if (!list || list.length === 0) {
        container.innerHTML = `<p style="grid-column:1/-1;text-align:center;color:var(--text-light);padding:40px">Tidak ada data.</p>`;
        return;
    }

    container.innerHTML = '';
    list.forEach(drama => {
        const card = document.createElement('div');
        card.className = 'movie-card';
        card.onclick = () => openDramaDetail(drama);

        const corner = drama.corner
            ? `<div class="movie-corner" style="background:${drama.corner.color}">${drama.corner.name}</div>` : '';

        card.innerHTML = `
            <div class="movie-thumb" style="background-image:url('${drama.coverWap}')">
                ${corner}
                <div class="movie-thumb-eps">${drama.chapterCount || '?'} Eps</div>
            </div>
            <h3>${drama.bookName}</h3>
            <div class="movie-tags">${drama.tags ? drama.tags.slice(0,2).join(' • ') : ''}</div>
            <div class="movie-playcount"><i class="fa-solid fa-play" style="font-size:.6rem"></i> ${drama.playCount || ''}</div>`;

        container.appendChild(card);
    });
}

/* ---- Open drama detail modal ---- */
window.openDramaDetail = function (drama) {
    _currentDrama = drama;
    const modal = document.getElementById('drama-modal');
    const body  = document.getElementById('drama-modal-body');
    if (!modal || !body) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const cornerBadge = drama.corner
        ? `<span class="drama-corner-badge" style="background:${drama.corner.color}">${drama.corner.name}</span>` : '';
    const stars = drama.rankVo ? `<span class="drama-rank">${drama.rankVo.recCopy}</span>` : '';
    const tags  = drama.tags ? drama.tags.map(t => `<span class="drama-tag">${t}</span>`).join('') : '';

    body.innerHTML = `
        <div class="drama-detail-header">
            <img class="drama-detail-cover" src="${drama.coverWap}" alt="${drama.bookName}" onerror="this.style.display='none'">
            <div class="drama-detail-info">
                <div class="drama-badges">${cornerBadge} ${stars}</div>
                <h2>${drama.bookName}</h2>
                <p class="drama-intro">${drama.introduction || 'Tidak ada sinopsis.'}</p>
                <div class="drama-tags-wrap">${tags}</div>
                <div class="drama-meta">
                    <span><i class="fa-solid fa-film"></i> ${drama.chapterCount} Episode</span>
                    <span><i class="fa-solid fa-eye"></i> ${drama.playCount || 'N/A'} views</span>
                </div>
            </div>
        </div>
        <div class="episode-section">
            <h3 class="ep-section-title"><i class="fa-solid fa-list"></i> Pilih Episode</h3>
            <div class="episode-grid" id="episode-grid">
                <div class="loading-state"><i class="fa-solid fa-circle-notch fa-spin"></i><span>Memuat episode...</span></div>
            </div>
        </div>`;

    // Fetch all episodes
    fetch(`${DRAMA_API}allepisode&bookId=${drama.bookId}`)
        .then(r => r.json())
        .then(data => {
            console.log('[DramaBox] allepisode raw response:', JSON.stringify(data).substring(0, 500));

            // Try multiple possible structures
            let episodes = [];
            if (Array.isArray(data)) {
                episodes = data;
            } else if (data.chapterList && Array.isArray(data.chapterList)) {
                episodes = data.chapterList;
            } else if (data.data && Array.isArray(data.data)) {
                episodes = data.data;
            } else if (data.result && Array.isArray(data.result)) {
                episodes = data.result;
            } else if (data.episodes && Array.isArray(data.episodes)) {
                episodes = data.episodes;
            } else {
                // If it's an object with numeric keys or nested structure
                const keys = Object.keys(data);
                for (const key of keys) {
                    if (Array.isArray(data[key]) && data[key].length > 0) {
                        episodes = data[key];
                        console.log('[DramaBox] Found episodes in key:', key);
                        break;
                    }
                }
            }

            if (episodes.length > 0) {
                console.log('[DramaBox] First episode keys:', Object.keys(episodes[0]));
                console.log('[DramaBox] First episode data:', JSON.stringify(episodes[0]).substring(0, 300));
            }

            _allEpisodes = episodes;
            renderEpisodes(episodes);
        })
        .catch(err => {
            console.error('[DramaBox] allepisode error:', err);
            const grid = document.getElementById('episode-grid');
            if (grid) grid.innerHTML = `<p style="color:red">Gagal memuat episode: ${err.message}</p>`;
        });
};

function renderEpisodes (episodes) {
    const grid = document.getElementById('episode-grid');
    if (!grid) return;
    if (!episodes || episodes.length === 0) {
        grid.innerHTML = `<p style="color:var(--text-light)">Tidak ada episode tersedia.</p>`;
        return;
    }
    grid.innerHTML = '';
    episodes.forEach((ep, i) => {
        const epNum = ep.chapterNo || ep.chapterId || ep.chapter_no || ep.number || ep.idx || (i + 1);
        const btn = document.createElement('button');
        btn.className = 'ep-btn';
        btn.innerText = `Ep ${epNum}`;
        btn.title = ep.chapterName || ep.title || `Episode ${epNum}`;
        btn.onclick = () => playEpisode(ep, epNum);
        grid.appendChild(btn);
    });
}

/* ---- Helper: find video URL from episode object ---- */
function findVideoUrl(ep) {
    // Try every possible field name used by DramaBox API
    const candidates = [
        ep.videoUrl, ep.playUrl, ep.url, ep.video_url,
        ep.chapterUrl, ep.chapter_url, ep.content,
        ep.mp4Url, ep.hlsUrl, ep.m3u8Url,
        ep.streamUrl, ep.stream_url, ep.link,
        ep.play_url, ep.media_url, ep.mediaUrl,
        ep.src, ep.source
    ];

    for (const c of candidates) {
        if (c && typeof c === 'string' && c.length > 5) return c;
    }

    // Deep search: look through all string values in the object
    for (const [key, val] of Object.entries(ep)) {
        if (typeof val === 'string' && (
            val.includes('.mp4') || val.includes('.m3u8') ||
            val.includes('http') || val.includes('video')
        )) {
            console.log('[DramaBox] Found potential video URL in field:', key);
            return val;
        }
    }

    return null;
}

/* ---- Play episode ---- */
window.playEpisode = function (ep, epNum) {
    const body = document.getElementById('drama-modal-body');

    const existingPlayer = document.getElementById('cinema-player-wrap');
    if (existingPlayer) existingPlayer.remove();

    const wrap = document.createElement('div');
    wrap.id = 'cinema-player-wrap';
    wrap.className = 'cinema-player-wrap';
    wrap.innerHTML = `
        <div class="cinema-loading">
            <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
            <p>Memproses Episode ${epNum}...</p>
        </div>`;
    body.prepend(wrap);
    wrap.scrollIntoView({ behavior: 'smooth' });

    // Pause music
    if (window.isPlaying && typeof pauseAudio === 'function') pauseAudio();

    console.log('[DramaBox] Episode object:', JSON.stringify(ep).substring(0, 500));

    // 1) Try direct video URL from episode data
    const directUrl = findVideoUrl(ep);

    if (directUrl) {
        console.log('[DramaBox] Found direct URL:', directUrl);
        tryDecryptAndPlay(directUrl, wrap, epNum);
        return;
    }

    // 2) If episode has an ID but no URL, try fetching detail
    const epId = ep.chapterId || ep.id || ep.chapterNo || epNum;
    console.log('[DramaBox] No direct URL found. Trying detail fetch with chapterId:', epId);

    // Try the detail endpoint
    fetch(`${DRAMA_API}detail&bookId=${_currentDrama?.bookId}`)
        .then(r => r.json())
        .then(detailData => {
            console.log('[DramaBox] detail response:', JSON.stringify(detailData).substring(0, 500));

            // Look for video URL in detail response
            const detailUrl = findVideoUrl(detailData);
            if (detailUrl) {
                tryDecryptAndPlay(detailUrl, wrap, epNum);
            } else {
                // 3) Last resort: try decrypt endpoint with bookId + chapterId
                tryDirectDecrypt(ep, epId, wrap, epNum);
            }
        })
        .catch(() => {
            tryDirectDecrypt(ep, epId, wrap, epNum);
        });
};

function tryDecryptAndPlay(encUrl, wrap, epNum) {
    console.log('[DramaBox] Attempting decrypt for URL:', encUrl);

    fetch(`${DRAMA_API}decrypt&url=${encodeURIComponent(encUrl)}`)
        .then(r => r.json())
        .then(res => {
            console.log('[DramaBox] decrypt response:', JSON.stringify(res).substring(0, 500));

            // Try all possible response shapes
            let videoUrl = null;
            if (typeof res === 'string') videoUrl = res;
            else if (res.url) videoUrl = res.url;
            else if (res.data && typeof res.data === 'string') videoUrl = res.data;
            else if (res.data && res.data.url) videoUrl = res.data.url;
            else if (res.videoUrl) videoUrl = res.videoUrl;
            else if (res.result) videoUrl = typeof res.result === 'string' ? res.result : res.result.url;

            if (videoUrl && typeof videoUrl === 'string' && videoUrl.startsWith('http')) {
                showVideoPlayer(videoUrl, wrap, epNum);
            } else {
                // Maybe the encrypted URL itself IS the video URL
                if (encUrl.startsWith('http') && (encUrl.includes('.mp4') || encUrl.includes('.m3u8'))) {
                    showVideoPlayer(encUrl, wrap, epNum);
                } else {
                    showVideoError(wrap, epNum, 'Dekripsi berhasil tapi URL video tidak ditemukan.', res);
                }
            }
        })
        .catch(err => {
            console.error('[DramaBox] decrypt error:', err);
            // Fallback: try the URL directly as video
            if (encUrl.startsWith('http')) {
                showVideoPlayer(encUrl, wrap, epNum);
            } else {
                showVideoError(wrap, epNum, 'Gagal mendekripsi URL.', err.message);
            }
        });
}

function tryDirectDecrypt(ep, epId, wrap, epNum) {
    console.log('[DramaBox] Trying randomdrama as fallback video source...');

    // Build a test URL from bookId and chapterId
    const testUrls = [
        `${DRAMA_API}decrypt&url=${encodeURIComponent(ep.chapterUrl || '')}`,
        `${DRAMA_API}decrypt&url=${encodeURIComponent(ep.content || '')}`,
    ].filter(u => !u.endsWith('url='));

    if (testUrls.length > 0) {
        fetch(testUrls[0])
            .then(r => r.json())
            .then(res => {
                let url = typeof res === 'string' ? res : (res.url || res.data || res.videoUrl || '');
                if (url && url.startsWith('http')) {
                    showVideoPlayer(url, wrap, epNum);
                } else {
                    showVideoError(wrap, epNum, 'Video belum tersedia untuk episode ini.', ep);
                }
            })
            .catch(() => showVideoError(wrap, epNum, 'Video belum tersedia untuk episode ini.', ep));
    } else {
        showVideoError(wrap, epNum, 'Episode ini tidak memiliki URL video yang bisa diproses.', ep);
    }
}

function showVideoPlayer(videoUrl, wrap, epNum) {
    console.log('[DramaBox] Playing video:', videoUrl);
    const isHls = videoUrl.includes('.m3u8');

    if (isHls) {
        wrap.innerHTML = `
            <div style="text-align:center;padding:20px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
                <p style="margin-bottom:12px"><i class="fa-solid fa-play-circle" style="color:var(--accent);font-size:2rem"></i></p>
                <p style="font-weight:600">${_currentDrama?.bookName || 'Drama'} – Episode ${epNum}</p>
                <p style="font-size:.82rem;color:var(--text-light);margin:8px 0">Stream HLS terdeteksi. Klik untuk buka di tab baru.</p>
                <a href="${videoUrl}" target="_blank" style="
                    display:inline-flex;align-items:center;gap:8px;
                    background:var(--bg);box-shadow:5px 5px 10px rgba(163,177,198,.6),-5px -5px 10px rgba(255,255,255,.8);
                    border:none;border-radius:16px;padding:10px 24px;
                    font-family:inherit;font-size:.9rem;font-weight:600;color:var(--accent);
                    text-decoration:none;cursor:pointer;transition:all .2s">
                    <i class="fa-solid fa-external-link-alt"></i> Buka Video
                </a>
            </div>`;
    } else {
        wrap.innerHTML = `
            <video id="cinema-video" class="cinema-video" controls autoplay playsinline>
                <source src="${videoUrl}" type="video/mp4">
                Browser tidak mendukung video HTML5.
            </video>
            <div class="cinema-video-info">
                <strong>${_currentDrama?.bookName || ''}</strong> – Episode ${epNum}
            </div>`;
    }
}

function showVideoError(wrap, epNum, message, debugData) {
    console.warn('[DramaBox] Video error:', message, debugData);
    wrap.innerHTML = `
        <div style="text-align:center;padding:24px;color:var(--text-light);background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
            <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
            <p style="font-weight:600;margin-bottom:6px">${message}</p>
            <small>Episode: ${epNum} | BookID: ${_currentDrama?.bookId || '?'}</small>
            <br><small style="opacity:.5">Buka console browser (F12) untuk debug info.</small>
        </div>`;
}

/* ---- Close modal ---- */
window.closeDramaModal = function (e) {
    if (e && e.target !== document.getElementById('drama-modal')) return;
    const modal = document.getElementById('drama-modal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
    const video = document.getElementById('cinema-video');
    if (video) { video.pause(); video.src = ''; }
    _currentDrama = null;
    _allEpisodes  = [];
};
