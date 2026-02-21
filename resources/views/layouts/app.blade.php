<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
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
