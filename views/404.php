<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Halaman Tidak Ditemukan | robrion.my.id</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #e0e5ec;
            --text: #2d3748;
            --text-light: #718096;
            --accent: #00e5ff;
            --accent2: #7c3aed;
            --shadow-out: 9px 9px 18px rgba(163,177,198,0.7),-9px -9px 18px rgba(255,255,255,0.85);
            --shadow-btn: 5px 5px 10px rgba(163,177,198,0.65),-5px -5px 10px rgba(255,255,255,0.8);
            --shadow-btn-active: inset 3px 3px 7px rgba(163,177,198,0.7),inset -3px -3px 7px rgba(255,255,255,0.8);
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
        body {
            font-family:'Poppins',sans-serif;
            background:var(--bg); color:var(--text);
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:20px;
        }
        .card {
            background:var(--bg);
            border-radius:30px;
            box-shadow:var(--shadow-out);
            padding:60px 50px;
            text-align:center;
            max-width:480px;
            width:100%;
            display:flex;flex-direction:column;align-items:center;gap:20px;
        }
        .err-icon {
            width:90px;height:90px;border-radius:50%;
            background:var(--bg);
            box-shadow:var(--shadow-btn);
            display:flex;align-items:center;justify-content:center;
            font-size:2.2rem;color:var(--accent);
        }
        .err-code {
            font-size:4.5rem;font-weight:700;line-height:1;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
        }
        h2{font-size:1.3rem;font-weight:600}
        p{color:var(--text-light);font-size:0.9rem;line-height:1.6}
        .btn-home {
            display:inline-flex;align-items:center;gap:8px;
            background:var(--bg);box-shadow:var(--shadow-btn);
            border:none;border-radius:20px;
            padding:12px 28px;font-family:inherit;font-size:0.9rem;
            font-weight:600;color:var(--text);cursor:pointer;
            text-decoration:none;transition:all 0.25s;margin-top:6px;
        }
        .btn-home:hover{box-shadow:var(--shadow-btn-active);color:var(--accent);}
    </style>
</head>
<body>
    <div class="card">
        <div class="err-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="err-code">404</div>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>URL yang Anda akses tidak tersedia atau sudah dipindahkan.<br>Coba kembali ke halaman utama.</p>
        <a href="/" class="btn-home"><i class="fa-solid fa-house"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
