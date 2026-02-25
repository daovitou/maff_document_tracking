<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    @include('layouts.admin.sidebar')
    @include('layouts.admin.header')
    <flux:main>
        {{ $slot }}
    </flux:main>
    {{-- <div x-data="{ loading: false }" x-show="loading" @loading.window="loading = $event.detail.loading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-100/70 dark:bg-zinc-700/40"
        style="display: none;">

        <div class="flex flex-col items-center justify-center g-4">
            <div class="relative h-20 w-20">
                <div class="absolute h-full w-full rounded-full border-8 border-zinc-500/20 dark:border-zinc-200/20">
                </div>
                <div
                    class="absolute h-full w-full animate-spin rounded-full border-8 border-t-accent-content border-r-transparent border-b-transparent border-l-transparent">
                </div>
            </div>

            <p class="mt-4 text-lg font-semibold text-accent-content tracking-widest animate-pulse">
                {{__('Processing')}}...
            </p>
        </div>
    </div> --}}
    <div x-data="{ loading: false }" x-show="loading" @loading.window="loading = $event.detail.loading"
        class="fixed flex inset-0 bg-zinc-100/20 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{ __('Processing your request') }}...</p>
        </div>
    </div>
    <script>
        // window.addEventListener('load', (event) => {
        //     window.dispatchEvent(new CustomEvent('loading', {
        //         detail: {
        //             loading: false
        //         }
        //     }));
        // });
        // window.addEventListener('beforeunload', (event) => {
        //     window.dispatchEvent(new CustomEvent('loading', {
        //         detail: {
        //             loading: true
        //         }
        //     }));
        //     console.log(event.detail.loading)
        // });
        document.addEventListener('livewire:navigate', () => {
            window.dispatchEvent(new CustomEvent('loading', {
                detail: {
                    loading: true
                }
            }));
        });

        document.addEventListener('livewire:navigated', () => {
            window.dispatchEvent(new CustomEvent('loading', {
                detail: {
                    loading: false
                }
            }));
        });
    </script>
    @livewireScripts
    @fluxScripts
</body>

</html>
