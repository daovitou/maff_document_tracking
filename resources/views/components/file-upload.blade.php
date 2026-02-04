@props(['name', 'label' => '', 'accept' => '*', 'maxSize' => 5])

<div x-data="{
    isDropping: false,
    progress: 0,
    isUploading: false,
    fileName: '',
    fileSize: '',
    error: '',
    handleFileSelect(event) {
        this.error = '';
        const file = event.target.files[0] || event.dataTransfer.files[0];

        if (file) {
            // Convert maxSize from MB to Bytes
            const maxBytes = {{ $maxSize }} * 1024 * 1024;

            if (file.size > maxBytes) {
                this.error = `File is too large. Maximum size is {{ $maxSize }}MB.`;
                this.fileName = '';
                // Clear the input so it doesn't try to upload
                event.target.value = '';
                return;
            }

            this.fileName = file.name;
            this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
        }
    }
}" x-on:livewire-upload-start="isUploading = true; progress = 0"
    x-on:livewire-upload-finish="isUploading = false; progress = 100"
    x-on:livewire-upload-error="isUploading = false; error = 'Upload failed.'"
    x-on:livewire-upload-progress="progress = $event.detail.progress" class="w-full max-w-xl font-sans">
    <div class="flex justify-between items-end">
        <h3 class="text-gray-800 text-lg font-semibold">{{ $label }}</h3>
        {{-- <span class="text-xs text-gray-400">Max {{ $maxSize }}MB</span> --}}
    </div>

    <div @dragover.prevent="isDropping = true" @dragleave.prevent="isDropping = false"
        @drop.prevent="isDropping = false; handleFileSelect($event)"
        :class="{
            'border-blue-500 bg-blue-50/50 ring-4 ring-blue-100': isDropping,
            'border-red-300 bg-red-50': error,
            'border-gray-200 bg-white hover:border-gray-300': !isDropping && !error
        }"
        class="relative border-2 border-dashed rounded-2xl p-10 transition-all duration-300 group shadow-sm">
        <input type="file" {{ $attributes->whereStartsWith('wire:model') }} accept="{{ $accept }}"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="handleFileSelect">

        <div class="flex flex-col items-center justify-center space-y-4">
            <div :class="{
                'bg-green-100 text-green-600': progress === 100 && !error,
                'bg-red-100 text-red-600': error,
                'bg-gray-100 text-gray-400 group-hover:bg-blue-100 group-hover:text-blue-500': !error && progress < 100
            }"
                class="p-4 rounded-full transition-colors duration-300">

                <template x-if="!error && progress < 100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </template>

                <template x-if="!error && progress === 100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </template>

                <template x-if="error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
            </div>

            <div class="text-center">
                <p class="text-gray-700 font-medium text-base">
                    <span x-show="!fileName && !error">
                        <span class="text-blue-600 underline decoration-2 underline-offset-2">{{__('Click here to upload')}}</span>
                        <span class="text-xs text-gray-400"> ({{__("Max Size")}} :{{ $maxSize }}MB)</span>
                    </span>
                    <span x-show="fileName" x-text="fileName" class="text-blue-600"></span>
                    <span x-show="error" x-text="error" class="text-red-600"></span>
                </p>
            </div>

            <div x-show="isUploading || (progress > 0 && !error)" class="w-full max-w-xs mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-semibold text-gray-500" x-text="fileSize"></span>
                    <span class="text-xs font-bold text-blue-600" x-text="progress + '%'"></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-300 ease-out"
                        :style="`width: ${progress}%`"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Server side errors (Livewire Validation) --}}
    {{-- @error($attributes->wire('model')->value())
        <p class="mt-2 text-sm text-red-500 font-medium flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
        </p>
    @enderror --}}
</div>
