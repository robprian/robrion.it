// ============================================================
// movies.js – Drama/Movie logic using Sansekai DramaBox API
// Video URL: ep.cdnList[].videoPathList[].videoPath
// ============================================================
const DRAMA_API    = '/api/drama.php?endpoint=';
const DRAMA_DIRECT = 'https://api.sansekai.my.id/api/dramabox';

let _moviesLoaded   = false;
let _currentDrama   = null;
let _allEpisodes    = [];
let _categoryCache  = {};

/* ── Load category ── */
window.loadDramaCategory = function (cat, tabEl) {
    document.querySelectorAll('.movie-tab').forEach(t => t.classList.remove('active'));
    if (tabEl) tabEl.classList.add('active');
    const container = document.getElementById('movie-container');
    if (!container) return;
    if (_categoryCache[cat]) { renderDramaGrid(_categoryCache[cat], container); return; }

    container.innerHTML = `<div class="loading-state" style="grid-column:1/-1;padding:60px 0">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i><span>Memuat drama...</span></div>`;

    fetch(`${DRAMA_API}${cat}`)
        .then(r => r.json())
        .then(data => {
            const list = Array.isArray(data) ? data : (data.data || data.result || []);
            _categoryCache[cat] = list;
            renderDramaGrid(list, container);
            _moviesLoaded = true;
        })
        .catch(() => {
            container.innerHTML = `<p style="color:red;grid-column:1/-1;text-align:center;padding:40px">Gagal memuat data dari API.</p>`;
        });
};

window.loadMovies = function () {
    if (_moviesLoaded) return;
    loadDramaCategory('foryou', document.querySelector('.movie-tab'));
};

