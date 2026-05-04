<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login PASIH</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background-color:#B9B9B9;">
  <div class="w-full max-w-md rounded-2xl bg-[#F7F7F7] ring-1 ring-slate-300 shadow-md px-6 py-7">
        <div class="flex items-center justify-center gap-4 mb-5">
        <div class="h-[82px] flex items-center justify-center">
            <img src="{{ asset('images/loginlogo2.png') }}"
                alt="Logo PASIH"
                class="h-[82px] object-contain">
        </div>
    </div>

    <h1 class="text-center text-3xl font-extrabold tracking-tight text-[#19305D]">PASIH</h1>
    <p class="text-center text-sm text-slate-700 mt-1">Pendampingan Analisis dan Evaluasi Hukum Daerah </p>

    @if($errors->any())
      <div class="mt-4 rounded-lg bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-3 py-2 text-sm">{{ $errors->first() }}</div>
    @endif
    <div id="recaptcha-inline-error" class="mt-4 rounded-lg bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-3 py-2 text-sm hidden">
      Silakan centang reCAPTCHA terlebih dahulu.
    </div>

    <form id="login-form" method="POST" action="{{ route('login.attempt') }}" class="mt-5 space-y-3">
      @csrf

      <label class="block text-sm font-medium text-slate-700">
        Email <span class="text-red-500">*</span>
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
        Password <span class="text-red-500">*</span>
        <div class="relative mt-1.5">
          <input
            id="password"
            type="password"
            name="password"
            required
            placeholder="Masukkan Password"
            class="w-full h-11 px-4 pr-11 py-2 rounded border border-[#B9B9B9] bg-white text-sm placeholder:text-[14px]"
          >
          <button
            type="button"
            id="toggle-password"
            aria-label="Tampilkan password"
            aria-pressed="false"
            class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 focus:outline-none"
          >
            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.413-3.416M9.88 5.09A9.76 9.76 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.97 9.97 0 01-4.132 5.411M6.228 6.231C4.383 7.477 3.06 9.518 2.457 12A9.97 9.97 0 006.59 17.411"/>
            </svg>
          </button>
        </div>
      </label>

      <div class="flex items-center justify-between text-xs pt-0.5">
        <label class="flex items-center gap-1.5 text-slate-600">
          <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
          Ingat saya
        </label>
        <a href="{{ route('password.request') }}" class="text-slate-500 hover:underline">Lupa Password?</a>
      </div>

      <div class="pt-2 flex justify-center">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onRecaptchaSuccess" data-expired-callback="onRecaptchaExpired"></div>
      </div>

      <button type="submit" class="mt-2 w-full h-11 rounded-lg text-white text-sm font-semibold" style="background-color:#19305D;">
        Login
      </button>
    </form>
  </div>
  <script>
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('toggle-password');
    const eyeOpenIcon = document.getElementById('eye-open');
    const eyeClosedIcon = document.getElementById('eye-closed');
    const loginForm = document.getElementById('login-form');
    const recaptchaInlineError = document.getElementById('recaptcha-inline-error');
    const emailInput = loginForm.querySelector('input[name="email"]');
    const passwordField = loginForm.querySelector('input[name="password"]');

    const setLoginFieldValidityMessage = function (field) {
      field.setCustomValidity('');

      const label = (field.getAttribute('data-label') || field.getAttribute('name') || 'kolom ini').toLowerCase();
      if (field.validity.valueMissing) {
        field.setCustomValidity(`Silakan isi ${label} terlebih dahulu.`);
        return;
      }

      if (field.validity.typeMismatch && field.type === 'email') {
        field.setCustomValidity('Format email tidak valid. Contoh: nama@domain.com.');
      }
    };

    togglePasswordButton.addEventListener('click', function () {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type = isHidden ? 'text' : 'password';

      eyeOpenIcon.classList.toggle('hidden', !isHidden);
      eyeClosedIcon.classList.toggle('hidden', isHidden);
      togglePasswordButton.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
      togglePasswordButton.setAttribute('aria-pressed', String(isHidden));
    });

    loginForm.addEventListener('submit', function (event) {
      const recaptchaValue = document.querySelector('[name="g-recaptcha-response"]')?.value;

      if (!recaptchaValue) {
        event.preventDefault();
        recaptchaInlineError.classList.remove('hidden');
      }
    });

    [emailInput, passwordField].forEach(function (field) {
      if (!field) {
        return;
      }

      field.setAttribute('data-label', field.getAttribute('name') === 'email' ? 'Email' : 'Password');

      field.addEventListener('invalid', function () {
        setLoginFieldValidityMessage(field);
      });

      field.addEventListener('input', function () {
        field.setCustomValidity('');
      });
    });

    window.onRecaptchaSuccess = function () {
      recaptchaInlineError.classList.add('hidden');
    };

    window.onRecaptchaExpired = function () {
      recaptchaInlineError.classList.remove('hidden');
    };
  </script>
</body>
</html>
