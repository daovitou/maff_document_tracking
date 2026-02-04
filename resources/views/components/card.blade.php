@props(['padding' => 'p-6'])

<div {{ $attributes->class([
    'rounded-xl border shadow-sm',
    'bg-white border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700',
    $padding
]) }}>
    {{ $slot }}
</div>