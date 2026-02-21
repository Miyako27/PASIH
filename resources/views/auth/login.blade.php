<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login SIPASIH</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background-color:#B9B9B9;">
  <div class="w-full max-w-md rounded-2xl bg-[#F7F7F7] ring-1 ring-slate-300 shadow-md px-6 py-7">
        <div class="flex items-center justify-center gap-4 mb-5">
        <div class="h-[82px] flex items-center justify-center">
            <img src="{{ asset('images/loginlogo1.png') }}"
                alt="Logo Kanwil"
                class="h-[82px] object-contain">
        </div>
        <div class="h-[82px] flex items-center justify-center">
            <img src="{{ asset('images/loginlogo2.png') }}"
                alt="Logo PASIH"
                class="h-[82px] object-contain">
        </div>
    </div>

    <h1 class="text-center text-3xl font-extrabold tracking-tight text-[#19305D]">SIPASIH</h1>

    @if($errors->any())
      <div class="mt-4 rounded-lg bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-3 py-2 text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="mt-5 space-y-3">
      @csrf

      <label class="block text-sm font-medium text-slate-700">
        Email
        <input
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          placeholder="Masukkan Alamat Email"
          class="mt-1.5 w-full h-11 px-4 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
        >
      </label>

      <label class="block text-sm font-medium text-slate-700">
        Password
        <input
          type="password"
          name="password"
          required
          placeholder="Masukkan Password"
          class="mt-1.5 w-full h-11 px-4 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
        >
      </label>

      <div class="flex items-center justify-between text-xs pt-0.5">
        <label class="flex items-center gap-1.5 text-slate-600">
          <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
          Ingat saya
        </label>
        <a href="{{ route('password.request') }}" class="text-slate-500 hover:underline">Lupa Password?</a>
      </div>

      <button type="submit" class="mt-2 w-full h-11 rounded-lg text-white text-sm font-semibold" style="background-color:#19305D;">
        Login
      </button>
    </form>
  </div>
</body>
</html>
