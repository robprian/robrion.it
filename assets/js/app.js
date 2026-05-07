// ============================================================
// app.js – SPA Navigation + URL History
// ============================================================
document.addEventListener('DOMContentLoaded', () => {

    // Init music player
    if (typeof initMusic === 'function') initMusic();

    /* ---- View switcher ---- */
    window.showView = function (view, e) {
        if (e) e.preventDefault();

        const isMobile = window.innerWidth <= 900;
        const playerEl    = document.getElementById('view-player');
        const playlistEl  = document.getElementById('view-playlist');
        const moviesEl    = document.getElementById('view-movies');
        const mainGrid    = document.getElementById('main-grid');

        if (view === 'music') {
            // Restore music view
            if (playerEl)   { playerEl.style.display = 'flex'; playerEl.classList.remove('minimized'); }
            if (playlistEl) playlistEl.style.display = 'flex';
            if (moviesEl)   moviesEl.style.display   = 'none';
            if (mainGrid)   mainGrid.style.gridTemplateColumns = isMobile ? '1fr' : '1fr 380px';

            setNavActive('music');
            window.history.pushState({}, '', '/music');

        } else if (view === 'movies') {
            // Minimize music player, show movies
            if (playerEl)   playerEl.classList.add('minimized');
            if (playlistEl) playlistEl.style.display = 'none';
            if (moviesEl)   moviesEl.style.display   = 'flex';
            if (mainGrid)   mainGrid.style.gridTemplateColumns = '1fr';

            setNavActive('movies');
            window.history.pushState({}, '', '/movies');

            // Load drama on first visit
            if (typeof loadMovies === 'function') loadMovies();
        }
    };

    function setNavActive (view) {
        // Desktop nav
        document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
        const desktopTarget = document.getElementById(`head-nav-${view === 'music' ? 'music' : 'movie'}`);
        if (desktopTarget) desktopTarget.classList.add('active');

        // Mobile nav
        document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
        const mobileTarget = document.getElementById(`nav-${view === 'music' ? 'music' : 'movie'}`);
        if (mobileTarget) mobileTarget.classList.add('active');
    }

    /* ---- Auto-activate view based on URL ---- */
    const path = window.location.pathname.replace(/^\//, '').split('/')[0];
    if (path === 'movies') {
        showView('movies');
    } else {
        // Default: music view
        showView('music');
    }

    /* ---- Handle browser back/forward ---- */
    window.addEventListener('popstate', () => {
        const p = window.location.pathname.replace(/^\//, '').split('/')[0];
        showView(p === 'movies' ? 'movies' : 'music');
    });

    /* ---- Resize handler ---- */
    window.addEventListener('resize', () => {
        const view = window.location.pathname.includes('movies') ? 'movies' : 'music';
        const mainGrid = document.getElementById('main-grid');
        if (mainGrid && view === 'music') {
            mainGrid.style.gridTemplateColumns = window.innerWidth <= 900 ? '1fr' : '1fr 380px';
        }
    });

    /* ---- Minimized player click -> restore music ---- */
    const playerEl = document.getElementById('view-player');
    if (playerEl) {
        playerEl.addEventListener('click', function (e) {
            if (this.classList.contains('minimized')) {
                // Only restore if click is NOT on play button
                if (!e.target.closest('#btn-play')) {
                    showView('music');
                }
            }
        });
    }
});
