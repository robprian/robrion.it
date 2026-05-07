// --- MOVIE API LOGIC ---
let moviesLoaded = false;
const movieContainer = document.getElementById('movie-container');

window.loadMovies = function() {
    if(moviesLoaded) return;
    if(!movieContainer) return;

    movieContainer.innerHTML = '<p style="text-align: center; color: var(--text-light); width: 100%;"><i class="fa-solid fa-circle-notch fa-spin"></i> Memuat data dari API...</p>';
    
    fetch('https://api.sansekai.my.id/api/dramabox/foryou')
        .then(res => res.json())
        .then(data => {
            if(data && data.length > 0) {
                movieContainer.innerHTML = '';
                data.forEach((movie) => {
                    const card = document.createElement('div');
                    card.className = 'movie-card';
                    card.onclick = () => showMovieDetail(movie);
                    
                    card.innerHTML = `
                        <div class="thumb" style="background: url('${movie.coverWap}') center/cover; position: relative;">
                            <div style="position: absolute; bottom: 5px; right: 5px; background: rgba(0,0,0,0.7); padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; color: white;">
                                ${movie.chapterCount} Eps
                            </div>
                        </div>
                        <h3 title="${movie.bookName}">${movie.bookName}</h3>
                        <div style="font-size: 0.7rem; color: var(--text-light); margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${movie.tags ? movie.tags.slice(0, 2).join(' • ') : ''}
                        </div>
                    `;
                    movieContainer.appendChild(card);
                });
                moviesLoaded = true;
            } else {
                movieContainer.innerHTML = `<p style="text-align: center; color: var(--text-light); width: 100%;">Tidak ada data film.</p>`;
            }
        })
        .catch(err => {
            console.error("API Error:", err);
            movieContainer.innerHTML = `<p style="text-align: center; color: red; width: 100%;">Gagal memuat film dari server API.</p>`;
        });
}

window.showMovieDetail = function(movie) {
    alert("Judul: " + movie.bookName + "\n\nSistem sedang dikembangkan untuk memutar video melalui API:\n- /dramabox/allepisode\n- /dramabox/decrypt\n\nBookID: " + movie.bookId);
}

// --- VIDEO MODAL LOGIC ---
const videoModal = document.getElementById('video-modal');
const moviePlayer = document.getElementById('movie-player');
const movieTitleDisplay = document.getElementById('movie-title-display');

window.openVideo = function(url, title) {
    if(typeof pauseAudio === 'function' && window.isPlaying) pauseAudio();

    if(moviePlayer) moviePlayer.src = url;
    if(movieTitleDisplay) movieTitleDisplay.innerText = title;
    if(videoModal) videoModal.style.display = 'flex';
    if(moviePlayer) moviePlayer.play();
};

window.closeVideo = function() {
    if(moviePlayer) {
        moviePlayer.pause();
        moviePlayer.src = '';
    }
    if(videoModal) videoModal.style.display = 'none';
};
