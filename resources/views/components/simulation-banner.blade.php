@if(session()->has('simulated_role'))
    @php
        $simRole = session('simulated_role');
        $policy = session('ui_policy', 'disabled_lock');
    @endphp

    <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white px-4 py-2.5 shadow-xl sticky top-0 z-50 border-b border-amber-400/40">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm">
            <div class="flex items-center gap-2.5 font-medium">
                <span class="text-xl animate-pulse">🎭</span>
                <div>
                    <span class="font-bold uppercase tracking-wider text-amber-100">Live UI Preview Mode:</span>
                    <span>Viewing system UI as <strong class="underline decoration-2 underline-offset-2 uppercase font-extrabold text-white px-1.5 py-0.5 rounded bg-black/30 font-mono">{{ $simRole }}</strong></span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- UI Policy Toggle Badge -->
                <form action="{{ route('simulator.policy') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <input type="hidden" name="policy" value="{{ $policy === 'disabled_lock' ? 'hide' : 'disabled_lock' }}">
                    <button type="submit" 
                            title="Click to toggle between Disabled-Lock and Hidden mode"
                            class="px-2.5 py-1 rounded-lg bg-black/40 hover:bg-black/60 border border-white/20 text-xs font-mono font-semibold transition flex items-center gap-1.5 text-amber-100">
                        <span>Policy:</span>
                        @if($policy === 'disabled_lock')
                            <span class="text-emerald-300 font-bold">🔒 Disabled-Lock</span>
                        @else
                            <span class="text-rose-200 font-bold">👁️ Hide Elements</span>
                        @endif
                    </button>
                </form>

                <!-- Exit Preview Button -->
                <form action="{{ route('simulator.exit') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-1 bg-white hover:bg-amber-100 text-amber-900 font-bold rounded-lg text-xs shadow-md transition flex items-center gap-1 active:scale-95">
                        <span>✕</span>
                        <span>Exit Preview</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
