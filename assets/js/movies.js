// ============================================================
// movies.js – Multi-Provider Drama/Anime Streaming Platform
// Providers: DramaBox, PineDrama, ReelShort, ShortMax,
//            GoodShort, NetShort, FreeReels, DramaNova, Anime
// ============================================================
const API_BASE = '/api/drama.php';

// ── Provider Registry ──
const PROVIDERS = {
    dramabox: {
        name: 'DramaBox', icon: 'fa-solid fa-tv', color: '#FF6B6B',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'trending', label: 'Trending' },
            { key: 'latest', label: 'Terbaru' },
            { key: 'dubindo', label: 'Dub Indo' },
            { key: 'vip', label: 'VIP' },
        ],
        episodeEndpoint: 'allepisode',
        episodeParam: 'bookId',
        idField: 'bookId',
        videoExtract: 'cdnList',       // nested cdnList → videoPathList → videoPath
        decryptEndpoint: 'decrypt',    // needs decrypt → returns streamUrl
    },
    pinedrama: {
        name: 'PineDrama', icon: 'fa-solid fa-leaf', color: '#4ECDC4',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'trending', label: 'Trending' },
        ],
        episodeEndpoint: 'episode',
        episodeParam: 'id',
        idField: 'id',
        videoExtract: 'direct',
    },
    reelshort: {
        name: 'ReelShort', icon: 'fa-solid fa-film', color: '#FF8C42',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'homepage', label: 'Homepage' },
        ],
        episodeEndpoint: 'episode',
        episodeParam: 'id',
        idField: 'id',
        videoExtract: 'direct',
    },
    shortmax: {
        name: 'ShortMax', icon: 'fa-solid fa-bolt', color: '#FFD93D',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'latest', label: 'Terbaru' },
            { key: 'rekomendasi', label: 'Rekomendasi' },
            { key: 'vip', label: 'VIP' },
        ],
        episodeEndpoint: 'episode',
        episodeParam: 'id',
        idField: 'id',
        videoExtract: 'direct',
    },
    goodshort: {
        name: 'GoodShort', icon: 'fa-solid fa-thumbs-up', color: '#6BCB77',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'latest', label: 'Terbaru' },
            { key: 'trending', label: 'Trending' },
            { key: 'anime', label: 'Anime' },
        ],
        episodeEndpoint: 'allepisode',
        episodeParam: 'id',
        idField: 'id',
        videoExtract: 'cdnList',
        decryptEndpoint: 'decrypt',
    },
    netshort: {
        name: 'NetShort', icon: 'fa-solid fa-globe', color: '#4D96FF',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'theaters', label: 'Theater' },
        ],
        episodeEndpoint: 'allepisode',
        episodeParam: 'id',
        idField: 'id',
        videoExtract: 'auto',
    },
    freereels: {
        name: 'FreeReels', icon: 'fa-solid fa-video', color: '#9B59B6',
        categories: [
            { key: 'foryou', label: 'For You' },
            { key: 'homepage', label: 'Homepage' },
            { key: 'animepage', label: 'Anime' },
        ],
        episodeEndpoint: 'detailAndAllEpisode',
        episodeParam: 'id',
        idField: 'id',
        videoExtract: 'direct',
    },
    dramanova: {
        name: 'DramaNova', icon: 'fa-solid fa-star', color: '#E74C3C',
        categories: [
            { key: 'home', label: 'Home' },
            { key: 'drama18', label: '18+' },
        ],
        episodeEndpoint: 'detail',
        episodeParam: 'id',
        idField: 'id',
        videoEndpoint: 'getvideo',
        videoExtract: 'direct',
    },
    anime: {
        name: 'Anime', icon: 'fa-solid fa-dragon', color: '#E91E63',
        categories: [
            { key: 'latest', label: 'Terbaru' },
            { key: 'recommended', label: 'Rekomendasi' },
            { key: 'movie', label: 'Movie' },
        ],
        episodeEndpoint: 'detail',
        episodeParam: 'id',
        idField: 'id',
        videoEndpoint: 'getvideo',
        videoExtract: 'direct',
    },
};

let _currentProvider = 'dramabox';
let _currentDrama    = null;
let _allEpisodes     = [];
let _cache           = {};

