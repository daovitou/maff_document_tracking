<?php

use Livewire\Component;
use App\Models\Document;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
new #[Layout('layouts::admin.app'), Title('Documentation | Document Details')] class extends Component {
    //
    public $doc;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('view-document')) {
            abort(403);
        }
    }
    public function mount($id)
    {
        $this->doc = Document::find($id);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Documentation') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Document Details') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <table class="table">
        <thead>
            <th width="230" class="text-left">{{ __('') }}</th>
            <th class="text-left">{{ __('Description') }}</th>
        </thead>
        <tbody>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Code') }} :</th>
                <td>{{ $this->doc->code }}</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Article At') }} :</th>
                <td>{{ Carbon::parse($this->doc->article_at)->format('d/m/Y') }}</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Article') }} :</th>
                <td>{{ $this->doc->article }}</td>
            </tr>
            @if ($doc->to_gd)
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('To Organization') }} :</th>
                    <td>{{ $this->doc->gd?->name ?? '' }}</td>
                </tr>
                @if ($this->doc->department?->name)
                    <tr class="border-b border-zinc-200">
                        <th class="text-right"></th>
                        <td>{{ $this->doc->department?->name ?? '' }}</td>
                    </tr>
                @endif
            @else
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('To Personel') }} :</th>
                    <td>
                        {{ $this->doc->personel?->name ?? '' }}
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Organization') }} :</th>
                    <td>
                        {{ $this->doc->personel?->organization ?? '' }}
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Position') }} :</th>
                    <td>
                        {{ $this->doc->personel?->position ?? '' }}
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Phone Number') }} :</th>
                    <td>
                        {{ $this->doc->personel?->phone ?? '' }}
                    </td>
                </tr>
            @endif

            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Document File') }} :</th>
                <td>
                    {{-- <a href="{{$this->doc->pdfUrl}}" target="_blank" rel="noopener noreferrer">view pdf</a> --}}
                    <flux:link href="{{ route('view-pdf', $this->doc->pdfName) }}" target="_blank" variant="ghost">
                        {{ __('View PDF') }}</flux:link>
                </td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Document Note') }} :</th>
                <td>{{ $this->doc->send_note }}</td>
            </tr>
            @if ($doc->status == 'បានបោះបង់')
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="zinc">{{ $doc->status }}</flux:badge>
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Canceled At') }} :</th>
                    <td>
                        {{ Carbon::parse($this->doc->cancel_at)->format('d/m/Y') }}
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Cancel note') }} :</th>
                    <td>{{ $this->doc->cancel_note }}</td>
                </tr>
            @elseif ($doc->status == 'បានប្រគល់ត្រឡប់')
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="lime">{{ $doc->status }}</flux:badge>
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Returned At') }} :</th>
                    <td>
                        {{ Carbon::parse($this->doc->return_at)->format('d/M/Y') }}
                    </td>
                </tr>

                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Return note') }} :</th>
                    <td>{{ $this->doc->return_note }}</td>
                </tr>
            @elseif ($doc->status == 'កំពុងរងចាំ')
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="amber">{{ $doc->status }}</flux:badge>
                    </td>
                </tr>
            @else
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="red">{{ __('Unknown') }}</flux:badge>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    {{-- <x-pdf-viewport url="{{ $this->doc->pdfUrl }}" /> --}}
    {{-- <span>{{ $this->doc->pdfName }}</span> --}}
</div>
