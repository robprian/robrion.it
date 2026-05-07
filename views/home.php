<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robby Aprianto – AI Agent & DevOps Engineer</title>
    <meta name="description" content="Portfolio Robby Aprianto: AI Agent Developer, DevOps Engineer, dan Music Producer berbasis di Indonesia.">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/assets/js/theme.js"></script>
    <style>
    /* ── HOME SPECIFIC ── */
    .hero-section {
        display: flex; flex-direction: column; align-items: center;
        text-align: center; gap: 20px; padding: 40px 28px;
    }
    .greeting-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 20px; border-radius: 24px; font-size: .82rem; font-style: italic;
        color: var(--text-muted); background: var(--bg); box-shadow: var(--shadow-sm);
    }
    .hero-name { font-size: 2.2rem; font-weight: 800; line-height: 1.15; }
    .hero-subtitle {
        font-size: .88rem; color: var(--text-sec); line-height: 1.7; max-width: 520px;
    }
    .hero-tags { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }

    /* ── Menu Grid ── */
    .menu-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; width: 100%;
    }
    .menu-card {
        background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow-out);
        padding: 24px 16px; display: flex; flex-direction: column; align-items: center; gap: 10px;
        transition: all .25s; border: 1px solid var(--glass-border); text-decoration: none; color: var(--text);
    }
    .menu-card:hover { box-shadow: var(--shadow-active); transform: translateY(2px); }
    .menu-icon {
        width: 52px; height: 52px; border-radius: 50%;
        background: var(--bg); box-shadow: var(--shadow-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; color: var(--accent); transition: all .25s;
    }
    .menu-card:hover .menu-icon { box-shadow: var(--shadow-active); color: var(--accent2); }
    .menu-label { font-weight: 600; font-size: .88rem; }
    .menu-desc { font-size: .7rem; color: var(--text-muted); text-align: center; }

    /* ── Repo Cards ── */
    .repo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; }
    .repo-card {
        background: var(--bg-card); border-radius: var(--radius-sm); box-shadow: var(--shadow-sm);
        padding: 18px 16px; display: flex; flex-direction: column; gap: 8px;
        transition: all .25s; border: 1px solid var(--glass-border); color: var(--text);
    }
    .repo-card:hover { box-shadow: var(--shadow-out); transform: translateY(-2px); }
    .repo-name {
        font-weight: 600; font-size: .88rem;
        display: flex; align-items: center; gap: 7px;
    }
    .repo-name i { color: var(--accent); font-size: .85rem; }
    .repo-desc {
        font-size: .75rem; color: var(--text-sec); line-height: 1.5;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .repo-meta { display: flex; gap: 12px; font-size: .72rem; color: var(--text-muted); flex-wrap: wrap; }
    .repo-lang { display: flex; align-items: center; gap: 4px; }
    .lang-dot { width: 8px; height: 8px; border-radius: 50%; }

    /* ── Skills Radar (simple bars) ── */
    .skill-bar-wrap { display: flex; flex-direction: column; gap: 10px; }
    .skill-row { display: flex; align-items: center; gap: 10px; }
    .skill-name { width: 100px; font-size: .78rem; font-weight: 600; color: var(--text-sec); }
    .skill-track {
        flex: 1; height: 8px; background: var(--bg); border-radius: 10px;
        box-shadow: var(--shadow-in); overflow: hidden;
    }
    .skill-fill {
        height: 100%; border-radius: 10px;
        background: var(--gradient-brand);
        transition: width .8s ease;
    }
    .skill-level { font-size: .68rem; color: var(--text-muted); width: 50px; text-align: right; }

    /* ── Footer ── */
    .footer-bar {
        text-align: center; color: var(--text-muted); font-size: .72rem;
        padding: 16px; margin-top: 8px;
    }

    @media(max-width: 768px) {
        .hero-section { padding: 28px 16px; }
        .hero-name { font-size: 1.7rem; }
        .menu-grid { grid-template-columns: 1fr 1fr; }
        .menu-grid .menu-card:last-child { grid-column: 1/-1; }
        .repo-grid { grid-template-columns: 1fr; }
    }
    @media(max-width: 480px) {
        .hero-name { font-size: 1.45rem; }
        .menu-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
        .menu-card { padding: 18px 12px; }
    }
    </style>
</head>
<body>
<?php
$h = (int) date('H');
$sapa = $h < 12 ? '☀️ Selamat Pagi' : ($h < 15 ? '🌤 Selamat Siang' : ($h < 18 ? '🌅 Selamat Sore' : '🌙 Selamat Malam'));
$page = 'home';
?>

<!-- ── TOP BAR (Desktop) ── -->
<header class="top-bar">
    <div class="top-bar-left">
        <a href="/" class="top-bar-logo">ROBRION</a>
        <nav class="top-bar-nav">
            <a href="/" class="active"><i class="fa-solid fa-house"></i> Home</a>
            <a href="/music"><i class="fa-solid fa-headphones-simple"></i> Music Hub</a>
            <a href="/movies"><i class="fa-solid fa-clapperboard"></i> Cinema Box</a>
            <a href="https://n8n.robrion.my.id" target="_blank"><i class="fa-solid fa-diagram-project"></i> AI Lab</a>
        </nav>
    </div>
    <div class="top-bar-right">
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fa-solid fa-moon"></i><i class="fa-solid fa-sun"></i>
        </button>
    </div>
</header>

<div class="app-shell">

    <!-- ══ HERO ══ -->
    <div class="neu-card hero-section animate-in">
        <div class="greeting-chip"><?= $sapa ?> – Welcome to my Portfolio 👋</div>

        <div class="avatar-ring">
            <div class="avatar-inner">
                <img src="/assets/img/foto.jpg" alt="Robby Aprianto"
                     onerror="this.style.display='none';this.parentNode.innerHTML+='<span style=&quot;font-size:2.6rem;display:flex;align-items:center;justify-content:center;width:100%;height:100%&quot;>🎧</span>'">
            </div>
        </div>

        <div>
            <h1 class="hero-name">Robby <span class="text-gradient">Aprianto</span></h1>
        </div>
        <p class="hero-subtitle">AI Agent Developer · DevOps Engineer · Music Producer<br>
            Membangun otomasi cerdas, mengelola infrastruktur cloud, dan menciptakan harmoni digital dari Bogor, Indonesia.</p>

        <div class="hero-tags">
            <span class="chip"><i class="fa-brands fa-github"></i>@robprian</span>
            <span class="chip"><i class="fa-solid fa-server"></i>AlmaLinux VPS</span>
            <span class="chip"><i class="fa-solid fa-robot"></i>n8n Automation</span>
            <span class="chip"><i class="fa-solid fa-music"></i>Music Producer</span>
            <span class="chip"><i class="fa-brands fa-docker"></i>Docker · CI/CD</span>
        </div>

        <!-- Stats -->
        <div class="stat-grid" style="width:100%;max-width:500px">
            <div class="stat-item animate-in">
                <div class="stat-value" id="st-repos">6+</div>
                <div class="stat-label">Public Repos</div>
            </div>
            <div class="stat-item animate-in">
                <div class="stat-value">5+</div>
                <div class="stat-label">Years Exp.</div>
            </div>
            <div class="stat-item animate-in">
                <div class="stat-value">24/7</div>
                <div class="stat-label">Server Uptime</div>
            </div>
        </div>
    </div>

    <!-- ══ DASHBOARD GRID ══ -->
    <div class="dashboard-grid">

        <!-- ── MENU CARDS ── -->
        <div class="neu-card animate-in">
            <div class="section-head">
                <div class="section-title"><i class="fa-solid fa-grid-2"></i> Quick Access</div>
            </div>
            <div class="menu-grid">
                <a href="https://n8n.robrion.my.id" class="menu-card" target="_blank">
                    <div class="menu-icon"><i class="fa-solid fa-diagram-project"></i></div>
                    <div class="menu-label">AI Lab</div>
                    <div class="menu-desc">n8n Pipelines</div>
                </a>
                <a href="/music" class="menu-card">
                    <div class="menu-icon" style="color:var(--accent2)"><i class="fa-solid fa-headphones-simple"></i></div>
                    <div class="menu-label">Music Hub</div>
                    <div class="menu-desc">Suno AI Player</div>
                </a>
                <a href="/movies" class="menu-card">
                    <div class="menu-icon" style="color:var(--accent3)"><i class="fa-solid fa-clapperboard"></i></div>
                    <div class="menu-label">Cinema Box</div>
                    <div class="menu-desc">9 Providers</div>
                </a>
                <a href="https://github.com/robprian" class="menu-card" target="_blank">
                    <div class="menu-icon"><i class="fa-brands fa-github"></i></div>
                    <div class="menu-label">GitHub</div>
                    <div class="menu-desc">Open Source</div>
                </a>
                <a href="#" class="menu-card">
                    <div class="menu-icon" style="color:var(--success)"><i class="fa-brands fa-docker"></i></div>
                    <div class="menu-label">DevOps</div>
                    <div class="menu-desc">CI/CD Pipeline</div>
                </a>
                <a href="mailto:robprian@gmail.com" class="menu-card">
                    <div class="menu-icon" style="color:var(--warning)"><i class="fa-solid fa-envelope"></i></div>
                    <div class="menu-label">Contact</div>
                    <div class="menu-desc">Collaborate</div>
                </a>
            </div>
        </div>

        <!-- ── SKILLS ── -->
        <div class="neu-card animate-in">
            <div class="section-head">
                <div class="section-title"><i class="fa-solid fa-chart-radar"></i> Skills Depth</div>
                <span class="live-badge">Live Pipeline</span>
            </div>
            <div class="skill-bar-wrap">
                <div class="skill-row"><span class="skill-name"><i class="fa-brands fa-python"></i> Python</span><div class="skill-track"><div class="skill-fill" style="width:90%"></div></div><span class="skill-level">Expert</span></div>
                <div class="skill-row"><span class="skill-name"><i class="fa-brands fa-aws"></i> AWS</span><div class="skill-track"><div class="skill-fill" style="width:75%"></div></div><span class="skill-level">Advanced</span></div>
                <div class="skill-row"><span class="skill-name"><i class="fa-brands fa-js"></i> JavaScript</span><div class="skill-track"><div class="skill-fill" style="width:82%"></div></div><span class="skill-level">Advanced</span></div>
                <div class="skill-row"><span class="skill-name"><i class="fa-solid fa-database"></i> SQL/NoSQL</span><div class="skill-track"><div class="skill-fill" style="width:85%"></div></div><span class="skill-level">Expert</span></div>
                <div class="skill-row"><span class="skill-name"><i class="fa-brands fa-docker"></i> Docker</span><div class="skill-track"><div class="skill-fill" style="width:88%"></div></div><span class="skill-level">Expert</span></div>
                <div class="skill-row"><span class="skill-name"><i class="fa-solid fa-shield"></i> Security</span><div class="skill-track"><div class="skill-fill" style="width:70%"></div></div><span class="skill-level">Proficient</span></div>
                <div class="skill-row"><span class="skill-name"><i class="fa-solid fa-brain"></i> AI/ML</span><div class="skill-track"><div class="skill-fill" style="width:78%"></div></div><span class="skill-level">Advanced</span></div>
            </div>
        </div>

        <!-- ── GITHUB REPOS ── -->
        <div class="neu-card full-width animate-in">
            <div class="section-head">
                <div class="section-title"><i class="fa-brands fa-github"></i> Recent Repositories</div>
                <a href="https://github.com/robprian" class="neu-btn" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View All
                </a>
            </div>
            <div class="repo-grid" id="repo-grid">
                <div class="loading-state"><i class="fa-solid fa-circle-notch fa-spin"></i> Loading repos...</div>
            </div>
        </div>

    </div><!-- /dashboard-grid -->

    <div class="footer-bar">&copy; <?= date('Y') ?> robrion.my.id – Managed on AlmaLinux VPS</div>
</div>

<!-- ── BOTTOM NAV ── -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="/" class="nav-item active"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a href="https://n8n.robrion.my.id" target="_blank" class="nav-item"><i class="fa-solid fa-flask"></i><span>AI Lab</span></a>
        <a href="/music" class="nav-item"><i class="fa-solid fa-headphones-simple"></i><span>Music</span></a>
        <a href="/movies" class="nav-item"><i class="fa-solid fa-clapperboard"></i><span>Cinema</span></a>
        <button class="nav-item" onclick="toggleTheme()"><i class="fa-solid fa-circle-half-stroke"></i><span>Theme</span></button>
    </div>
</nav>

<script>
const LANG_COLORS = {
    PHP:'#4f5d95', JavaScript:'#f1e05a', Python:'#3572A5',
    Shell:'#89e051', HTML:'#e34c26', CSS:'#563d7c',
    TypeScript:'#2b7489', Vue:'#41b883'
};
fetch('https://api.github.com/users/robprian/repos?sort=updated&per_page=6&type=public')
    .then(r => r.json())
    .then(repos => {
        const grid = document.getElementById('repo-grid');
        grid.innerHTML = '';
        repos.forEach(r => {
            const c = LANG_COLORS[r.language] || '#718096';
            const card = document.createElement('a');
            card.href = r.html_url; card.target = '_blank';
            card.className = 'repo-card animate-in';
            card.innerHTML = `
                <div class="repo-name"><i class="fa-solid fa-code-branch"></i>${r.name}</div>
                <div class="repo-desc">${r.description||'No description provided.'}</div>
                <div class="repo-meta">
                    ${r.language?`<span class="repo-lang"><span class="lang-dot" style="background:${c}"></span>${r.language}</span>`:''}
                    <span><i class="fa-solid fa-star" style="color:#f59e0b"></i> ${r.stargazers_count}</span>
                    <span><i class="fa-solid fa-code-fork" style="color:var(--accent)"></i> ${r.forks_count}</span>
                </div>`;
            grid.appendChild(card);
        });
        document.getElementById('st-repos').innerText = repos.length + '+';
    })
    .catch(() => {
        document.getElementById('repo-grid').innerHTML =
            '<p class="loading-state">Failed to load repos. Visit <a href="https://github.com/robprian" target="_blank" style="color:var(--accent)">GitHub</a> directly.</p>';
    });
</script>
</body>
</html>