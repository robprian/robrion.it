<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Page Not Found | robrion.my.id</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/assets/js/theme.js"></script>
    <style>
    .error-page {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; min-height: 70vh; text-align: center; gap: 20px;
    }
    .error-code {
        font-size: 6rem; font-weight: 800; line-height: 1;
        background: var(--gradient-brand);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .error-title { font-size: 1.3rem; font-weight: 700; }
    .error-desc { font-size: .88rem; color: var(--text-sec); max-width: 400px; }
    </style>
</head>
<body>
<header class="top-bar">
    <div class="top-bar-left">
        <a href="/" class="top-bar-logo">ROBRION</a>
        <nav class="top-bar-nav">
            <a href="/"><i class="fa-solid fa-house"></i> Home</a>
            <a href="/music"><i class="fa-solid fa-headphones-simple"></i> Music</a>
            <a href="/movies"><i class="fa-solid fa-clapperboard"></i> Cinema</a>
        </nav>
    </div>
    <div class="top-bar-right">
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fa-solid fa-moon"></i><i class="fa-solid fa-sun"></i>
        </button>
    </div>
</header>
<div class="app-shell">
    <div class="neu-card error-page animate-in">
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">Maaf, halaman yang kamu cari tidak ada atau sudah dipindahkan.</p>
        <a href="/" class="neu-btn-accent neu-btn" style="margin-top:10px">
            <i class="fa-solid fa-house"></i> Kembali ke Home
        </a>
    </div>
</div>
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="/" class="nav-item"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a href="/music" class="nav-item"><i class="fa-solid fa-headphones-simple"></i><span>Music</span></a>
        <a href="/movies" class="nav-item"><i class="fa-solid fa-clapperboard"></i><span>Cinema</span></a>
        <button class="nav-item" onclick="toggleTheme()"><i class="fa-solid fa-circle-half-stroke"></i><span>Theme</span></button>
    </div>
</nav>
</body>
</html>
