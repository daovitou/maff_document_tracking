<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Admin;
use App\Models\Document;
use Carbon\Carbon;
new #[Layout('layouts::admin.app'), Title('Dashboard')] class extends Component {
    //
    public $users;
    public $docs;
    public $followups;
    public function __construct() {}
    public function mount()
    {
        $this->users = Admin::where('is_system', false)->where('deleted_at', null)->get();
        $this->docs = Document::where('status', 'កំពុងរងចាំ')->get();
        // $this->followups = Document::where('status', 'followup')->get();
        $threeDaysAgo = Carbon::now()->subDays(3);
        $this->followups = Document::where('status', 'កំពុងរងចាំ')->where('article_at', '<', $threeDaysAgo)->get();
    }
};
?>
<div>
    <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Application Summary') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
        {{-- <div class="relative bg-lime-100/80 border border-lime-300 rounded-lg h-36 overflow-clip p-4 shadow-lg">
            <x-ri-user-3-line class="absolute text-lime-600 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{__('Total Users')}}:</span>
                <span class="block font-bold text-6xl text-lime-600">{{ count($this->users) }}</span>
                <flux:link href="{{ route('admin.user.index') }}" variant="subtle" class="text-sm" wire:navigate>
                    {{ __('View Users') }}</flux:link>
            </div>
        </div> --}}
        <div class="relative bg-amber-100/80 border border-amber-300 rounded-lg h-36 overflow-clip p-4 shadow-lg">
            <x-ri-file-text-line class="absolute text-amber-600 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{__('Pending Documents')}}:</span>
                <span class="block font-bold text-6xl text-amber-600">{{ count($this->docs) }}</span>
                <flux:link href="{{ route('admin.doc.index') }}" variant="subtle" class="text-sm" wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>
        <div class="relative bg-red-100/80 border border-red-300 rounded-lg h-36 overflow-clip p-4 shadow-lg">
            <x-ri-file-text-line class="absolute text-red-500 size-44 -right-12 top-0" />
            <div>
                <span class="block font-semibold text-lg">{{__('Follow Up Documents')}}:</span>
                <span class="block font-bold text-6xl text-red-500">{{ count($this->followups) }}</span>
                <flux:link href="{{ route('admin.doc.followup') }}" variant="subtle" class="text-sm" wire:navigate>
                    {{ __('View Documents') }}</flux:link>
            </div>
        </div>

    </div>
</div>
