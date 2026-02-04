@props(['displayName' => '', 'field' => '', 'sortField' => '', 'sortDirection' => ''])

<span class="flex-1 flex items-center justify-between">
    {{ $displayName }}
    @if ($sortField !== $field)
        <x-ri-expand-up-down-fill class="w-4 h-4 text-zinc-400 dark:text-zinc-400/20" />
    @elseif ($sortDirection === 'ASC')
        <x-ri-arrow-down-s-fill class="w-4 h-4 text-zinc-500 dark:text-zinc-300" />
    @else
        <x-ri-arrow-up-s-fill class="w-4 h-4 text-zinc-500 dark:text-zinc-300" />
    @endif
</span>
