<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Box – Streaming Hub | robrion.my.id</title>
    <meta name="description" content="Nonton drama & anime online dari 9 provider – DramaBox, PineDrama, ReelShort, ShortMax, GoodShort, NetShort, FreeReels, DramaNova & Anime.">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/assets/js/theme.js"></script>
    <style>
    /* ── PROVIDER BAR ── */
    .prov-scroll {
        display: flex; gap: 8px; overflow-x: auto; padding: 4px 0 8px;
        scrollbar-width: none; -webkit-overflow-scrolling: touch;
    }
    .prov-scroll::-webkit-scrollbar { display: none; }
    .prov-btn {
        display: flex; align-items: center; gap: 7px; padding: 9px 16px;
        border-radius: 14px; font-size: .8rem; font-weight: 600; white-space: nowrap;
        background: var(--bg-card); box-shadow: var(--shadow-sm); color: var(--text-sec);
        transition: all .2s; flex-shrink: 0; border: 1px solid var(--glass-border);
    }
    .prov-btn:hover { transform: translateY(-1px); color: var(--text); }
    .prov-btn.active { box-shadow: var(--shadow-active); color: #fff; }
    .prov-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ── CATEGORY TABS ── */
    .cat-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
    .movie-tab {
        padding: 7px 16px; border-radius: 12px; font-size: .78rem; font-weight: 600;
        background: var(--bg-card); box-shadow: var(--shadow-sm); color: var(--text-muted);
        transition: all .2s; border: 1px solid var(--glass-border);
    }
    .movie-tab:hover { color: var(--text); }
    .movie-tab.active { box-shadow: var(--shadow-active); color: var(--accent); }

    /* ── SEARCH ── */
    .search-row { display: flex; gap: 10px; align-items: center; margin-bottom: 14px; }
    .search-input-wrap { flex: 1; position: relative; }
    .search-input-wrap i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--text-muted); font-size: .82rem;
    }
    .search-input {
        width: 100%; padding: 10px 16px 10px 38px; border: none; border-radius: 14px;
        background: var(--bg); box-shadow: var(--shadow-in); color: var(--text);
        font-family: inherit; font-size: .85rem; outline: none;
    }
    .search-input::placeholder { color: var(--text-muted); }

    /* ── MOVIE GRID ── */
    .movie-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
    }
    .movie-card {
        background: var(--bg-card); border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm); overflow: hidden; cursor: pointer;
        transition: all .25s; border: 1px solid var(--glass-border);
    }
    .movie-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-out); }
    .movie-thumb {
        width: 100%; aspect-ratio: 3/4; background-size: cover;
        background-position: center; background-color: var(--bg);
        position: relative;
    }
    .movie-thumb-eps {
        position: absolute; bottom: 6px; right: 6px;
        background: rgba(0,0,0,0.7); color: #fff; font-size: .65rem;
        padding: 2px 8px; border-radius: 8px; font-weight: 600;
    }
    .movie-corner {
        position: absolute; top: 8px; left: 8px;
        color: #fff; font-size: .6rem; font-weight: 700;
        padding: 3px 8px; border-radius: 6px;
    }
    .movie-card h3 {
        font-size: .8rem; font-weight: 600; padding: 10px 10px 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .movie-tags {
        font-size: .65rem; color: var(--text-muted); padding: 0 10px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .movie-playcount {
        font-size: .65rem; color: var(--text-muted); padding: 4px 10px 10px;
        display: flex; align-items: center; gap: 4px;
    }

    /* ── MODAL ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000;
        display: flex; align-items: center; justify-content: center;
        padding: 20px; backdrop-filter: blur(4px);
    }
    .drama-modal-content {
        background: var(--bg-card); border-radius: var(--radius);
        max-width: 800px; width: 100%; max-height: 90vh; overflow-y: auto;
        padding: 28px; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        border: 1px solid var(--glass-border);
    }
    .modal-close {
        position: absolute; top: 14px; right: 14px; width: 36px; height: 36px;
        border-radius: 50%; background: var(--bg); box-shadow: var(--shadow-sm);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-sec); font-size: .9rem; z-index: 10; transition: all .2s;
    }
    .modal-close:hover { box-shadow: var(--shadow-active); color: var(--danger); }

    /* ── Detail Header ── */
    .drama-detail-header { display: flex; gap: 20px; margin-bottom: 20px; }
    .drama-detail-cover {
        width: 160px; height: 220px; object-fit: cover; border-radius: 16px;
        box-shadow: var(--shadow-out); flex-shrink: 0;
    }
    .drama-detail-info { flex: 1; min-width: 0; }
    .drama-detail-info h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 8px; }
    .drama-intro { font-size: .82rem; color: var(--text-sec); line-height: 1.6; margin-bottom: 10px; }
    .drama-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
    .drama-provider-badge {
        padding: 3px 10px; border-radius: 8px; font-size: .7rem; font-weight: 600; color: #fff;
    }
    .drama-tags-wrap { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
    .drama-tag {
        padding: 3px 10px; border-radius: 8px; font-size: .68rem;
        background: var(--bg); box-shadow: var(--shadow-sm); color: var(--text-muted);
    }
    .drama-meta { font-size: .78rem; color: var(--text-sec); display: flex; gap: 14px; }

    /* ── Episodes ── */
    .ep-section-title {
        font-size: .95rem; font-weight: 700; margin-bottom: 12px;
        display: flex; align-items: center; gap: 8px;
    }
    .episode-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .ep-btn {
        padding: 8px 16px; border-radius: 12px; font-size: .78rem; font-weight: 600;
        background: var(--bg-card); box-shadow: var(--shadow-sm); color: var(--text-sec);
        transition: all .2s; border: 1px solid var(--glass-border);
    }
    .ep-btn:hover { box-shadow: var(--shadow-active); color: var(--accent); }
    .ep-paid { opacity: .6; }

    /* ── Cinema Player ── */
    .cinema-player-wrap { margin-bottom: 20px; }
    .cinema-video {
        width: 100%; border-radius: 16px; background: #000;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }
    .cinema-video-info {
        display: flex; align-items: center; gap: 10px; padding: 8px 4px; font-size: .82rem;
    }
    .cinema-provider {
        padding: 3px 10px; border-radius: 8px; font-size: .7rem; font-weight: 600; color: #fff;
    }

    /* ── Quality Picker ── */
    .quality-grid { display: flex; gap: 10px; flex-wrap: wrap; }
    .quality-btn {
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        padding: 14px 22px; border-radius: var(--radius-sm); font-weight: 600; font-size: .9rem;
        background: var(--bg-card); box-shadow: var(--shadow-sm); color: var(--text-sec);
        transition: all .2s; position: relative; border: 1px solid var(--glass-border);
    }
    .quality-btn:hover { box-shadow: var(--shadow-active); }

    @media(max-width: 600px) {
        .movie-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; }
        .drama-detail-header { flex-direction: column; align-items: center; text-align: center; }
        .drama-detail-cover { width: 120px; height: 170px; }
        .drama-modal-content { padding: 18px; }
    }
    </style>
</head>
<body>
<?php $page = 'movies'; ?>

<!-- ── TOP BAR ── -->
<header class="top-bar">
    <div class="top-bar-left">
        <a href="/" class="top-bar-logo">ROBRION</a>
        <nav class="top-bar-nav">
            <a href="/"><i class="fa-solid fa-house"></i> Home</a>
            <a href="/music"><i class="fa-solid fa-headphones-simple"></i> Music Hub</a>
            <a href="/movies" class="active"><i class="fa-solid fa-clapperboard"></i> Cinema Box</a>
        </nav>
    </div>
    <div class="top-bar-right">
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fa-solid fa-moon"></i><i class="fa-solid fa-sun"></i>
        </button>
    </div>
</header>

<div class="app-shell">

    <!-- ══ PROVIDER BAR ══ -->
    <div class="neu-card animate-in" style="padding:16px 20px">
        <div class="prov-scroll" id="provider-bar">
            <button class="prov-btn active" data-provider="dramabox" onclick="switchProvider('dramabox')">
                <span class="prov-dot" style="background:#FF6B6B"></span><i class="fa-solid fa-tv"></i> DramaBox
            </button>
            <button class="prov-btn" data-provider="pinedrama" onclick="switchProvider('pinedrama')">
                <span class="prov-dot" style="background:#4ECDC4"></span><i class="fa-solid fa-leaf"></i> PineDrama
            </button>
            <button class="prov-btn" data-provider="reelshort" onclick="switchProvider('reelshort')">
                <span class="prov-dot" style="background:#FF8C42"></span><i class="fa-solid fa-film"></i> ReelShort
            </button>
            <button class="prov-btn" data-provider="shortmax" onclick="switchProvider('shortmax')">
                <span class="prov-dot" style="background:#FFD93D"></span><i class="fa-solid fa-bolt"></i> ShortMax
            </button>
            <button class="prov-btn" data-provider="goodshort" onclick="switchProvider('goodshort')">
                <span class="prov-dot" style="background:#6BCB77"></span><i class="fa-solid fa-thumbs-up"></i> GoodShort
            </button>
            <button class="prov-btn" data-provider="netshort" onclick="switchProvider('netshort')">
                <span class="prov-dot" style="background:#4D96FF"></span><i class="fa-solid fa-globe"></i> NetShort
            </button>
            <button class="prov-btn" data-provider="freereels" onclick="switchProvider('freereels')">
                <span class="prov-dot" style="background:#9B59B6"></span><i class="fa-solid fa-video"></i> FreeReels
            </button>
            <button class="prov-btn" data-provider="dramanova" onclick="switchProvider('dramanova')">
                <span class="prov-dot" style="background:#E74C3C"></span><i class="fa-solid fa-star"></i> DramaNova
            </button>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ══ -->
    <div class="neu-card animate-in">
        <div class="section-head">
            <div class="section-title">
                <i class="fa-solid fa-fire"></i>
                <span class="drama-provider-badge" id="active-prov-badge" style="background:#FF6B6B">DramaBox</span>
                Streaming
            </div>
        </div>

        <!-- Search -->
        <div class="search-row">
            <div class="search-input-wrap">
                <i class="fa-solid fa-search"></i>
                <input type="text" class="search-input" id="movie-search" placeholder="Cari drama / anime..."
                       onkeydown="if(event.key==='Enter')searchDrama()">
            </div>
            <button class="neu-btn-accent neu-btn" onclick="searchDrama()"><i class="fa-solid fa-search"></i> Cari</button>
        </div>

        <!-- Category tabs -->
        <div class="cat-tabs" id="category-tabs"></div>

        <!-- Grid -->
        <div class="movie-grid" id="movie-container">
            <div class="loading-state" style="grid-column:1/-1;padding:60px 0">
                <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
                <span>Memuat drama...</span>
            </div>
        </div>
    </div>

</div>

<!-- ── BOTTOM NAV ── -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="/" class="nav-item"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a href="https://n8n.robrion.my.id" target="_blank" class="nav-item"><i class="fa-solid fa-flask"></i><span>AI Lab</span></a>
        <a href="/music" class="nav-item"><i class="fa-solid fa-headphones-simple"></i><span>Music</span></a>
        <a href="/movies" class="nav-item active"><i class="fa-solid fa-clapperboard"></i><span>Cinema</span></a>
        <button class="nav-item" onclick="toggleTheme()"><i class="fa-solid fa-circle-half-stroke"></i><span>Theme</span></button>
    </div>
</nav>

<!-- DRAMA DETAIL MODAL -->
<div class="modal-overlay" id="drama-modal" style="display:none" onclick="closeDramaModal(event)">
    <div class="drama-modal-content" id="drama-modal-content">
        <button class="modal-close" onclick="document.getElementById('drama-modal').style.display='none';document.body.style.overflow=''">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div id="drama-modal-body"></div>
    </div>
</div>

<script src="/assets/js/movies.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    switchProvider('dramabox');
    const origSwitch = window.switchProvider;
    window.switchProvider = function(key) {
        origSwitch(key);
        const prov = PROVIDERS[key];
        const badge = document.getElementById('active-prov-badge');
        if (badge && prov) { badge.style.background = prov.color; badge.textContent = prov.name; }
    };
    window.switchProvider('dramabox');
});
</script>
</body>
</html>
