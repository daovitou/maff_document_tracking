<div
    x-data="{ 
        toasts: [],
        add(e) {
            const id = Date.now();
            this.toasts.push({
                id: id,
                type: e.detail.type || 'info',
                message: e.detail.message
            });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event)"
    class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-full max-w-sm"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative p-4 rounded-xl border shadow-lg flex items-start gap-3 bg-white"
            :class="{
                'border-emerald-200 bg-emerald-50 text-emerald-800': toast.type === 'success',
                'border-rose-200 bg-rose-50 text-rose-800': toast.type === 'error',
                'border-blue-200 bg-blue-50 text-blue-800': toast.type === 'info'
            }"
        >
            <div class="flex-1 text-sm font-medium" x-text="toast.message"></div>
            
            <button @click="remove(toast.id)" class="shrink-0 hover:opacity-70">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>
</div>