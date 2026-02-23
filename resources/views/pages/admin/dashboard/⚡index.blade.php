<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Admin;
use App\Models\BeDocumentSendTo;
use App\Models\NoteDocumentSendTo;
use Carbon\Carbon;
new #[Layout('layouts::admin.app'), Title('Dashboard')] class extends Component {
    //
    public $docs;
    public $followups;
    public $beDocs;
    public $beFollowups;
    public function __construct() {}
    public function mount()
    {
        $this->docs = NoteDocumentSendTo::where('status', 'កំពុងរងចាំ')->get();
        $threeDaysAgo = Carbon::now()->subDays(3);
        $this->followups = NoteDocumentSendTo::where('status', 'កំពុងរងចាំ')->where('send_at', '<', $threeDaysAgo)->get();
        $this->beDocs= BeDocumentSendTo::where('status', 'កំពុងរងចាំ')->get();
        $this->beFollowups= BeDocumentSendTo::where('status', 'កំពុងរងចាំ')->where('respect_at', '<=', Carbon::now())->get();

    }
};
?>
<div>
    <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Application Summary') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    {{-- <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
        <div class="relative bg-amber-100/80 border border-amber-300 rounded-lg h-36 overflow-clip p-4 shadow-lg">
            <x-ri-file-text-line class="absolute text-amber-600 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{__('Pending Documents')}}:</span>
                <span class="block font-bold text-6xl text-amber-600">{{ count($this->docs) }}</span>
                <flux:link href="{{ route('admin.note-document.index') }}" variant="subtle" class="text-sm" wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
        <div class="relative bg-red-100/80 border border-red-300 rounded-lg h-36 overflow-clip p-4 shadow-lg">
            <x-ri-file-text-line class="absolute text-red-500 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{__('Follow Up Documents')}}:</span>
                <span class="block font-bold text-6xl text-red-500">{{ count($this->followups) }}</span>
                <flux:link href="{{ route('admin.note-document.followup') }}" variant="subtle" class="text-sm" wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
    </div> --}}
    <div class="relative border border-zinc-200 px-4 pb-4 pt-8 rounded-xl shadow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
        <div class="absolute -top-4 left-4 backdrop-blur-lg px-2 py-1 text-xl text-zinc-500 font-semibold">
            <span>{{__('Note Document')}}</span>
        </div>
        <div class="relative bg-amber-100/80 border border-amber-300 rounded-lg h-36 overflow-clip p-4">
            <x-ri-file-text-line class="absolute text-amber-600 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{ __('Pending Documents') }}:</span>
                <span class="block font-bold text-6xl text-amber-600">{{ count($this->docs) }}</span>
                <flux:link href="{{ route('admin.note-document.index') }}" variant="subtle" class="text-sm"
                    wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
        <div class="relative bg-red-100/80 border border-red-300 rounded-lg h-36 overflow-clip p-4">
            <x-ri-file-text-line class="absolute text-red-500 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{ __('Follow Up Documents') }}:</span>
                <span class="block font-bold text-6xl text-red-500">{{ count($this->followups) }}</span>
                <flux:link href="{{ route('admin.note-document.followup') }}" variant="subtle" class="text-sm"
                    wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
    </div>
    <div class="relative mt-12 border border-zinc-200 px-4 pb-4 pt-8 rounded-xl shadow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
        <div class="absolute -top-4 left-4 backdrop-blur-lg px-2 py-1 text-xl text-zinc-500 font-semibold">
            <span>{{__('BE Document')}}</span>
        </div>
        <div class="relative bg-amber-100/80 border border-amber-300 rounded-lg h-36 overflow-clip p-4">
            <x-ri-file-text-line class="absolute text-amber-600 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{ __('Pending Documents') }}:</span>
                <span class="block font-bold text-6xl text-amber-600">{{ count($this->beDocs) }}</span>
                <flux:link href="{{ route('admin.be-document.index') }}" variant="subtle" class="text-sm"
                    wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
        <div class="relative bg-red-100/80 border border-red-300 rounded-lg h-36 overflow-clip p-4">
            <x-ri-file-text-line class="absolute text-red-500 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{ __('Follow Up Documents') }}:</span>
                <span class="block font-bold text-6xl text-red-500">{{ count($this->beFollowups) }}</span>
                <flux:link href="{{ route('admin.be-document.followup') }}" variant="subtle" class="text-sm"
                    wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
    </div>
</div>
