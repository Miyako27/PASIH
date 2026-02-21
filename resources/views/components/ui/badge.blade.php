@props(['tone' => 'slate']) {{-- slate|green|amber|rose + custom submission/analysis tones --}}

@php
  $tones = [
    'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
    'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'rose'  => 'bg-rose-50 text-rose-700 ring-rose-200',
    'permohonan-unassigned' => 'bg-[#F2F4F7] text-[#364254] ring-[#F2F4F7]',
    'permohonan-available' => 'bg-[#DBEAFE] text-[#1D4ED8] ring-[#DBEAFE]',
    'permohonan-in-analysis' => 'bg-[#EDE9FE] text-[#5B21B6] ring-[#EDE9FE]',
    'permohonan-done' => 'bg-[#CCFBF1] text-[#0F766E] ring-[#CCFBF1]',
    'analisis-submitted' => 'bg-[#F2F4F7] text-[#364254] ring-[#F2F4F7]',
    'analisis-accepted' => 'bg-[#DCFCE7] text-[#166534] ring-[#DCFCE7]',
    'analisis-revised' => 'bg-[#FEF3C7] text-[#92400E] ring-[#FEF3C7]',
    'analisis-rejected' => 'bg-[#FEE2E2] text-[#991B1B] ring-[#FEE2E2]',
  ][$tone] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $tones }}">
  {{ $slot }}
</span>
