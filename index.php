<?php
// Set timezone ke Jakarta
date_default_timezone_set('Asia/Jakarta');

// Logika sapaan dinamis berdasarkan jam
$jam = date('H');
if ($jam < 12) {
    $sapaan = "Selamat Pagi";
} elseif ($jam < 15) {
    $sapaan = "Selamat Siang";
} elseif ($jam < 18) {
    $sapaan = "Selamat Sore";
} else {
    $sapaan = "Selamat Malam";
}

$tahun_sekarang = date('Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robby Aprianto | AI Agent & DevOps</title>
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            text-align: center;
            max-width: 600px;
            padding: 20px;
        }

        .greeting {
            color: #8b949e;
            font-size: 1.2em;
            margin-bottom: -10px;
            font-style: italic;
        }

        h1 {
            color: #58a6ff;
            font-size: 2.5em;
            margin-bottom: 0.5em;
            letter-spacing: 1px;
        }

        p {
            line-height: 1.6;
            font-size: 1.1em;
            color: #8b949e;
        }

        .links {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin: 10px;
            border: 1px solid #30363d;
            border-radius: 6px;
            text-decoration: none;
            color: #c9d1d9;
            transition: all 0.3s ease;
            background-color: #161b22;
        }

        .btn:hover {
            background-color: #1f6feb;
            border-color: #58a6ff;
            color: white;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 50px;
            font-size: 0.8em;
            color: #484f58;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="greeting">
            <?php echo $sapaan; ?> 👋
        </div>
        <h1>Robby Aprianto</h1>
        <p>AI Agent Developer | DevOps Engineer | Music Producer</p>
        <p>Membangun otomasi cerdas, mengelola infrastruktur cloud, dan menciptakan harmoni digital.</p>

        <div class="links">
            <a href="https://n8n.robrion.my.id" class="btn">Workflow Automations (n8n)</a>
            <a href="music.php" class="btn">Music Portfolio</a>
            <a href="#" class="btn">DevOps Projects</a>
        </div>

        <div class="footer">
            &copy;
            <?php echo $tahun_sekarang; ?> robrion.my.id. Managed on AlmaLinux.
        </div>
    </div>
</body>

</html>