@props([
    'permission' => null,
    'role' => null,
    'tooltip' => null,
    'tag' => 'div',
])

@php
    $hasAccess = true;

    if ($permission) {
        $hasAccess = auth()->check() && auth()->user()->can($permission);
    } elseif ($role) {
        $hasAccess = auth()->check() && auth()->user()->hasRole($role);
    }

    $policy = session('ui_policy', 'disabled_lock');
    $requiredText = $permission ? "Requires '{$permission}' permission" : "Requires '{$role}' role";
    $tooltipMessage = $tooltip ?? "🔒 Access Restricted: {$requiredText}";
@endphp

@if($hasAccess)
    {{ $slot }}
@elseif($policy === 'disabled_lock')
    <div x-data="{ showTooltip: false }" 
         @mouseenter="showTooltip = true" 
         @mouseleave="showTooltip = false" 
         class="relative inline-block cursor-not-allowed group">
        
        <!-- Disabled Wrapper Content -->
        <div class="opacity-40 grayscale pointer-events-none select-none filter blur-[0.3px]">
            {{ $slot }}
        </div>

        <!-- Lock Badge Overlay -->
        <div class="absolute -top-1.5 -right-1.5 bg-rose-900 border border-rose-500/80 text-rose-200 text-[10px] font-mono px-1.5 py-0.5 rounded-full shadow-md flex items-center gap-0.5 z-10 pointer-events-none">
            <span>🔒</span>
        </div>

        <!-- Interactive Floating Tooltip -->
        <div x-show="showTooltip" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             style="display: none;"
             class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max max-w-xs px-3 py-1.5 bg-slate-950/95 text-slate-100 text-xs rounded-xl shadow-2xl border border-rose-500/50 text-center z-50 pointer-events-none">
            <div class="font-bold text-rose-400 flex items-center justify-center gap-1">
                <span>🔒</span>
                <span>Permission Restricted</span>
            </div>
            <div class="text-[11px] text-slate-300 mt-0.5 font-sans">
                {{ $tooltipMessage }}
            </div>
            <!-- Arrow -->
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-slate-950"></div>
        </div>
    </div>
@endif