// ── API helper ──
function apiFetch(provider, endpoint, params = {}) {
    const qs = new URLSearchParams({ provider, endpoint, ...params }).toString();
    return fetch(`${API_BASE}?${qs}`).then(r => r.json());
}

// ── Normalize drama list – handle varying field names ──
function normalizeDrama(raw) {
    return {
        id:       raw.bookId || raw.id || raw.dramaId || raw.drama_id || raw.contentId || '',
        title:    raw.bookName || raw.title || raw.name || raw.dramaName || raw.drama_name || 'Untitled',
        cover:    raw.coverWap || raw.cover || raw.poster || raw.image || raw.coverUrl || raw.img || '',
        epCount:  raw.chapterCount || raw.episodeCount || raw.episode_count || raw.totalEpisode || '?',
        tags:     raw.tags || raw.genre || [],
        intro:    raw.introduction || raw.synopsis || raw.description || raw.desc || '',
        plays:    raw.playCount || raw.views || raw.view_count || '',
        corner:   raw.corner || null,
        rankVo:   raw.rankVo || null,
        _raw:     raw, // keep original for episode fetching
    };
}

// ── Extract list from API response ──
function extractList(data) {
    if (Array.isArray(data)) return data;
    // Try common wrapper keys
    for (const k of ['data', 'result', 'list', 'items', 'dramas', 'videos', 'contents']) {
        if (data[k] && Array.isArray(data[k])) return data[k];
    }
    // Try first array property
    for (const v of Object.values(data)) {
        if (Array.isArray(v) && v.length > 0) return v;
    }
    return [];
}

// ══════════════════════════════════════════════════
//  UI RENDERING
// ══════════════════════════════════════════════════

