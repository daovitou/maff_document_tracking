@props([
    'icon' => null,
    'label' => null,
    'placeholder' => 'Choose option...',
    'readonly' => false,
    'options' => [], // Expects [['value' => 1, 'label' => 'Photography'], ...]
])

<div x-data="{
    open: false,
    search: '',
    readonly: {{ $readonly ? 'true' : 'false' }},
    selectedVal: @entangle($attributes->wire('model')),
    allOptions: {{ json_encode($options) }},

    get selectedLabel() {
        let option = this.allOptions.find(opt => opt.value == this.selectedVal);
        return option ? option.label : '{{ $placeholder }}';
    },

    get filteredOptions() {
        if (this.search === '') return this.allOptions;
        return this.allOptions.filter(opt =>
            opt.label.toLowerCase().includes(this.search.toLowerCase())
        );
    },
    toggle() {
        if (this.readonly) return;
        this.open = !this.open;
    }
}" class="relative w-full border border-zinc-300 rounded-lg">
    @if ($label)
        <flux:label class="mb-2">{{ $label }}</flux:label>
    @endif

    <div class="relative">
        {{-- The Trigger Button with Justify-Between --}}
        <flux:button variant="subtle" class="w-full flex items-center justify-between text-left font-normal px-3"
            x-on:click="toggle()" x-on:click.outside="open = false">
            @if ($icon)
                <flux:icon :name="$icon" class="size-5 text-zinc-400" variant="solid" />
            @endif
            <span :class="!selectedVal && 'text-zinc-400'" class="flex-1 truncate" x-text="selectedLabel"></span>
            <flux:icon.chevron-up-down variant="micro" class="ml-2 text-zinc-400 shrink-0" />
        </flux:button>

        {{-- Dropdown Panel --}}
        <div x-show="open" x-cloak
            class="absolute left-0 z-50 mt-2 w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-xl rounded-lg overflow-hidden"
            style="display: none;">
            {{-- Search Bar --}}
            <div class="p-2 border-b border-zinc-100 dark:border-zinc-800">
                <div class="relative flex items-center">
                    <flux:icon.magnifying-glass variant="micro" class="absolute left-3 text-zinc-400" />
                    <input x-model="search" x-ref="searchInput" type="text" placeholder="{{__('Search')}}..."
                        class="w-full pl-9 pr-3 py-1.5 text-sm bg-zinc-50 dark:bg-zinc-800 border-none rounded-md focus:ring-0 outline-none"
                        x-effect="if(open) setTimeout(() => $refs.searchInput.focus(), 50)">
                </div>
            </div>

            {{-- List of Options --}}
            <div class="max-h-60 overflow-y-auto py-2 px-4">
                <template x-for="option in filteredOptions" :key="option.value">
                    <button type="button" x-on:click="selectedVal = option.value; open = false; search = '';"
                        class="w-full text-left px-3 py-2 text-sm rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between group">
                        <span x-text="option.label"
                            :class="selectedVal == option.value ? 'text-green-600 font-medium dark:text-white' :
                                'text-zinc-700 dark:text-zinc-300'"></span>

                        <template x-if="selectedVal == option.value">
                            <flux:icon.check variant="micro" class="text-green-600" />
                        </template>
                    </button>
                </template>

                <div x-show="filteredOptions.length === 0" class="p-4 text-sm text-zinc-500 text-center">
                    No results found.
                </div>
            </div>
        </div>
    </div>
</div>
