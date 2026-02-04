<div x-data="{ 
        open: false, 
        x: 0, 
        y: 0,
        close() { this.open = false }
    }" 
    @contextmenu.prevent="open = true; x = $event.clientX; y = $event.clientY"
    @click.away="close()"
    @keydown.escape.window="close()"
    class="relative"
>
    {{-- This is the area where right-click is enabled --}}
    {{ $slot }}

    {{-- The actual Menu --}}
    <template x-teleport="body">
        <div 
            x-show="open"
            x-transition.opacity
            :style="`top: ${y}px; left: ${x}px;`"
            class="fixed z-50 min-w-[160px] bg-white border border-gray-200 rounded-lg shadow-xl py-1 text-sm text-gray-700"
        >
            {{ $menu }}
        </div>
    </template>
</div>