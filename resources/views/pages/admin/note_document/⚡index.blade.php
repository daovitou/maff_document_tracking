<?php
use App\Models\NoteDocument;
use App\Models\NoteDocumentSendTo;
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
    use WithPagination, WithoutUrlPagination, WithFileUploads;
    public $search = '';
    public $perPage = 10;
    public $sortDirection = 'DESC';
    public $sortField = 'article_at';
    public $return_note;
    public $return_at;
    public $return_file;

    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('view-note-document')) {
            abort(403);
        }
    }

    public function clearReturn()
    {
        $this->return_note = '';
        $this->return_at = Carbon::now()->format('Y-m-d');
        $this->return_file = '';
    }
    public function mount()
    {
        $this->clearReturn();
    }
    public function return($id)
    {
        $rec = NoteDocumentSendTo::find($id);
        $rec->return_note = $this->return_note;
        $rec->return_at = $this->return_at;
        if ($this->return_file) {
            $rec->return_file = $this->return_file->store('files', 'public');
        }
        $rec->status = 'បានប្រគល់ត្រឡប់';
        $rec->document->disable = true;
        $rec->document->save();
        $rec->save();
        Flux::modal('return-{{ $id }}')->close();
        $this->clearReturn();
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function search()
    {
        $this->resetPage();
    }
    public function doSort($field)
    {
        if ($this->sortField == $field) {
            $this->sortDirection = $this->sortDirection == 'ASC' ? 'DESC' : 'ASC';
            return;
        }
        $this->sortDirection = 'ASC';
        $this->sortField = $field;
    }
    #[Computed]
    public function docs()
    {
        return NoteDocument::search($this->search)
            ->whereIn('code', $this->docsMainGroup->pluck('code')->toArray())
            ->get();
    }
    #[Computed]
    public function docsMainGroup()
    {
        return NoteDocument::mainGroup($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
    public function delete($id)
    {
        $record = NoteDocumentSendTo::find($id);
        $note_document_id = $record->note_document_id;
        $record->delete();
        $afterDelete = NoteDocumentSendTo::where('note_document_id', $note_document_id)->count();
        if ($afterDelete == 0) {
            $note_document = NoteDocument::find($note_document_id);
            if ($note_document->document_file) {
                Storage::disk('public')->delete($note_document->document_file);
            }
            $note_document->delete();
        }
        Flux::modal('delete-' . $id)->close();
    }
    public $expandedRows = [];

    public function toggleRow($userId)
    {
        if (isset($this->expandedRows[$userId])) {
            unset($this->expandedRows[$userId]);
        } else {
            // To allow only one open at a time, use: $this->expandedRows = [$userId => true];
            $this->expandedRows[$userId] = true;
        }
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Note Documentation') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Document List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        @if (Gate::forUser(auth('admin')->user())->allows('create-note-document'))
            <flux:tooltip content="{{ __('New Document') }}">
                <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.note-document.create') }}"
                    class="cursor-default" wire:navigate>
                    {{ __('New Document') }}</flux:button>
            </flux:tooltip>
        @endif
    </div>
    <table class="min-w-full table mt-6">
        <thead>
            <tr class="opacity-70">
                <th class="text-left w-52">{{ __('Code') }}</th>
                <th class="text-left">{{ __('Article') }}</th>
                <th class="text-left w-44">{{ __('Article At') }}</th>
                <th class="text-left">{{ __('Source') }}</th>
                <th class="w-24"></th>
            </tr>
        </thead>
        <tbody>
            @if (count($this->docsMainGroup) < 1)
                <tr>
                    <td colspan="5" class="text-center font-semibold bg-zinc-100">
                        {{ __('No Document Found') }}
                    </td>
                </tr>
            @else
                {{-- Grouping by the unique ID of the document is safer than grouping by Code string --}}
                @foreach ($this->docs->groupBy('id') as $docId => $items)
                    @php $first = $items->first(); @endphp

                    <tr wire:click="toggleRow('{{ $docId }}')"
                        class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-white/5">
                        <th class="p-2 text-left">
                            <span
                                class="inline-block w-4 mr-1 text-zinc-500">{{ isset($expandedRows[$docId]) ? '▼' : '▶' }}</span>
                            {{ $first->code }}
                        </th>
                        <td>{{ $first->article }}</td>
                        <td>{{ Carbon::parse($first->article_at)->format('Y-m-d') }}</td>
                        <td>{{ $first->source }}</td>
                        <td>
                            <flux:tooltip content="{{ __('Edit') }}">
                                <a href="{{ route('admin.note-document.edit', $first->id) }}"
                                    class="cursor-pointer text-accent-content" wire:navigate>
                                    <x-ri-ball-pen-line class="w-6 h-6" />
                                </a>
                            </flux:tooltip>
                        </td>
                    </tr>

                    @if (isset($expandedRows[$docId]))
                        <tr wire:key="details-{{ $docId }}">
                            <td colspan="5" class="p-0 "> {{-- Colspan must match total columns --}}
                                <div
                                    class="bg-zinc-100/40 dark:bg-white/5 px-2 py-4 border-l-4 border-accent border-primary">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="opacity-70">
                                                <th class="text-left">{{ __('Reciever') }}</th>
                                                <th class="text-left w-32">{{ __('Status') }}</th>
                                                <th class="text-left w-32">{{ __('Send At') }}</th>
                                                <th class="text-right w-44">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr class="border-t border-zinc-200 dark:border-white/10">
                                                    <td class="py-2">
                                                        {{ $item->to_gd ? $item->department_name : $item->personel_name }}
                                                    </td>
                                                    <td>
                                                        @if ($item->status == 'បានបោះបង់')
                                                            <flux:badge size="sm" color="red">បានបោះបង់
                                                            </flux:badge>
                                                        @elseif ($item->status == 'បានប្រគល់ត្រឡប់')
                                                            <flux:badge size="sm" color="lime">បានប្រគល់ត្រឡប់
                                                            </flux:badge>
                                                        @elseif ($item->status == 'កំពុងរងចាំ')
                                                            <flux:badge size="sm" color="amber">កំពុងរងចាំ
                                                            </flux:badge>
                                                        @else
                                                            <flux:badge size="sm" color="red">Unknown
                                                            </flux:badge>
                                                        @endif
                                                    </td>
                                                    <td>{{ Carbon::parse($item->send_at)->format('Y-m-d') }}</td>
                                                    <td class="flex items-center gap-3">
                                                        @if (Gate::forUser(auth('admin')->user())->allows('view-note-document'))
                                                            <flux:tooltip content="{{ __('View') }}">
                                                                <a href="{{ route('admin.note-document.send-to.view', ['id' => $item->id, 'send_to_id' => $item->send_to_id]) }}"
                                                                    class="cursor-default" wire:navigate>
                                                                    <x-ri-eye-line class="w-6 h-6 text-amber-600" />
                                                                </a>
                                                            </flux:tooltip>

                                                            @if ($item->status == 'កំពុងរងចាំ')
                                                                {{-- ==== Cancel Action ===== --}}
                                                                <flux:tooltip content="{{ __('Delete') }}">
                                                                    <x-ri-delete-bin-5-line class="w-6 h-6 text-red-500"
                                                                        x-on:click="$flux.modal('delete-{{ $item->send_to_id }}').show()" />
                                                                </flux:tooltip>

                                                                <flux:modal name="delete-{{ $item->send_to_id }}">
                                                                    <flux:heading
                                                                        class="text-left text-lg font-bold text-red-500 ">
                                                                        {{ __('Confirm') }}
                                                                    </flux:heading>
                                                                    <flux:text class="mt-2 mb-6">
                                                                        {{ __('Are you sure to ') }}{{ __('Delete Code') }}
                                                                        :<span
                                                                            class="font-bold">{{ $first->code }}</span>
                                                                        <br>
                                                                        @if ($item->to_gd)
                                                                            {{ __('Reciever') }}:
                                                                            <br>
                                                                            <span
                                                                                class="font-bold">{{ $item->gd_name }}</span>
                                                                            <br>
                                                                            {{ $item->department_name }}
                                                                        @else
                                                                            {{ __('Reciever') }}:
                                                                            <br>
                                                                            <span
                                                                                class="font-bold">{{ $item->personel_name }}</span>
                                                                            <br>
                                                                            {{ $item->personel_position }}
                                                                        @endif
                                                                    </flux:text>
                                                                    <flux:button variant="danger"
                                                                        wire:click="delete('{{ $item->send_to_id }}')"
                                                                        class="float-end">
                                                                        {{ __('Delete') }}
                                                                    </flux:button>
                                                                </flux:modal>
                                                                {{-- ==== Return Action ==== --}}
                                                                <flux:tooltip content="{{ __('Returned') }}">
                                                                    <x-ri-text-wrap class="w-6 h-6 text-blue-600"
                                                                        x-on:click="$flux.modal('return-{{ $item->send_to_id }}').show()" />
                                                                </flux:tooltip>

                                                                <flux:modal name="return-{{ $item->send_to_id }}"
                                                                    class="w-xl text-left">
                                                                    <flux:heading
                                                                        class="text-left text-lg font-bold text-green-600 ">
                                                                        {{ __('Confirm Return') }}
                                                                    </flux:heading>
                                                                    <flux:text class="mt-2 mb-6">
                                                                        {{ __('Code') }} :
                                                                        <span class="font-bold">
                                                                            {{ $first->code }}</span>
                                                                        <br>
                                                                        @if ($item->to_gd)
                                                                            {{ __('Reciever') }}:
                                                                            <br>
                                                                            <span
                                                                                class="font-bold">{{ $item->gd_name }}</span>
                                                                            <br>
                                                                            {{ $item->department_name }}
                                                                        @else
                                                                            {{ __('Reciever') }}:
                                                                            <br>
                                                                            <span
                                                                                class="font-bold">{{ $item->personel_name }}</span>
                                                                            <br>
                                                                            {{ $item->personel_position }}
                                                                        @endif
                                                                    </flux:text>
                                                                    <flux:field class="mt-4">
                                                                        <flux:label>
                                                                            {{ __('Returned At') }}
                                                                            <flux:badge size="xs" color="red"
                                                                                class="ml-1">
                                                                                {{ __('Require') }}
                                                                            </flux:badge>
                                                                        </flux:label>
                                                                        <flux:input type="date" max="2999-12-31"
                                                                            wire:model="return_at" />
                                                                    </flux:field>
                                                                    <flux:field class="my-4">
                                                                        <flux:label>
                                                                            {{ __('Document Note') }}
                                                                            <flux:badge size="xs" class="ml-1">
                                                                                {{ __('Optional') }}
                                                                            </flux:badge>
                                                                        </flux:label>
                                                                        <flux:textarea rows="auto"
                                                                            wire:model="return_note" />
                                                                    </flux:field>
                                                                    <flux:field class="mt-4">
                                                                        <flux:label>
                                                                            {{ __('Documentation File') }}
                                                                        </flux:label>
                                                                        <x-file-upload accept=".pdf,.doc,.docx"
                                                                            maxSize="20" wire:model="return_file"
                                                                            class="w-full" />
                                                                    </flux:field>
                                                                    <flux:button variant="primary"
                                                                        wire:click="return('{{ $item->send_to_id }}')"
                                                                        class="float-end">
                                                                        {{ __('Save') }}
                                                                    </flux:button>
                                                                </flux:modal>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
    <div class="flex items-center justify-between mt-4 mb-2 text-sm">
        <div>
            <flex:field class="flex text-nowrap gap-2">
                <flux:select size="sm" wire:model.live='perPage'>
                    <flux:select.option>1</flux:select.option>
                    <flux:select.option>5</flux:select.option>
                    <flux:select.option>10</flux:select.option>
                    <flux:select.option>20</flux:select.option>
                    <flux:select.option>30</flux:select.option>
                    <flux:select.option>50</flux:select.option>
                    <flux:select.option>100</flux:select.option>
                </flux:select>
                <flux:label>{{ __('Items Per Page') }}</flux:label>
            </flex:field>
        </div>
        <div>
            {{ $this->docsMainGroup->links() }}
        </div>
    </div>
</div>