/* ── Render drama cards ── */
function renderDramaGrid(list, container) {
    if (!list || !list.length) {
        container.innerHTML = `<p style="grid-column:1/-1;text-align:center;color:var(--text-light);padding:40px">Tidak ada data.</p>`;
        return;
    }
    container.innerHTML = '';
    list.forEach(drama => {
        const card = document.createElement('div');
        card.className = 'movie-card';
        card.onclick = () => openDramaDetail(drama);
        const corner = drama.corner ? `<div class="movie-corner" style="background:${drama.corner.color}">${drama.corner.name}</div>` : '';
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

/* ── Extract video URL from episode's nested cdnList structure ── */
function extractVideoUrl(ep) {
    if (!ep.cdnList || !Array.isArray(ep.cdnList)) return null;

    // 1) Pick default CDN first, fallback to first
    let cdn = ep.cdnList.find(c => c.isDefault === 1) || ep.cdnList[0];
    if (!cdn || !cdn.videoPathList || !cdn.videoPathList.length) return null;

    // 2) Pick quality: prefer default (720p), skip VIP-only
    let video = cdn.videoPathList.find(v => v.isDefault === 1 && v.isVipEquity === 0);
    if (!video) video = cdn.videoPathList.find(v => v.isVipEquity === 0);
    if (!video) video = cdn.videoPathList[0]; // last resort

    return video ? video.videoPath : null;
}

/* ── Extract subtitle URL (prefer Indonesian) ── */
function extractSubtitleUrl(ep) {
    if (!ep.subLanguageVoList || !Array.isArray(ep.subLanguageVoList)) return null;
    let sub = ep.subLanguageVoList.find(s => s.captionLanguage === 'in' && s.url);
    if (!sub) sub = ep.subLanguageVoList.find(s => s.captionLanguage === 'en' && s.url);
    return sub ? sub.url : null;
}

/* ── Open drama detail modal ── */
window.openDramaDetail = function (drama) {
    _currentDrama = drama;
    const modal = document.getElementById('drama-modal');
    const body  = document.getElementById('drama-modal-body');
    if (!modal || !body) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const cornerBadge = drama.corner ? `<span class="drama-corner-badge" style="background:${drama.corner.color}">${drama.corner.name}</span>` : '';
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

    fetch(`${DRAMA_API}allepisode&bookId=${drama.bookId}`)
        .then(r => r.json())
        .then(data => {
            let episodes = Array.isArray(data) ? data : (data.chapterList || data.data || data.result || []);
            // If still not array, search for first array property
            if (!Array.isArray(episodes)) {
                for (const k of Object.keys(data)) {
                    if (Array.isArray(data[k]) && data[k].length > 0) { episodes = data[k]; break; }
                }
            }
            _allEpisodes = episodes;
            renderEpisodes(episodes);
        })
        .catch(() => {
            document.getElementById('episode-grid').innerHTML = `<p style="color:red">Gagal memuat episode.</p>`;
        });
};

function renderEpisodes(episodes) {
    const grid = document.getElementById('episode-grid');
    if (!grid) return;
    if (!episodes || !episodes.length) {
        grid.innerHTML = `<p style="color:var(--text-light)">Tidak ada episode tersedia.</p>`;
        return;
    }
    grid.innerHTML = '';
    episodes.forEach((ep, i) => {
        const epNum  = ep.chapterName || `Ep ${ep.chapterIndex !== undefined ? ep.chapterIndex + 1 : i + 1}`;
        const isPaid = ep.isCharge === 1 || ep.chargeChapter === true;
        const btn = document.createElement('button');
        btn.className = 'ep-btn' + (isPaid ? ' ep-paid' : '');
        btn.innerHTML = isPaid ? `<i class="fa-solid fa-lock" style="font-size:.6rem;margin-right:4px"></i>${epNum}` : epNum;
        btn.title = isPaid ? `${epNum} (VIP)` : epNum;
        btn.onclick = () => playEpisode(ep, epNum);
        grid.appendChild(btn);
    });
}

/* ── Play episode ── */
window.playEpisode = function (ep, epLabel) {
    const body = document.getElementById('drama-modal-body');
    const old  = document.getElementById('cinema-player-wrap');
    if (old) old.remove();

    const wrap = document.createElement('div');
    wrap.id = 'cinema-player-wrap';
    wrap.className = 'cinema-player-wrap';
    wrap.innerHTML = `<div class="cinema-loading"><i class="fa-solid fa-circle-notch fa-spin fa-2x"></i><p>Memuat ${epLabel}...</p></div>`;
    body.prepend(wrap);
    wrap.scrollIntoView({ behavior: 'smooth' });

    // Pause music if playing
    if (window.isPlaying && typeof pauseAudio === 'function') pauseAudio();

    // Extract video URL from nested cdnList structure
    const videoUrl = extractVideoUrl(ep);

    if (!videoUrl) {
        wrap.innerHTML = `<div style="text-align:center;padding:24px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
            <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
            <p style="font-weight:600">Video tidak tersedia untuk ${epLabel}.</p>
            <small style="color:var(--text-light)">Episode ini mungkin memerlukan akun VIP DramaBox.</small></div>`;
        return;
    }

    // Get subtitle
    const subUrl = extractSubtitleUrl(ep);
    const subTrack = subUrl ? `<track kind="subtitles" src="${subUrl}" srclang="id" label="Indonesia" default>` : '';

    // Try decrypt first, fallback to direct play
    fetch(`${DRAMA_API}decrypt&url=${encodeURIComponent(videoUrl)}`)
        .then(r => r.json())
        .then(res => {
            let finalUrl = null;
            if (typeof res === 'string' && res.startsWith('http')) finalUrl = res;
            else if (res.url) finalUrl = res.url;
            else if (res.data && typeof res.data === 'string') finalUrl = res.data;
            else if (res.data && res.data.url) finalUrl = res.data.url;
            else if (res.videoUrl) finalUrl = res.videoUrl;

            showPlayer(finalUrl || videoUrl, subTrack, wrap, epLabel);
        })
        .catch(() => {
            // Decrypt failed, try direct URL
            showPlayer(videoUrl, subTrack, wrap, epLabel);
        });
};

function showPlayer(url, subTrack, wrap, epLabel) {
    wrap.innerHTML = `
        <video id="cinema-video" class="cinema-video" controls autoplay playsinline crossorigin="anonymous">
            <source src="${url}" type="video/mp4">
            ${subTrack}
            Browser tidak mendukung video HTML5.
        </video>
        <div class="cinema-video-info">
            <strong>${_currentDrama?.bookName || ''}</strong> – ${epLabel}
        </div>`;

    // Handle video error: fallback message
    const vid = document.getElementById('cinema-video');
    if (vid) {
        vid.onerror = function () {
            wrap.innerHTML = `<div style="text-align:center;padding:24px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
                <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
                <p style="font-weight:600">Gagal memutar video.</p>
                <p style="font-size:.82rem;color:var(--text-light);margin:8px 0">Server CDN mungkin memblokir akses langsung. Coba buka di tab baru:</p>
                <a href="${url}" target="_blank" rel="noopener" style="
                    display:inline-flex;align-items:center;gap:8px;
                    background:var(--bg);box-shadow:var(--shadow-btn);
                    border:none;border-radius:16px;padding:10px 24px;
                    font-family:inherit;font-size:.9rem;font-weight:600;color:var(--accent);
                    text-decoration:none;cursor:pointer;">
                    <i class="fa-solid fa-external-link-alt"></i> Buka Video
                </a></div>`;
        };
    }
}

/* ── Close modal ── */
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
