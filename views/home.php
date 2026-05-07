<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robby Aprianto | AI Agent & DevOps</title>
    <meta name="description" content="Portfolio Robby Aprianto - AI Agent Developer, DevOps Engineer, dan Music Producer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #e0e5ec;
            --text: #2d3748;
            --text-light: #718096;
            --accent: #00e5ff;
            --accent2: #7c3aed;
            --shadow-out: 9px 9px 18px rgba(163,177,198,0.7), -9px -9px 18px rgba(255,255,255,0.8);
            --shadow-in: inset 6px 6px 12px rgba(163,177,198,0.6), inset -6px -6px 12px rgba(255,255,255,0.8);
            --shadow-btn: 5px 5px 10px rgba(163,177,198,0.6), -5px -5px 10px rgba(255,255,255,0.7);
            --shadow-btn-active: inset 3px 3px 7px rgba(163,177,198,0.7), inset -3px -3px 7px rgba(255,255,255,0.8);
            --radius: 24px;
            --radius-sm: 14px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .hero {
            width: 100%;
            max-width: 900px;
            background: var(--bg);
            border-radius: 30px;
            box-shadow: var(--shadow-out);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .greeting-chip {
            background: var(--bg);
            border-radius: 30px;
            box-shadow: var(--shadow-btn);
            padding: 8px 24px;
            font-size: 0.9rem;
            color: var(--text-light);
            font-style: italic;
        }

        .avatar-wrap {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            box-shadow: var(--shadow-out);
            padding: 8px;
            background: var(--bg);
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #2b1055, #7597de, #00e5ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text);
            text-align: center;
        }

        h1 span {
            background: linear-gradient(135deg, #00e5ff, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: var(--text-light);
            font-size: 1rem;
            text-align: center;
            max-width: 500px;
            line-height: 1.7;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .tag {
            background: var(--bg);
            border-radius: 20px;
            box-shadow: var(--shadow-btn);
            padding: 6px 18px;
            font-size: 0.8rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
        }

        .menu-card {
            background: var(--bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow-out);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-family: inherit;
        }

        .menu-card:hover {
            box-shadow: var(--shadow-in);
            transform: translateY(2px);
        }

        .menu-card .icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: var(--shadow-btn);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent);
            transition: all 0.3s ease;
        }

        .menu-card:hover .icon-wrap {
            box-shadow: var(--shadow-btn-active);
            color: var(--accent2);
        }

        .menu-card .card-title {
            font-weight: 600;
            font-size: 0.95rem;
            text-align: center;
        }

        .menu-card .card-desc {
            font-size: 0.75rem;
            color: var(--text-light);
            text-align: center;
        }

        .footer {
            color: var(--text-light);
            font-size: 0.78rem;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .hero { padding: 30px 20px; border-radius: 20px; }
            h1 { font-size: 1.8rem; }
            .menu-grid { grid-template-columns: 1fr 1fr; }
            .menu-grid .menu-card:last-child { grid-column: 1 / -1; }
        }
    </style>
</head>
<body>
    <div class="hero">
        <?php
            $jam = (int)date('H');
            if ($jam < 12) $sapaan = "☀️ Selamat Pagi";
            elseif ($jam < 15) $sapaan = "🌤️ Selamat Siang";
            elseif ($jam < 18) $sapaan = "🌅 Selamat Sore";
            else $sapaan = "🌙 Selamat Malam";
        ?>
        <div class="greeting-chip"><?php echo $sapaan; ?></div>

        <div class="avatar-wrap">
            <div class="avatar-inner">🎧</div>
        </div>

        <h1>Robby <span>Aprianto</span></h1>
        <p class="subtitle">AI Agent Developer · DevOps Engineer · Music Producer<br>Membangun otomasi cerdas, infrastruktur cloud, dan harmoni digital.</p>

        <div class="tags">
            <span class="tag"><i class="fa-brands fa-github" style="margin-right:5px"></i>GitHub</span>
            <span class="tag"><i class="fa-solid fa-server" style="margin-right:5px"></i>AlmaLinux VPS</span>
            <span class="tag"><i class="fa-solid fa-robot" style="margin-right:5px"></i>n8n Automation</span>
            <span class="tag"><i class="fa-solid fa-music" style="margin-right:5px"></i>Music Producer</span>
        </div>

        <div class="menu-grid">
            <a href="https://n8n.robrion.my.id" class="menu-card">
                <div class="icon-wrap"><i class="fa-solid fa-diagram-project"></i></div>
                <div class="card-title">Workflow Automation</div>
                <div class="card-desc">n8n AI Pipelines</div>
            </a>
            <a href="/music" class="menu-card">
                <div class="icon-wrap"><i class="fa-solid fa-music"></i></div>
                <div class="card-title">Music & Movies</div>
                <div class="card-desc">Player & Drama Box</div>
            </a>
            <a href="#" class="menu-card">
                <div class="icon-wrap"><i class="fa-brands fa-docker"></i></div>
                <div class="card-title">DevOps Projects</div>
                <div class="card-desc">CI/CD & Container</div>
            </a>
        </div>

        <div class="footer">&copy; <?php echo date('Y'); ?> robrion.my.id &mdash; Managed on AlmaLinux</div>
    </div>
</body>
</html>