/* ── Switch provider ── */
window.switchProvider = function(provKey) {
    _currentProvider = provKey;
    _cache = {};

    // Update provider tabs
    document.querySelectorAll('.provider-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`[data-provider="${provKey}"]`)?.classList.add('active');

    // Render category tabs
    const prov = PROVIDERS[provKey];
    const tabsEl = document.getElementById('category-tabs');
    if (tabsEl) {
        tabsEl.innerHTML = prov.categories.map((c, i) =>
            `<button class="movie-tab${i===0?' active':''}" onclick="loadCategory('${c.key}',this)">${c.label}</button>`
        ).join('');
    }

    // Load first category
    loadCategory(prov.categories[0].key, tabsEl?.querySelector('.movie-tab'));
};

/* ── Load category ── */
window.loadCategory = function(cat, tabEl) {
    document.querySelectorAll('.movie-tab').forEach(t => t.classList.remove('active'));
    if (tabEl) tabEl.classList.add('active');

    const container = document.getElementById('movie-container');
    if (!container) return;

    const cacheKey = `${_currentProvider}_${cat}`;
    if (_cache[cacheKey]) { renderGrid(_cache[cacheKey], container); return; }

    container.innerHTML = `<div class="loading-state" style="grid-column:1/-1;padding:60px 0">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i><span>Memuat...</span></div>`;

    apiFetch(_currentProvider, cat)
        .then(data => {
            const list = extractList(data).map(normalizeDrama);
            _cache[cacheKey] = list;
            renderGrid(list, container);
        })
        .catch(() => {
            container.innerHTML = `<p style="color:red;grid-column:1/-1;text-align:center;padding:40px">Gagal memuat dari ${PROVIDERS[_currentProvider].name}.</p>`;
        });
};

// Backward compat
window.loadDramaCategory = function(cat, tabEl) { loadCategory(cat, tabEl); };

/* ── Render grid ── */
function renderGrid(list, container) {
    if (!list.length) {
        container.innerHTML = `<p style="grid-column:1/-1;text-align:center;color:var(--text-light);padding:40px">Tidak ada data.</p>`;
        return;
    }
    container.innerHTML = '';
    list.forEach(d => {
        const card = document.createElement('div');
        card.className = 'movie-card';
        card.onclick = () => openDetail(d);
        const corner = d.corner ? `<div class="movie-corner" style="background:${d.corner.color}">${d.corner.name}</div>` : '';
        const tags = Array.isArray(d.tags) ? d.tags.slice(0,2).join(' • ') : (d.tags || '');
        card.innerHTML = `
            <div class="movie-thumb" style="background-image:url('${d.cover}')">
                ${corner}
                <div class="movie-thumb-eps">${d.epCount} Eps</div>
            </div>
            <h3>${d.title}</h3>
            <div class="movie-tags">${tags}</div>
            <div class="movie-playcount"><i class="fa-solid fa-play" style="font-size:.6rem"></i> ${d.plays}</div>`;
        container.appendChild(card);
    });
}

// ══════════════════════════════════════════════════
//  DETAIL MODAL & EPISODES
// ══════════════════════════════════════════════════

window.openDetail = function(drama) {
    _currentDrama = drama;
    const prov  = PROVIDERS[_currentProvider];
    const modal = document.getElementById('drama-modal');
    const body  = document.getElementById('drama-modal-body');
    if (!modal || !body) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const cornerBadge = drama.corner ? `<span class="drama-corner-badge" style="background:${drama.corner.color}">${drama.corner.name}</span>` : '';
    const stars = drama.rankVo ? `<span class="drama-rank">${drama.rankVo.recCopy || ''}</span>` : '';
    const tags  = Array.isArray(drama.tags) ? drama.tags.map(t => `<span class="drama-tag">${t}</span>`).join('') : '';

    body.innerHTML = `
        <div class="drama-detail-header">
            <img class="drama-detail-cover" src="${drama.cover}" alt="${drama.title}" onerror="this.style.display='none'">
            <div class="drama-detail-info">
                <div class="drama-badges">${cornerBadge} ${stars}
                    <span class="drama-provider-badge" style="background:${prov.color}">${prov.name}</span>
                </div>
                <h2>${drama.title}</h2>
                <p class="drama-intro">${drama.intro || 'Tidak ada sinopsis.'}</p>
                <div class="drama-tags-wrap">${tags}</div>
                <div class="drama-meta">
                    <span><i class="fa-solid fa-film"></i> ${drama.epCount} Episode</span>
                    ${drama.plays ? `<span><i class="fa-solid fa-eye"></i> ${drama.plays} views</span>` : ''}
                </div>
            </div>
        </div>
        <div class="episode-section">
            <h3 class="ep-section-title"><i class="fa-solid fa-list"></i> Pilih Episode</h3>
            <div class="episode-grid" id="episode-grid">
                <div class="loading-state"><i class="fa-solid fa-circle-notch fa-spin"></i><span>Memuat episode...</span></div>
            </div>
        </div>`;

    // Fetch episodes based on provider config
    const epEndpoint = prov.episodeEndpoint;
    const paramName  = prov.episodeParam;
    const dramaId    = drama.id;

    apiFetch(_currentProvider, epEndpoint, { [paramName]: dramaId })
        .then(data => {
            let episodes = extractList(data);
            // FreeReels returns detail+episodes in one
            if (!episodes.length && data.episodes) episodes = data.episodes;
            if (!episodes.length && data.chapterList) episodes = data.chapterList;
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
    if (!episodes.length) {
        grid.innerHTML = `<p style="color:var(--text-light)">Tidak ada episode.</p>`;
        return;
    }
    grid.innerHTML = '';
    episodes.forEach((ep, i) => {
        const label  = ep.chapterName || ep.title || ep.name || ep.episodeName || `Ep ${ep.chapterIndex !== undefined ? ep.chapterIndex+1 : (ep.episode || i+1)}`;
        const isPaid = ep.isCharge === 1 || ep.chargeChapter === true || ep.vip === true;
        const btn = document.createElement('button');
        btn.className = 'ep-btn' + (isPaid ? ' ep-paid' : '');
        btn.innerHTML = isPaid ? `<i class="fa-solid fa-lock" style="font-size:.6rem;margin-right:4px"></i>${label}` : label;
        btn.onclick = () => playEpisode(ep, label, i);
        grid.appendChild(btn);
    });
}

// ══════════════════════════════════════════════════
//  VIDEO PLAYBACK – with Quality Picker & Decrypt
// ══════════════════════════════════════════════════

/* ── Collect all qualities from cdnList ── */
function getQualities(ep) {
    if (!ep.cdnList || !Array.isArray(ep.cdnList)) return [];
    const qualities = [];
    for (const cdn of ep.cdnList) {
        if (!cdn.videoPathList || !Array.isArray(cdn.videoPathList)) continue;
        for (const vp of cdn.videoPathList) {
            if (!vp.videoPath) continue;
            qualities.push({
                quality:    vp.quality || 'Unknown',
                url:        vp.videoPath,
                isDefault:  vp.isDefault === 1,
                isVip:      vp.isVipEquity === 1,
                cdnDomain:  cdn.cdnDomain || '',
                isEntry:    vp.isEntry === 1,
            });
        }
    }
    // Sort: non-VIP first, then by quality descending
    qualities.sort((a, b) => {
        if (a.isVip !== b.isVip) return a.isVip ? 1 : -1;
        return (b.quality || 0) - (a.quality || 0);
    });
    return qualities;
}

/* ── Get subtitle ── */
function extractSubtitle(ep) {
    if (!ep.subLanguageVoList) return null;
    const sub = ep.subLanguageVoList.find(s => s.captionLanguage === 'in' && s.url)
             || ep.subLanguageVoList.find(s => s.captionLanguage === 'en' && s.url);
    return sub?.url || null;
}

/* ── Extract direct video URL (for non-cdnList providers) ── */
function extractDirectUrl(data) {
    if (typeof data === 'string' && data.startsWith('http')) return data;
    const fields = ['streamUrl', 'videoUrl', 'playUrl', 'url', 'video_url',
        'mp4Url', 'hlsUrl', 'm3u8Url', 'stream', 'link', 'src', 'source', 'video', 'data'];
    for (const f of fields) {
        if (data[f] && typeof data[f] === 'string' && data[f].startsWith('http')) return data[f];
    }
    // Deep search
    for (const val of Object.values(data)) {
        if (typeof val === 'string' && val.startsWith('http') &&
            (val.includes('.mp4') || val.includes('.m3u8') || val.includes('stream') || val.includes('video'))) {
            return val;
        }
    }
    return null;
}

/* ── Play episode – main entry ── */
window.playEpisode = function(ep, epLabel, epIndex) {
    const prov = PROVIDERS[_currentProvider];
    const body = document.getElementById('drama-modal-body');
    const old  = document.getElementById('cinema-player-wrap');
    if (old) old.remove();

    const wrap = document.createElement('div');
    wrap.id = 'cinema-player-wrap';
    wrap.className = 'cinema-player-wrap';
    body.prepend(wrap);
    wrap.scrollIntoView({ behavior: 'smooth' });

    if (window.isPlaying && typeof pauseAudio === 'function') pauseAudio();

    const subUrl   = extractSubtitle(ep);
    const subTrack = subUrl ? `<track kind="subtitles" src="${subUrl}" srclang="id" label="Indonesia" default>` : '';

    // ── STRATEGY A: Providers with cdnList + decrypt (DramaBox, GoodShort) ──
    if (prov.videoExtract === 'cdnList' && prov.decryptEndpoint) {
        const qualities = getQualities(ep);
        if (qualities.length === 0) {
            showError(wrap, epLabel, 'Tidak ada video tersedia untuk episode ini.');
            return;
        }
        // Show quality picker
        showQualityPicker(wrap, qualities, subTrack, epLabel);
        return;
    }

    // ── STRATEGY B: Providers with separate video endpoint (DramaNova, Anime) ──
    if (prov.videoEndpoint) {
        wrap.innerHTML = loadingHTML(epLabel);
        const vidId = ep.id || ep.episodeId || ep.chapterId || _currentDrama.id;
        apiFetch(_currentProvider, prov.videoEndpoint, { id: vidId, ep: epIndex + 1 })
            .then(data => {
                const url = extractDirectUrl(data);
                if (url) showPlayer(url, subTrack, wrap, epLabel);
                else showError(wrap, epLabel, 'Video tidak ditemukan.');
            })
            .catch(() => showError(wrap, epLabel, 'Gagal mengambil video.'));
        return;
    }

    // ── STRATEGY C: Direct / auto providers (PineDrama, ReelShort, ShortMax, NetShort, FreeReels) ──
    // Try existing episode data first
    const directUrl = extractDirectUrl(ep);
    if (directUrl) {
        showPlayer(directUrl, subTrack, wrap, epLabel);
        return;
    }

    // Otherwise fetch from episode endpoint
    wrap.innerHTML = loadingHTML(epLabel);
    const epId = ep.id || ep.episodeId || ep.chapterId || '';
    apiFetch(_currentProvider, prov.episodeEndpoint, { id: _currentDrama.id, ep: epIndex + 1, episodeId: epId })
        .then(data => {
            const url = extractDirectUrl(data);
            if (url) showPlayer(url, subTrack, wrap, epLabel);
            else showError(wrap, epLabel, 'Stream URL tidak ditemukan.');
        })
        .catch(() => showError(wrap, epLabel, 'Gagal mengambil stream.'));
};

/* ── Quality Picker UI ── */
function showQualityPicker(wrap, qualities, subTrack, epLabel) {
    const prov = PROVIDERS[_currentProvider];
    wrap.innerHTML = `
        <div class="quality-picker" style="padding:20px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-btn)">
            <h4 style="margin:0 0 12px;font-size:.95rem;display:flex;align-items:center;gap:8px">
                <i class="fa-solid fa-sliders" style="color:${prov.color}"></i> Pilih Kualitas – ${epLabel}
            </h4>
            <div class="quality-grid" style="display:flex;gap:10px;flex-wrap:wrap">
                ${qualities.map((q, i) => `
                    <button class="quality-btn" data-qi="${i}" style="
                        display:flex;flex-direction:column;align-items:center;gap:4px;
                        padding:14px 22px;border:none;border-radius:14px;cursor:pointer;
                        font-family:inherit;font-weight:600;font-size:.9rem;
                        background:${q.isDefault ? prov.color : 'var(--bg)'};
                        color:${q.isDefault ? '#fff' : 'var(--text)'};
                        box-shadow:${q.isDefault ? '0 4px 14px ' + prov.color + '55' : 'var(--shadow-btn)'};
                        transition:all .2s;position:relative;
                    ">
                        <span style="font-size:1.1rem">${q.quality}p</span>
                        <span style="font-size:.65rem;opacity:.7">${q.isVip ? '🔒 VIP' : '🆓 Free'}</span>
                        ${q.isDefault ? '<span style="position:absolute;top:-6px;right:-6px;background:#4CAF50;color:#fff;font-size:.55rem;padding:2px 6px;border-radius:8px">DEFAULT</span>' : ''}
                    </button>
                `).join('')}
            </div>
        </div>`;

    // Attach click handlers
    wrap.querySelectorAll('.quality-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const qi = parseInt(btn.dataset.qi);
            const chosen = qualities[qi];
            // Show loading, then decrypt
            wrap.innerHTML = loadingHTML(`${epLabel} @ ${chosen.quality}p`);
            decryptAndPlay(chosen.url, subTrack, wrap, `${epLabel} (${chosen.quality}p)`);
        });
    });
}

