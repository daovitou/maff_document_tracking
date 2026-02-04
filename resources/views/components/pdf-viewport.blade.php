@props([
    'url', 
    'height' => '800px', 
    'downloadable' => true
])

<div {{ $attributes->merge(['class' => 'pdf-container border rounded-lg overflow-hidden bg-gray-100']) }}>
    @if($downloadable)
        <div class="bg-gray-200 p-2 flex justify-end border-b">
            <a href="{{ $url }}" download class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Store-down-arrow-icon..." />
                </svg>
                Download Document
            </a>
        </div>
    @endif

    <div style="height: {{ $height }};">
        <object 
            data="{{ $url }}#toolbar=1" 
            type="application/pdf" 
            class="w-full h-full"
        >
            <div class="flex flex-col items-center justify-center h-full p-10 text-center">
                <p class="text-gray-600 mb-4">It looks like your browser doesn't support embedded PDFs.</p>
                <a href="{{ $url }}" class="px-4 py-2 bg-blue-600 text-white rounded">View PDF Instead</a>
            </div>
        </object>
    </div>
</div>