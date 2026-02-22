@php
  $currentRouteName = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';
  $isShowPage = \Illuminate\Support\Str::contains($currentRouteName, '.show')
    || \Illuminate\Support\Str::endsWith($currentRouteName, 'show');
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body
  class="bg-slate-50 text-slate-900"
  data-flash-success="{{ session('success') ? e(session('success')) : '' }}"
  data-is-show-page="{{ $isShowPage ? '1' : '0' }}"
>
  <div class="min-h-screen flex">
    {{-- Sidebar --}}
    <x-admin.sidebar />

    {{-- Main --}}
    <div class="flex-1 flex flex-col md:ml-[280px]">
      <x-admin.topbar />

      <main class="p-4 sm:p-6 lg:p-8">
        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