/* ── Decrypt encrypted URL and play ── */
function decryptAndPlay(encryptedUrl, subTrack, wrap, epLabel) {
    const prov = PROVIDERS[_currentProvider];
    // Call decrypt API directly (not through PHP proxy, to avoid double-encoding)
    // The decrypt endpoint: GET /api/dramabox/decrypt?url=<encrypted>
    // Returns: { success: true, streamUrl: "https://api.sansekai.my.id/api/dramabox/decrypt-stream?url=..." }
    const decryptUrl = `https://api.sansekai.my.id/api/${_currentProvider}/${prov.decryptEndpoint}?url=${encodeURIComponent(encryptedUrl)}`;

    fetch(decryptUrl)
        .then(r => r.json())
        .then(res => {
            console.log('[Decrypt Response]', res);
            if (res.success && res.streamUrl) {
                showPlayer(res.streamUrl, subTrack, wrap, epLabel);
            } else if (res.streamUrl) {
                showPlayer(res.streamUrl, subTrack, wrap, epLabel);
            } else if (res.url) {
                showPlayer(res.url, subTrack, wrap, epLabel);
            } else {
                // Show debug info
                wrap.innerHTML = `<div style="text-align:center;padding:24px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
                    <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
                    <p style="font-weight:600">Dekripsi gagal untuk ${epLabel}</p>
                    <pre style="text-align:left;font-size:.7rem;max-height:120px;overflow:auto;background:#1a1a2e;color:#0f0;padding:10px;border-radius:8px;margin:8px 0">${JSON.stringify(res, null, 2)}</pre>
                    <a href="${encryptedUrl}" target="_blank" rel="noopener" class="fallback-link" style="display:inline-flex;align-items:center;gap:8px;margin-top:8px;padding:10px 20px;border-radius:12px;background:var(--accent);color:#fff;text-decoration:none;font-weight:600;font-size:.85rem">
                        <i class="fa-solid fa-external-link-alt"></i> Buka URL Langsung
                    </a>
                </div>`;
            }
        })
        .catch(err => {
            console.error('[Decrypt Error]', err);
            // Fallback: try playing encrypted URL directly
            showPlayer(encryptedUrl, subTrack, wrap, epLabel + ' (raw)');
        });
}

