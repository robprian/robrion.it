<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robby Aprianto – AI Agent & DevOps Engineer</title>
    <meta name="description" content="Portfolio Robby Aprianto: AI Agent Developer, DevOps Engineer, dan Music Producer berbasis di Indonesia.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg:#e0e5ec; --text:#2d3748; --muted:#718096;
            --accent:#00e5ff; --purple:#7c3aed; --pink:#ec4899;
            --so: 9px 9px 18px rgba(163,177,198,.7),-9px -9px 18px rgba(255,255,255,.85);
            --si: inset 6px 6px 12px rgba(163,177,198,.6),inset -6px -6px 12px rgba(255,255,255,.85);
            --sb: 5px 5px 10px rgba(163,177,198,.6),-5px -5px 10px rgba(255,255,255,.8);
            --sba: inset 3px 3px 7px rgba(163,177,198,.7),inset -3px -3px 7px rgba(255,255,255,.8);
        }
        *,::before,::after{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;padding:24px 16px;display:flex;flex-direction:column;align-items:center;gap:28px}

        /* ── HERO ── */
        .hero{width:100%;max-width:960px;background:var(--bg);border-radius:32px;box-shadow:var(--so);padding:50px 44px;display:flex;flex-direction:column;align-items:center;gap:28px;text-align:center}

        .greeting-chip{background:var(--bg);box-shadow:var(--sb);border-radius:30px;padding:8px 22px;font-size:.85rem;color:var(--muted);font-style:italic}

        .avatar-ring{width:110px;height:110px;border-radius:50%;box-shadow:var(--so);padding:8px;background:var(--bg);flex-shrink:0}
        .avatar-inner{width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,#1a0533,#2b4590,#00e5ff);display:flex;align-items:center;justify-content:center;font-size:2.6rem}

        h1{font-size:2.6rem;font-weight:700;line-height:1.15}
        h1 .grad{background:linear-gradient(135deg,var(--accent),var(--purple));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .subtitle{color:var(--muted);font-size:.95rem;line-height:1.7;max-width:520px}

        /* Tags */
        .tags{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}
        .tag{background:var(--bg);box-shadow:var(--sb);border-radius:20px;padding:6px 16px;font-size:.78rem;color:var(--muted);font-weight:500;display:flex;align-items:center;gap:6px}

        /* Stats row */
        .stats-row{display:flex;gap:16px;flex-wrap:wrap;justify-content:center;width:100%}
        .stat-card{flex:1;min-width:130px;background:var(--bg);border-radius:20px;box-shadow:var(--sb);padding:18px 14px;text-align:center}
        .stat-val{font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,var(--accent),var(--purple));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .stat-lbl{font-size:.72rem;color:var(--muted);margin-top:2px}

        /* Menu Grid */
        .menu-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;width:100%}
        .menu-card{background:var(--bg);border-radius:24px;box-shadow:var(--so);padding:28px 18px;display:flex;flex-direction:column;align-items:center;gap:12px;text-decoration:none;color:var(--text);transition:all .25s;border:none;font-family:inherit;cursor:pointer}
        .menu-card:hover{box-shadow:var(--sba);transform:translateY(2px)}
        .icon-w{width:58px;height:58px;border-radius:50%;background:var(--bg);box-shadow:var(--sb);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--accent);transition:all .25s}
        .menu-card:hover .icon-w{box-shadow:var(--sba);color:var(--purple)}
        .c-title{font-weight:600;font-size:.92rem;text-align:center}
        .c-desc{font-size:.72rem;color:var(--muted);text-align:center}

        /* GitHub section */
        .github-section{width:100%;max-width:960px;background:var(--bg);border-radius:28px;box-shadow:var(--so);padding:32px 36px;display:flex;flex-direction:column;gap:18px}
        .section-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
        .section-title{font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:10px}
        .view-all{background:var(--bg);box-shadow:var(--sb);border:none;border-radius:16px;padding:7px 16px;font-family:inherit;font-size:.78rem;font-weight:600;color:var(--muted);cursor:pointer;text-decoration:none;transition:all .2s;display:flex;align-items:center;gap:6px}
        .view-all:hover{box-shadow:var(--sba);color:var(--accent)}

        .repo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
        .repo-card{background:var(--bg);border-radius:18px;box-shadow:var(--sb);padding:18px 16px;display:flex;flex-direction:column;gap:8px;text-decoration:none;color:var(--text);transition:all .25s}
        .repo-card:hover{box-shadow:var(--so)}
        .repo-name{font-weight:600;font-size:.88rem;display:flex;align-items:center;gap:7px;color:var(--text)}
        .repo-name i{color:var(--accent);font-size:.85rem}
        .repo-desc{font-size:.75rem;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .repo-meta{display:flex;gap:12px;font-size:.72rem;color:var(--muted);margin-top:2px;flex-wrap:wrap}
        .repo-lang{display:flex;align-items:center;gap:4px}
        .lang-dot{width:8px;height:8px;border-radius:50%;background:var(--accent)}
        .repo-loading{text-align:center;color:var(--muted);padding:20px;font-size:.85rem}

        .footer{color:var(--muted);font-size:.75rem;padding-bottom:10px}

        @media(max-width:700px){
            .hero{padding:32px 20px;border-radius:24px}
            h1{font-size:1.9rem}
            .menu-grid{grid-template-columns:1fr 1fr}
            .menu-grid .menu-card:last-child{grid-column:1/-1}
            .stats-row .stat-card{min-width:100px}
            .github-section{padding:24px 18px}
        }
    </style>
</head>
<body>
<?php
$h = (int)date('H');
$sapa = $h<12?'☀️ Selamat Pagi':($h<15?'🌤 Selamat Siang':($h<18?'🌅 Selamat Sore':'🌙 Selamat Malam'));
?>

<!-- ── HERO ── -->
<div class="hero">
    <div class="greeting-chip"><?= $sapa ?> – Welcome to my Portfolio 👋</div>

    <div class="avatar-ring">
        <div class="avatar-inner">🎧</div>
    </div>

    <div>
        <h1>Robby <span class="grad">Aprianto</span></h1>
    </div>
    <p class="subtitle">AI Agent Developer · DevOps Engineer · Music Producer<br>
    Membangun otomasi cerdas, mengelola infrastruktur cloud, dan menciptakan harmoni digital dari Bogor, Indonesia.</p>

    <div class="tags">
        <span class="tag"><i class="fa-brands fa-github"></i>@robprian</span>
        <span class="tag"><i class="fa-solid fa-server"></i>AlmaLinux VPS</span>
        <span class="tag"><i class="fa-solid fa-robot"></i>n8n Automation</span>
        <span class="tag"><i class="fa-solid fa-music"></i>Music Producer</span>
        <span class="tag"><i class="fa-brands fa-docker"></i>Docker · CI/CD</span>
        <span class="tag"><i class="fa-solid fa-database"></i>Supabase · MySQL</span>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-val" id="st-repos">–</div>
            <div class="stat-lbl">Public Repos</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" id="st-stars">–</div>
            <div class="stat-lbl">Total Stars</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">5+</div>
            <div class="stat-lbl">Years Exp.</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">24/7</div>
            <div class="stat-lbl">Server Uptime</div>
        </div>
    </div>

    <div class="menu-grid">
        <a href="https://n8n.robrion.my.id" class="menu-card" target="_blank">
            <div class="icon-w"><i class="fa-solid fa-diagram-project"></i></div>
            <div class="c-title">Workflow Automation</div>
            <div class="c-desc">n8n AI Pipelines</div>
        </a>
        <a href="/music" class="menu-card">
            <div class="icon-w"><i class="fa-solid fa-headphones-simple"></i></div>
            <div class="c-title">Music Player</div>
            <div class="c-desc">Neumorphism Audio App</div>
        </a>
        <a href="/movies" class="menu-card">
            <div class="icon-w"><i class="fa-solid fa-clapperboard"></i></div>
            <div class="c-title">Drama Box</div>
            <div class="c-desc">DramaBox Streaming</div>
        </a>
        <a href="https://github.com/robprian" class="menu-card" target="_blank">
            <div class="icon-w"><i class="fa-brands fa-github"></i></div>
            <div class="c-title">GitHub</div>
            <div class="c-desc">Open Source Projects</div>
        </a>
        <a href="#" class="menu-card">
            <div class="icon-w"><i class="fa-brands fa-docker"></i></div>
            <div class="c-title">DevOps</div>
            <div class="c-desc">CI/CD & Containers</div>
        </a>
        <a href="mailto:robby@robrion.my.id" class="menu-card">
            <div class="icon-w"><i class="fa-solid fa-envelope"></i></div>
            <div class="c-title">Contact</div>
            <div class="c-desc">Let's collaborate</div>
        </a>
    </div>
</div>

<!-- ── GITHUB REPOS ── -->
<div class="github-section">
    <div class="section-header">
        <div class="section-title">
            <i class="fa-brands fa-github" style="color:var(--accent)"></i>
            Recent Repositories
        </div>
        <a href="https://github.com/robprian" class="view-all" target="_blank">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> View all on GitHub
        </a>
    </div>
    <div class="repo-grid" id="repo-grid">
        <div class="repo-loading"><i class="fa-solid fa-circle-notch fa-spin"></i> Memuat repositories...</div>
    </div>
</div>

<div class="footer">&copy; <?= date('Y') ?> robrion.my.id – Managed on AlmaLinux VPS</div>

<script>
// Fetch GitHub repos
const LANG_COLORS = {
    PHP:'#4f5d95', JavaScript:'#f1e05a', Python:'#3572A5',
    Shell:'#89e051', HTML:'#e34c26', CSS:'#563d7c',
    TypeScript:'#2b7489', Vue:'#41b883'
};
fetch('https://api.github.com/users/robprian/repos?sort=updated&per_page=6&type=public')
    .then(r => r.json())
    .then(repos => {
        const grid = document.getElementById('repo-grid');
        let totalStars = 0;
        grid.innerHTML = '';
        repos.forEach(r => {
            totalStars += r.stargazers_count || 0;
            const langColor = LANG_COLORS[r.language] || '#718096';
            const card = document.createElement('a');
            card.href = r.html_url;
            card.target = '_blank';
            card.className = 'repo-card';
            card.innerHTML = `
                <div class="repo-name"><i class="fa-solid fa-code-branch"></i>${r.name}</div>
                <div class="repo-desc">${r.description || 'No description provided.'}</div>
                <div class="repo-meta">
                    ${r.language ? `<span class="repo-lang"><span class="lang-dot" style="background:${langColor}"></span>${r.language}</span>` : ''}
                    <span><i class="fa-solid fa-star" style="color:#f59e0b"></i> ${r.stargazers_count}</span>
                    <span><i class="fa-solid fa-code-fork" style="color:var(--accent)"></i> ${r.forks_count}</span>
                </div>`;
            grid.appendChild(card);
        });
        document.getElementById('st-repos').innerText = repos.length + '+';
        document.getElementById('st-stars').innerText = totalStars;
    })
    .catch(() => {
        document.getElementById('repo-grid').innerHTML =
            '<p class="repo-loading">Gagal memuat repositori. Kunjungi <a href="https://github.com/robprian" target="_blank" style="color:var(--accent)">GitHub</a> langsung.</p>';
    });
</script>
</body>
</html>
