// app.js – helper: show mini player bar when state exists
document.addEventListener('DOMContentLoaded', () => {
    const miniBar = document.getElementById('mini-player');
    if (miniBar && miniBar.style.display !== 'none') {
        document.body.classList.add('has-mini-player');
    }
});