function loadingHTML(label) {
    return `<div class="cinema-loading" style="text-align:center;padding:30px">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:var(--accent)"></i>
        <p style="margin-top:10px;font-weight:500">Memuat ${label}...</p>
    </div>`;
}

function showPlayer(url, subTrack, wrap, epLabel) {
    const prov = PROVIDERS[_currentProvider];
    wrap.innerHTML = `
        <video id="cinema-video" class="cinema-video" controls autoplay playsinline crossorigin="anonymous">
            <source src="${url}" type="video/mp4">
            ${subTrack}
        </video>
        <div class="cinema-video-info" style="display:flex;align-items:center;gap:10px;padding:8px 12px;font-size:.82rem">
            <span class="cinema-provider" style="background:${prov.color};color:#fff;padding:3px 10px;border-radius:8px;font-size:.7rem;font-weight:600">${prov.name}</span>
            <strong>${_currentDrama?.title || ''}</strong> – ${epLabel}
        </div>`;

    const vid = document.getElementById('cinema-video');
    if (vid) vid.onerror = () => {
        wrap.innerHTML = `<div style="text-align:center;padding:24px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
            <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
            <p style="font-weight:600">Gagal memutar video.</p>
            <p style="font-size:.82rem;color:var(--text-light);margin:8px 0">CDN mungkin memblokir. Coba buka langsung:</p>
            <a href="${url}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;background:var(--accent);color:#fff;border:none;border-radius:16px;padding:10px 24px;font-size:.9rem;font-weight:600;text-decoration:none">
                <i class="fa-solid fa-external-link-alt"></i> Buka Video
            </a></div>`;
    };
}

