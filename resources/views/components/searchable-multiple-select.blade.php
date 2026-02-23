@props([
    'icon' => null,
    'label' => null,
    'placeholder' => 'Select options...',
    'readonly' => false,
    'options' => [], 
])

<div 
    x-data="{
        open: false,
        search: '',
        readonly: {{ $readonly ? 'true' : 'false' }},
        {{-- Initialize as empty array if null --}}
        selectedVals: @entangle($attributes->wire('model')) || [],
        allOptions: {{ json_encode($options) }},

        toggleOption(val) {
            if (this.readonly) return;
            if (this.selectedVals.includes(val)) {
                this.selectedVals = this.selectedVals.filter(i => i != val);
            } else {
                this.selectedVals.push(val);
            }
        },

        get selectedLabels() {
            return this.allOptions.filter(opt => this.selectedVals.includes(opt.value));
        },

        get filteredOptions() {
            if (this.search === '') return this.allOptions;
            return this.allOptions.filter(opt =>
                opt.label.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    }" 
    x-on:keydown.escape.window="open = false"
    class="relative w-full"
    wire:ignore
>
    @if ($label)
        <flux:label class="mb-2">{{ $label }}</flux:label>
    @endif

    <div class="relative">
        {{-- Trigger: Displays tags or placeholder --}}
        <div 
            x-on:click="if(!readonly) open = !open"
            class="min-h-10 w-full flex flex-wrap items-center gap-1.5 px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 cursor-pointer focus-within:ring-2 focus-within:ring-zinc-500"
        >
            @if ($icon)
                <flux:icon :name="$icon" class="size-4 text-zinc-400 shrink-0" variant="solid" />
            @endif

            {{-- Placeholder --}}
            <template x-if="selectedVals.length === 0">
                <span class="text-zinc-400 text-sm">{{ $placeholder }}</span>
            </template>

            {{-- Selected Tags --}}
            <template x-for="opt in selectedLabels" :key="opt.value">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-700 text-xs font-medium text-zinc-800 dark:text-zinc-200">
                    <span x-text="opt.label"></span>
                    <button type="button" x-on:click.stop="toggleOption(opt.value)" class="hover:text-red-500">
                        <flux:icon.x-mark variant="micro" class="size-3" />
                    </button>
                </span>
            </template>

            <div class="flex-1 text-right">
                <flux:icon.chevron-up-down variant="micro" class="ml-auto text-zinc-400 shrink-0" />
            </div>
        </div>

        {{-- Dropdown Panel --}}
        <div 
            x-show="open" 
            x-cloak
            x-on:click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute left-0 z-[60] mt-2 w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-xl rounded-lg overflow-hidden"
        >
            {{-- Search Bar --}}
            <div class="p-2 border-b border-zinc-100 dark:border-zinc-800">
                <div class="relative flex items-center">
                    <flux:icon.magnifying-glass variant="micro" class="absolute left-3 text-zinc-400" />
                    <input 
                        x-model="search" 
                        x-ref="searchInput" 
                        type="text" 
                        placeholder="{{__('Search')}}..."
                        class="w-full pl-9 pr-3 py-1.5 text-sm bg-zinc-50 dark:bg-zinc-800 border-none rounded-md outline-none"
                        x-effect="if(open) setTimeout(() => $refs.searchInput.focus(), 100)"
                    >
                </div>
            </div>

            {{-- List --}}
            <div class="max-h-60 overflow-y-auto p-1">
                <template x-for="option in filteredOptions" :key="option.value">
                    <button 
                        type="button" 
                        x-on:click="toggleOption(option.value)"
                        class="w-full text-left px-3 py-2 text-sm rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between group"
                    >
                        <span 
                            x-text="option.label"
                            :class="selectedVals.includes(option.value) ? 'text-zinc-900 font-semibold dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                        ></span>

                        <template x-if="selectedVals.includes(option.value)">
                            <flux:icon.check variant="micro" class="text-zinc-800 dark:text-white" />
                        </template>
                    </button>
                </template>

                <div x-show="filteredOptions.length === 0" class="p-4 text-sm text-zinc-500 text-center">
                    {{ __('No results found.') }}
                </div>
            </div>

            {{-- Optional: Footer to Clear All --}}
            <template x-if="selectedVals.length > 0">
                <div class="p-2 border-t border-zinc-100 dark:border-zinc-800 text-right">
                    <button type="button" x-on:click="selectedVals = []" class="text-xs text-red-500 hover:underline">
                        Clear all
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>