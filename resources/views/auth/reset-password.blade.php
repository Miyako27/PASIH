<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Atur Ulang Password - SIPASIH</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background-color:#D9D9DE;">
  <div class="w-full max-w-md rounded-2xl bg-[#F7F7F7] ring-1 ring-slate-300 shadow-md px-6 py-7">
    <h1 class="text-center text-[25px] font-bold tracking-tight text-[#19305D]">Reset Password</h1>

    @if($errors->any())
      <div class="mt-4 rounded-lg bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-3 py-2 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <label class="block text-sm font-medium text-slate-700">
        Email
        <input
          type="email"
          name="email"
          value="{{ old('email', $email) }}"
          required
          placeholder="Masukkan Alamat Email"
          class="mt-1.5 w-full h-11 px-4 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
        >
      </label>

      <label class="block text-sm font-medium text-slate-700">
        New Password
        <input
          type="password"
          name="password"
          required
          placeholder="Masukkan Password"
          class="mt-1.5 w-full h-11 px-4 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
        >
      </label>

      <label class="block text-sm font-medium text-slate-700">
        Confirm Password
        <input
          type="password"
          name="password_confirmation"
          required
          placeholder="Masukkan Password"
          class="mt-1.5 w-full h-11 px-4 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
        >
      </label>

      <button type="submit" class="w-full h-11 rounded-lg text-white text-[14px] font-semibold" style="background-color:#19305D;">
        Kirim
      </button>
    </form>
  </div>
</body>
</html>
