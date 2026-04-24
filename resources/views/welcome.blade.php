<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PASIH - Home Page</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="welcome-page">
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

    <main class="hero">
        <div class="hero-inner">
            <h1 class="headline">
                Hasil Analisis
                <br>
                <span class="headline-accent">Peraturan Daerah</span> Kemenkum Riau
            </h1>

            <p class="subtitle">
                Menyediakan hasil analisis dan evaluasi peraturan daerah yang dapat diakses serta dimanfaatkan oleh masyarakat, termasuk untuk keperluan akademik dan penelitian
            </p>

            <a class="cta" href="{{ route('public.analysis.index') }}">Lihat Hasil Analisis</a>
        </div>

        <svg class="wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,44 C130,18 290,22 430,34 C590,48 740,54 890,34 C1030,16 1190,20 1310,32 C1368,38 1412,40 1440,36 L1440,120 L0,120 Z" fill="var(--bg-secondary)"></path>
        </svg>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <section>
                <h3>Lokasi Kantor</h3>
                <div class="map-wrap">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3454.5826483470028!2d101.4478346!3d0.5210424!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5ac1b73791a1b%3A0xef96772586252a40!2sMinistry%20of%20Law%20and%20Human%20Rights!5e1!3m2!1sen!2sid!4v1776957611911!5m2!1sen!2sid" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </section>

            <section>
                <h3>Kantor Wilayah Kementerian Hukum Riau</h3>
                <div class="office-list">
                    <p class="office-item">
                        <span class="icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <path d="M3 21h18M5 21V7h14v14M9 7V3h6v4M8 11h2M14 11h2M8 15h2M14 15h2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>Jl. Jend. Sudirman No.233, Sumahilang, Kec. Pekanbaru Kota, Kota Pekanbaru, Riau 28111</span>
                    </p>
                    <p class="office-item">
                        <span class="icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011-.24c1.1.37 2.28.56 3.49.56a1 1 0 011 1V20a1 1 0 01-1 1C10.3 21 3 13.7 3 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.21.19 2.39.56 3.49a1 1 0 01-.24 1l-2.2 2.3z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>0811-6904-422</span>
                    </p>
                    <p class="office-item">
                        <span class="icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <rect x="3" y="5" width="18" height="14" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M3 7l9 7 9-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="stack">
                            <span>Email Kehumasan</span>
                            <span class="muted">humaskumriau@gmail.com</span>
                        </span>
                    </p>
                    <p class="office-item">
                        <span class="icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <rect x="3" y="5" width="18" height="14" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M3 7l9 7 9-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="stack">
                            <span>Email Pengaduan</span>
                            <span class="muted">humaskumriau@gmail.com</span>
                        </span>
                    </p>
                </div>
            </section>

        </div>

        <div class="copyright">
            &copy; {{ date('Y') }} PASIH - Kementerian Hukum Provinsi Riau. Dikembangkan bersama Politeknik Caltex Riau.
        </div>
    </footer>
</body>
</html>
