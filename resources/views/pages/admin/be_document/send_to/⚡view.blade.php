<?php
use App\Models\BeDocument;
use App\Models\BeDocumentSendTo;
use App\Models\Gd;
use App\Models\Department;
use App\Models\Personel;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
new #[Layout('layouts::admin.app'), Title('Create Document')] class extends Component {
    //
    public $doc;
    public $reciever;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('view-be-document')) {
            abort(403);
        }
    }
    public function mount($id, $send_to_id)
    {
        $this->doc = BeDocument::find($id);
        $this->reciever = BeDocumentSendTo::find($send_to_id);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Organization Personel') }}</flux:heading>
    {{-- <flux:text class="mb-6 mt-2 text-xl">{{ __('Organization Personel') }}</flux:text> --}}
    <flux:separator variant="subtle" class="my-6" />
    <table class="table">
        <thead>
            <th width="230" class="text-left">{{ __('') }}</th>
            <th class="text-left">{{ __('Description') }}</th>
        </thead>
        <tbody>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Code') }} :</th>
                <td>{{ $this->reciever->document->code }}</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Source') }} :</th>
                <td>{{ $this->reciever->document->source }}</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Article') }} :</th>
                <td>{{ $this->reciever->document->article }}</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Article At') }} :</th>
                <td>{{ Carbon::parse($this->reciever->document->article_at)->format('d/m/Y') }} ( {{ __('dmY') }}
                    )</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Documentation File') }} :</th>
                <td>
                    {{-- <a href="{{$this->doc->pdfUrl}}" target="_blank" rel="noopener noreferrer">view pdf</a> --}}
                    <flux:link href="{{ route('view-pdf', $this->doc->pdfName) }}" target="_blank" variant="ghost">
                        {{ __('View PDF') }}</flux:link>
                </td>
            </tr>
            <tr class="bg-zinc-100 border-b border-zinc-200">
                <th colspan="2" class="text-left font-lg">{{ __('Reciever') }} :</th>
            </tr>
            @if ($this->reciever->to_gd)
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Organization') }} :</th>
                    <td>
                        {{ $this->reciever->gd->name }}
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Department') }} :</th>
                    <td>
                        {{ $this->reciever->department?->name }}
                    </td>
                </tr>
            @else
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Personel') }} :</th>
                    <td>
                        {{ $this->reciever->personel->name }}
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Position') }} :</th>
                    <td>
                        {{ $this->reciever->personel->position }}
                    </td>
                </tr>
            @endif
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Send At') }} :</th>
                <td>{{ Carbon::parse($this->reciever->send_at)->format('d/m/Y') }} ( {{ __('dmY') }})</td>
            </tr>
            <tr class="border-b border-zinc-200">
                <th class="text-right">{{ __('Respect At') }} :</th>
                <td>{{ Carbon::parse($this->reciever->respect_at)->format('d/m/Y') }} ( {{ __('dmY') }})</td>
            </tr>
            @if ($reciever->status == 'បានបោះបង់')
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="zinc">{{ $this->reciever->status }}</flux:badge>
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Canceled At') }} :</th>
                    <td>
                        {{ Carbon::parse($this->reciever->cancel_at)->format('d/m/Y') }}
                    </td>
                </tr>
            @elseif ($reciever->status == 'បានប្រគល់ត្រឡប់')
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="lime">{{ $this->reciever->status }}</flux:badge>
                    </td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Returned At') }} :</th>
                    <td>{{ Carbon::parse($this->reciever->return_at)->format('d/m/Y') }} ( {{ __('dmY') }})</td>
                </tr>
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Documentation File') }} :</th>
                    <td>
                        {{-- <a href="{{$this->doc->pdfUrl}}" target="_blank" rel="noopener noreferrer">view pdf</a> --}}
                        <flux:link href="{{ route('view-pdf', $this->reciever->returnPdfName) }}" target="_blank" variant="ghost">
                            {{ __('View PDF') }}</flux:link>
                    </td>
                </tr>
            @elseif($reciever->status == 'កំពុងរងចាំ')
                <tr class="border-b border-zinc-200">
                    <th class="text-right">{{ __('Status') }} :</th>
                    <td>
                        <flux:badge size="sm" color="amber">{{ $reciever->status }}</flux:badge>
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
</div>