function showError(wrap, epLabel, msg) {
    wrap.innerHTML = `<div style="text-align:center;padding:24px;background:var(--bg);border-radius:18px;box-shadow:var(--shadow-in)">
        <i class="fa-solid fa-triangle-exclamation fa-2x" style="color:orange;margin-bottom:12px"></i>
        <p style="font-weight:600">${msg}</p>
        <small style="color:var(--text-light)">${epLabel} | ${PROVIDERS[_currentProvider].name}</small></div>`;
}

/* ── Search ── */
window.searchDrama = function() {
    const q = document.getElementById('movie-search')?.value?.trim();
    if (!q) return;
    const container = document.getElementById('movie-container');
    container.innerHTML = `<div class="loading-state" style="grid-column:1/-1;padding:60px 0">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i><span>Mencari "${q}"...</span></div>`;

    apiFetch(_currentProvider, 'search', { q, query: q, keyword: q, s: q })
        .then(data => {
            const list = extractList(data).map(normalizeDrama);
            renderGrid(list, container);
        })
        .catch(() => {
            container.innerHTML = `<p style="color:red;grid-column:1/-1;text-align:center;padding:40px">Pencarian gagal.</p>`;
        });
};

/* ── Close modal ── */
window.closeDramaModal = function(e) {
    if (e && e.target !== document.getElementById('drama-modal')) return;
    const modal = document.getElementById('drama-modal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
    const video = document.getElementById('cinema-video');
    if (video) { video.pause(); video.src = ''; }
    _currentDrama = null;
    _allEpisodes  = [];
};
