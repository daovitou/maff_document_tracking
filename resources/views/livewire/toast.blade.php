<?php
use function Livewire\Volt\{on, mount};

// This runs on the server side the moment the component is initialized
mount(function () {
    if (session()->has('notify')) {
        $notification = session('notify');

        // We dispatch an event to the browser so Alpine can catch it
        $this->dispatch('add-toast', message: $notification['message'], type: $notification['type'] ?? 'success');
    }
});

// This handles "Live" updates without a page refresh
on([
    'notify' => function ($message, $type = 'success') {
        $this->dispatch('add-toast', message: $message, type: $type);
    },
]);
?>

<div x-data="{
    toasts: [],
    add(e) {
        const id = Date.now();
        this.toasts.push({ id, message: e.detail.message, type: e.detail.type });
        setTimeout(() => this.remove(id), 5000);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}" @add-toast.window="add($event)"
    class="fixed top-6 right-6 z-[100] flex flex-col gap-3 w-full max-w-[350px]">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-20"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-end="opacity-0 scale-90"
            class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-xl ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div class="flex items-center gap-3">
                <span
                    :class="{
                        'text-emerald-500': toast.type === 'success',
                        'text-rose-500': toast.type === 'error',
                        'text-blue-500': toast.type === 'info'
                    }">
                    <svg x-show="toast.type === 'success'" class="size-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="toast.type === 'error'" class="size-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>

                    <svg x-show="toast.type === 'info'" class="size-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </span>

                <p class="text-sm font-medium text-slate-700 dark:text-slate-200" x-text="toast.message"></p>

                <button @click="remove(toast.id)" class="ml-auto text-slate-400 hover:text-slate-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="absolute bottom-0 left-0 h-0.5 bg-current opacity-30 transition-all duration-[5000ms] ease-linear"
                :class="{ 'text-emerald-500': toast.type === 'success', 'text-rose-500': toast
                    .type === 'error', 'text-blue-500': toast.type === 'info' }"
                x-init="$el.style.width = '100%';
                setTimeout(() => $el.style.width = '0%', 50)">
            </div>
        </div>
    </template>
</div>
