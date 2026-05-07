// ============================================================
// movies.js – Drama/Movie logic using Sansekai API
// ============================================================
const DRAMA_API = 'https://api.sansekai.my.id/api/dramabox';

let _moviesLoaded   = false;
let _currentDrama   = null;
let _allEpisodes    = [];
let _categoryCache  = {};

/* ---- Load category ---- */
window.loadDramaCategory = function (cat, tabEl) {
    // Update active tab
    document.querySelectorAll('.movie-tab').forEach(t => t.classList.remove('active'));
    if (tabEl) tabEl.classList.add('active');

    const container = document.getElementById('movie-container');
    if (!container) return;

    // Use cache if available
    if (_categoryCache[cat]) {
        renderDramaGrid(_categoryCache[cat], container);
        return;
    }

    container.innerHTML = `<div class="loading-state" style="grid-column:1/-1;padding:60px 0">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
        <span>Memuat drama...</span></div>`;

    fetch(`${DRAMA_API}/${cat}`)
        .then(r => r.json())
        .then(data => {
            const list = Array.isArray(data) ? data : (data.data || []);
            _categoryCache[cat] = list;
            renderDramaGrid(list, container);
            _moviesLoaded = true;
        })
        .catch(() => {
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
                <div class="movie-thumb-eps">${drama.chapterCount} Eps</div>
            </div>
            <h3>${drama.bookName}</h3>
            <div class="movie-tags">${drama.tags ? drama.tags.slice(0,2).join(' • ') : ''}</div>
            <div class="movie-playcount"><i class="fa-solid fa-play" style="font-size:.6rem"></i> ${drama.playCount || ''}</div>`;

        container.appendChild(card);
    });
}

/* ---- Open drama detail modal (Cinema Flow layout) ---- */
window.openDramaDetail = function (drama) {
    _currentDrama = drama;
    const modal    = document.getElementById('drama-modal');
    const body     = document.getElementById('drama-modal-body');
    if (!modal || !body) return;

    // Show modal with loading
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

    // Fetch episodes
    fetch(`${DRAMA_API}/allepisode?bookId=${drama.bookId}`)
        .then(r => r.json())
        .then(data => {
            const episodes = Array.isArray(data) ? data : (data.chapterList || data.data || []);
            _allEpisodes = episodes;
            renderEpisodes(episodes);
        })
        .catch(() => {
            const grid = document.getElementById('episode-grid');
            if (grid) grid.innerHTML = `<p style="color:red">Gagal memuat episode.</p>`;
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
        const btn = document.createElement('button');
        btn.className = 'ep-btn';
        btn.innerText = `Ep ${ep.chapterNo || (i + 1)}`;
        btn.onclick = () => playEpisode(ep);
        grid.appendChild(btn);
    });
}

/* ---- Play episode ---- */
window.playEpisode = function (ep) {
    const videoEl = document.getElementById('cinema-video');
    const body    = document.getElementById('drama-modal-body');

    // Show video player in modal
    const existingPlayer = document.getElementById('cinema-player-wrap');
    if (existingPlayer) existingPlayer.remove();

    const wrap = document.createElement('div');
    wrap.id = 'cinema-player-wrap';
    wrap.className = 'cinema-player-wrap';
    wrap.innerHTML = `
        <div class="cinema-loading">
            <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
            <p>Mendekripsi URL video Episode ${ep.chapterNo}...</p>
        </div>`;
    body.prepend(wrap);
    wrap.scrollIntoView({ behavior: 'smooth' });

    // Pause music if playing
    if (window.isPlaying && typeof pauseAudio === 'function') pauseAudio();

    // Decrypt video URL
    const encUrl = ep.videoUrl || ep.playUrl || ep.url || '';
    if (!encUrl) {
        wrap.innerHTML = `<p style="color:red;text-align:center;padding:20px">URL video tidak tersedia untuk episode ini.</p>`;
        return;
    }

    fetch(`${DRAMA_API}/decrypt?url=${encodeURIComponent(encUrl)}`)
        .then(r => r.json())
        .then(res => {
            const videoUrl = res.url || res.data || res.videoUrl || res;
            if (!videoUrl || typeof videoUrl !== 'string') throw new Error('No URL');
            wrap.innerHTML = `
                <video id="cinema-video" class="cinema-video" controls autoplay>
                    <source src="${videoUrl}" type="video/mp4">
                    Browser Anda tidak mendukung video HTML5.
                </video>
                <div class="cinema-video-info">
                    <strong>${_currentDrama?.bookName || ''}</strong> – Episode ${ep.chapterNo}
                </div>`;
        })
        .catch(() => {
            wrap.innerHTML = `<div style="text-align:center;padding:24px;color:var(--text-light)">
                <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
                <p>Gagal mendekripsi URL. API mungkin memerlukan autentikasi tambahan.</p>
                <small>Episode: ${ep.chapterNo} | BookID: ${_currentDrama?.bookId}</small>
            </div>`;
        });
};

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
