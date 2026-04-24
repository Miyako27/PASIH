<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PASIH')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-page min-h-screen bg-slate-100 text-slate-800">
    <header class="topbar">
        <div class="brand">
            <img class="brand-logo" src="{{ asset('images/loginlogo2.png') }}" alt="Logo Kementerian Hukum">
            <div>
                <div class="brand-title">PASIH</div>
                <p class="brand-subtitle">Pendampingan Analisis &amp; Evaluasi Peraturan Daerah Kementerian Hukum Provinsi Riau</p>
            </div>
        </div>

        <a class="login-btn" href="{{ route('login') }}">Masuk</a>
    </header>

    @yield('content')

    <footer class="public-footer">
        <div class="public-copyright">
            © 2026 PASIH - Kementerian Hukum Provinsi Riau. Dikembangkan bersama Politeknik Caltex Riau.
        </div>
    </footer>
</body>
</html>
