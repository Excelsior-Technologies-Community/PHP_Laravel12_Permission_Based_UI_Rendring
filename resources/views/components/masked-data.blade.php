@props([
    'permission' => 'view product costs',
    'value' => null,
    'mask' => '••••••',
])

@php
    $canView = auth()->check() && auth()->user()->can($permission);
    $policy = session('ui_policy', 'disabled_lock');
@endphp

@if($canView)
    <span>{{ $value ?? $slot }}</span>
@else
    @if($policy === 'disabled_lock')
        <span x-data="{ showTooltip: false }" 
              @mouseenter="showTooltip = true" 
              @mouseleave="showTooltip = false" 
              class="relative inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-400 font-mono text-xs border border-slate-200 cursor-not-allowed select-none">
            <span class="tracking-widest font-bold text-slate-500">{{ $mask }}</span>
            <span class="text-[10px]" title="Field Masked">🔒</span>

            <!-- Tooltip -->
            <span x-show="showTooltip" 
                  x-transition:enter="transition ease-out duration-150"
                  x-transition:enter-start="opacity-0 translate-y-1"
                  x-transition:enter-end="opacity-100 translate-y-0"
                  style="display: none;"
                  class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 w-max max-w-xs px-2.5 py-1 bg-slate-900 text-slate-100 text-[11px] rounded-lg shadow-xl border border-slate-700 z-50 pointer-events-none">
                🔒 Masked: Requires '{{ $permission }}' permission
            </span>
        </span>
    @else
        <span class="text-slate-300 font-mono text-xs">—</span>
    @endif
@endif
