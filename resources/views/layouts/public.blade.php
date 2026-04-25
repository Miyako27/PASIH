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
@php
    $publicBodyClass = trim($__env->yieldContent('public_body_class'));
@endphp
<body class="public-page {{ $publicBodyClass }} min-h-screen bg-slate-100 text-slate-800">
    @php
        $isPublicAnalysisPage = request()->routeIs('public.analysis.*');
    @endphp

    <header class="topbar">
        <div class="brand">
            <img class="brand-logo" src="{{ asset('images/loginlogo2.png') }}" alt="Logo Kementerian Hukum">
            <div>
                <div class="brand-title">PASIH</div>
                <p class="brand-subtitle">Pendampingan Analisis &amp; Evaluasi Peraturan Daerah Kementerian Hukum Provinsi Riau</p>
            </div>
        </div>

        <div class="topbar-actions">
            <a class="login-btn" href="{{ route('login') }}">Masuk</a>
            <button
                type="button"
                data-sidebar-toggle
                aria-label="Buka menu"
                class="menu-btn"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </header>

    <div data-sidebar-overlay class="fixed inset-0 z-[60] hidden bg-slate-900/50 backdrop-blur-[1px] md:hidden"></div>

    <aside
        data-sidebar-drawer
        class="fixed inset-y-0 left-0 z-[70] flex w-[280px] -translate-x-full flex-col overflow-y-auto bg-white shadow-2xl transition-transform duration-200 ease-out md:hidden"
    >
        <div class="px-4 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/loginlogo2.png') }}" alt="Logo Kementerian Hukum" class="w-10 h-10 rounded-md object-cover">
                <div class="min-w-0">
                    <div class="font-extrabold tracking-tight text-lg text-[#29346b] truncate">PASIH</div>
                    <div class="text-[11px] leading-snug text-slate-500">Pendampingan Analisis &amp; Evaluasi Peraturan Daerah Kementerian Hukum Provinsi Riau</div>
                </div>
            </div>
            <button
                type="button"
                data-sidebar-close
                aria-label="Tutup menu"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-300 text-slate-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="px-4 py-4 space-y-2">
            <a
                href="{{ route('home') }}"
                data-sidebar-link
                class="block rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('home') ? 'bg-[#eef2ff] text-[#1f275e]' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                Beranda
            </a>
            <a
                href="{{ route('public.analysis.index') }}"
                data-sidebar-link
                class="block rounded-xl px-4 py-3 text-sm font-semibold {{ $isPublicAnalysisPage ? 'bg-[#eef2ff] text-[#1f275e]' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                Hasil Analisis
            </a>
            <a
                href="{{ route('login') }}"
                data-sidebar-link
                class="mt-3 block w-full rounded-xl px-4 py-3 text-sm font-semibold bg-[#1f275e] text-white hover:bg-[#27316a] text-center"
            >
                Masuk
            </a>
        </nav>
    </aside>

    @yield('content')

    @hasSection('public_footer')
        @yield('public_footer')
    @else
        <footer class="public-footer">
            <div class="public-copyright">
                &copy; 2026 PASIH - Kementerian Hukum Provinsi Riau. Dikembangkan bersama Politeknik Caltex Riau.
            </div>
        </footer>
    @endif
</body>
</html>
