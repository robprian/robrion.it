// --- SPA NAVIGATION LOGIC ---
document.addEventListener('DOMContentLoaded', () => {
    
    // Initialize Music Player by default
    if(typeof initMusic === 'function') initMusic();

    window.showMusic = function(e) {
        if(e) e.preventDefault();
        
        window.history.pushState({}, '', '/music');

        document.getElementById('view-playlist').style.display = 'flex';
        document.getElementById('view-movies').style.display = 'none';
        
        document.getElementById('main-grid').style.gridTemplateColumns = '1fr 400px';
        if(window.innerWidth <= 900) {
            document.getElementById('main-grid').style.gridTemplateColumns = '1fr';
        }

        document.getElementById('view-player').classList.remove('minimized');

        document.getElementById('head-nav-music').classList.add('active');
        document.getElementById('head-nav-movie').classList.remove('active');
        document.getElementById('nav-music').classList.add('active');
        document.getElementById('nav-movie').classList.remove('active');
    };

    window.showMovies = function(e) {
        if(e) e.preventDefault();
        
        window.history.pushState({}, '', '/movies');

        document.getElementById('view-playlist').style.display = 'none';
        document.getElementById('view-movies').style.display = 'block';
        
        document.getElementById('main-grid').style.gridTemplateColumns = '1fr';

        // Minimize Audio Player
        document.getElementById('view-player').classList.add('minimized');

        document.getElementById('head-nav-music').classList.remove('active');
        document.getElementById('head-nav-movie').classList.add('active');
        document.getElementById('nav-music').classList.remove('active');
        document.getElementById('nav-movie').classList.add('active');

        if(typeof loadMovies === 'function') loadMovies();
    };

    // Auto load tab based on URL
    const currentPath = window.location.pathname;
    if(currentPath.includes('movies')) {
        showMovies();
    }
});
