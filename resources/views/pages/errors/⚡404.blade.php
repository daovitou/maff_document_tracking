<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Error 404</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
</head>

<body>
    <div class="flex min-h-screen flex-col items-center justify-center bg-zinc-50 px-6 py-24 dark:bg-zinc-900">
        <div class="text-center">
            <p class="text-9xl font-black text-zinc-200 dark:text-zinc-800">404</p>

            <div class="-mt-12">
                <flux:heading size="xl" level="1" class="mb-2">Lost in space?</flux:heading>
                <flux:subheading class="max-w-xs mx-auto">
                    The page you're looking for has vanished or never existed in this dimension.
                </flux:subheading>
            </div>

            <div class="mt-10 inline-block w-full max-w-sm">
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                    <div class="flex flex-col gap-4">
                        <flux:button icon="home" href="/" variant="primary">
                            Return to Home
                        </flux:button>

                        <flux:button variant="subtle" onclick="history.back()">
                            Go Back
                        </flux:button>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-sm text-zinc-500">
                Think this is a mistake? <flux:link href="/contact">Contact Support</flux:link>
            </p>
        </div>
    </div>


    @fluxScripts
</body>

</html>
