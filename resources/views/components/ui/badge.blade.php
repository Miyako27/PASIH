@props(['tone' => 'slate']) {{-- slate|green|amber|rose + custom submission/analysis tones --}}

@php
  $tones = [
    'slate' => 'bg-slate-100 text-slate-700 ring-slate-700',
    'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-700',
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-700',
    'rose'  => 'bg-rose-50 text-rose-700 ring-rose-700',
    'account-analis-hukum' => 'bg-[#CFFAFE] text-[#0F4C5C] ring-[#99E7F5]',
    'account-operator-pemda' => 'bg-[#DBEAFE] text-[#1E3A8A] ring-[#BFDBFE]',
    'account-operator-kanwil' => 'bg-[#D1FAE5] text-[#065F46] ring-[#A7F3D0]',
    'account-ketua-tim-analisis' => 'bg-[#FFE4D6] text-[#9A3412] ring-[#FBCFB8]',
    'account-kakanwil' => 'bg-[#E9D5FF] text-[#6B21A8] ring-[#D8B4FE]',
    'account-kepala-divisi-p3h' => 'bg-[#FEF3C7] text-[#92400E] ring-[#FDE68A]',
    'account-admin' => 'bg-[#FECACA] text-[#991B1B] ring-[#FCA5A5]',
    'permohonan-unassigned' => 'bg-[#F2F4F7] text-[#364254] ring-[#364254]',
    'permohonan-available' => 'bg-[#DBEAFE] text-[#1D4ED8] ring-[#1D4ED8]',
    'permohonan-in-analysis' => 'bg-[#EDE9FE] text-[#5B21B6] ring-[#5B21B6]',
    'permohonan-awaiting-kadiv' => 'bg-[#FEF3C7] text-[#92400E] ring-[#92400E]',
    'permohonan-awaiting-kakanwil' => 'bg-[#FCE7F3] text-[#9D174D] ring-[#9D174D]',
    'permohonan-revision' => 'bg-[#FEE2E2] text-[#991B1B] ring-[#991B1B]',
    'permohonan-done' => 'bg-[#CCFBF1] text-[#0F766E] ring-[#0F766E]',
    'analisis-submitted' => 'bg-[#F2F4F7] text-[#364254] ring-[#364254]',
    'analisis-accepted' => 'bg-[#DCFCE7] text-[#166534] ring-[#166534]',
    'analisis-revised' => 'bg-[#FEF3C7] text-[#92400E] ring-[#92400E]',
    'analisis-rejected' => 'bg-[#FEE2E2] text-[#991B1B] ring-[#991B1B]',
  ][$tone] ?? 'bg-slate-100 text-slate-700 ring-slate-700';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $tones }}">
  {{ $slot }}
</span>
