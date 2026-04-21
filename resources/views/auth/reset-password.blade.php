<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Atur Ulang Password - PASIH</title>
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
        Email <span class="text-red-500">*</span>
        <input
          type="email"
          name="email"
          value="{{ old('email', $email) }}"
          required
          readonly
          placeholder="Masukkan Alamat Email"
          class="mt-1.5 w-full h-11 px-4 py-2 rounded border border-[#B9B9B9] bg-slate-100 text-sm text-slate-700 placeholder:text-[14px] cursor-not-allowed"
        >
      </label>

      <label class="block text-sm font-medium text-slate-700">
        New Password <span class="text-red-500">*</span>
        <div class="relative mt-1.5">
          <input
            id="new-password"
            type="password"
            name="password"
            required
            placeholder="Masukkan Password"
            class="w-full h-11 px-4 pr-11 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
          >
          <button
            type="button"
            data-toggle-password
            data-target="new-password"
            aria-label="Tampilkan password"
            aria-pressed="false"
            class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 focus:outline-none"
          >
            <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.413-3.416M9.88 5.09A9.76 9.76 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.97 9.97 0 01-4.132 5.411M6.228 6.231C4.383 7.477 3.06 9.518 2.457 12A9.97 9.97 0 006.59 17.411"/>
            </svg>
          </button>
        </div>
        <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.</p>
      </label>

      <label class="block text-sm font-medium text-slate-700">
        Confirm Password <span class="text-red-500">*</span>
        <div class="relative mt-1.5">
          <input
            id="confirm-password"
            type="password"
            name="password_confirmation"
            required
            placeholder="Masukkan Password"
            class="w-full h-11 px-4 pr-11 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
          >
          <button
            type="button"
            data-toggle-password
            data-target="confirm-password"
            aria-label="Tampilkan password"
            aria-pressed="false"
            class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 focus:outline-none"
          >
            <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.413-3.416M9.88 5.09A9.76 9.76 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.97 9.97 0 01-4.132 5.411M6.228 6.231C4.383 7.477 3.06 9.518 2.457 12A9.97 9.97 0 006.59 17.411"/>
            </svg>
          </button>
        </div>
      </label>

      <button type="submit" class="w-full h-11 rounded-lg text-white text-[14px] font-semibold" style="background-color:#19305D;">
        Kirim
      </button>
    </form>
  </div>
  <script>
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
      const targetId = button.getAttribute('data-target');
      const input = targetId ? document.getElementById(targetId) : null;
      const eyeOpenIcon = button.querySelector('[data-eye-open]');
      const eyeClosedIcon = button.querySelector('[data-eye-closed]');

      if (!input || !eyeOpenIcon || !eyeClosedIcon) return;

      button.addEventListener('click', function () {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        eyeOpenIcon.classList.toggle('hidden', !isHidden);
        eyeClosedIcon.classList.toggle('hidden', isHidden);
        button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        button.setAttribute('aria-pressed', String(isHidden));
      });
    });
  </script>
</body>
</html>
