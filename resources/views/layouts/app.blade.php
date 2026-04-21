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
  class="bg-slate-50 text-slate-900 overflow-x-clip"
  data-flash-success="{{ session('success') ? e(session('success')) : '' }}"
  data-is-show-page="{{ $isShowPage ? '1' : '0' }}"
>
  <div class="min-h-screen flex">
    {{-- Sidebar --}}
    <x-admin.sidebar />

    {{-- Main --}}
    <div class="flex-1 min-w-0 flex flex-col md:ml-[280px]">
      <x-admin.topbar />

      <main class="min-w-0 p-4 sm:p-6 lg:p-8">
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    (() => {
      const normalizeLabelText = (value) => {
        return (value || '')
          .replace(/\r?\n+/g, ' ')
          .replace(/\s+/g, ' ')
          .replace(/\*/g, '')
          .replace(/\(.*?\)/g, '')
          .replace(/\s*:\s*$/g, '')
          .trim();
      };

      const extractLabelText = (labelEl) => {
        if (!labelEl) return '';
        const clone = labelEl.cloneNode(true);
        clone.querySelectorAll('input, select, textarea, button').forEach((node) => node.remove());

        return normalizeLabelText(clone.textContent || '');
      };

      const getFieldLabel = (el) => {
        const fromAria = (el.getAttribute('aria-label') || '').trim();
        if (fromAria) return fromAria;

        const id = (el.getAttribute('id') || '').trim();
        if (id) {
          const explicitLabel = document.querySelector(`label[for="${id}"]`);
          if (explicitLabel) {
            const text = extractLabelText(explicitLabel);
            if (text) return text;
          }
        }

        const wrappingLabel = el.closest('label');
        if (wrappingLabel) {
          const text = extractLabelText(wrappingLabel);
          if (text) return text;
        }

        const parent = el.parentElement;
        if (parent) {
          const siblingLabel = parent.querySelector(':scope > label');
          if (siblingLabel) {
            const text = extractLabelText(siblingLabel);
            if (text) return text;
          }
        }

        let prev = el.previousElementSibling;
        while (prev) {
          if (prev.tagName === 'LABEL') {
            const text = extractLabelText(prev);
            if (text) return text;
          }
          prev = prev.previousElementSibling;
        }

        const fromName = (el.getAttribute('name') || '').trim();
        return normalizeLabelText(fromName.replace(/_/g, ' ')) || 'kolom ini';
      };

      const sentenceCase = (value) => {
        const text = (value || '').trim();
        if (!text) return 'kolom ini';
        return text.toLocaleLowerCase('id-ID');
      };

      const applyIndonesianValidity = (el) => {
        if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) return;

        const inlineOnInvalid = (el.getAttribute('oninvalid') || '').toLowerCase();
        if (inlineOnInvalid.includes('setcustomvalidity')) return;

        el.setCustomValidity('');

        const label = sentenceCase(getFieldLabel(el));
        if (el.validity.valueMissing) {
          if (el instanceof HTMLSelectElement) {
            el.setCustomValidity(`Silakan pilih ${label} terlebih dahulu.`);
            return;
          }

          if (el instanceof HTMLInputElement && el.type === 'file') {
            el.setCustomValidity(`Silakan unggah ${label} terlebih dahulu.`);
            return;
          }

          el.setCustomValidity(`Silakan isi ${label} terlebih dahulu.`);
          return;
        }

        if (el.validity.typeMismatch && el instanceof HTMLInputElement && el.type === 'email') {
          el.setCustomValidity('Format email tidak valid. Contoh: nama@domain.com.');
          return;
        }

        if (el.validity.tooShort) {
          el.setCustomValidity(`Minimal ${el.minLength} karakter untuk ${label}.`);
          return;
        }

        if (el.validity.tooLong) {
          el.setCustomValidity(`Maksimal ${el.maxLength} karakter untuk ${label}.`);
          return;
        }

        if (el.validity.patternMismatch) {
          el.setCustomValidity(`Format ${label} tidak sesuai.`);
        }
      };

      document.addEventListener('invalid', (event) => {
        applyIndonesianValidity(event.target);
      }, true);

      document.addEventListener('input', (event) => {
        const el = event.target;
        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement) {
          el.setCustomValidity('');
        }
      }, true);

      document.addEventListener('change', (event) => {
        const el = event.target;
        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement) {
          el.setCustomValidity('');
        }
      }, true);
    })();
  </script>

  @stack('scripts')
</body>
</html>
