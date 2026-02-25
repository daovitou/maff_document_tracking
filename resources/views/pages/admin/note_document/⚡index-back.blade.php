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
        return NoteDocument::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
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
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Note Documentation List') }}</flux:heading>
    {{-- <flux:text class="mb-6 mt-2 text-xl">{{ __('Document List') }}</flux:text> --}}
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
        <thead class="">
            <tr>
                <th class="text-left" wire:click="doSort('code')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Code') }}" field="code" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('article')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Article') }}" field="article" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('source')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Source') }}" field="source" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('gd_name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Organization Personel') }}" field="gd_name"
                            :sortField="$sortField" :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('status')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Status') }}" field="status" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('article_at')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Article At') }}" field="article_at" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('send_at')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Send At') }}" field="send_at" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="">
            @if (count($this->docs) < 1)
                <tr>
                    <td colspan="8" class="text-center font-semibold bg-zinc-100">
                        {{ __('No Document Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->docs as $doc)
                    <tr wire:key="{{ $doc->send_to_id }}">
                        <th class="text-left">
                            <flux:tooltip content="{{ __('Edit') }}">
                                <a href="{{ route('admin.note-document.edit', $doc->id) }}"
                                    class="cursor-pointer hover:text-primary-content/20 underline flex items-center gap-0.5" wire:navigate>
                                    {{ $doc->code }}
                                     <x-ri-ball-pen-line class="w-4 h-4"/>
                                </a>
                            </flux:tooltip>
                        </th>
                        <td>{{ $doc->article }}</td>
                        <td>{{ $doc->source }}</td>
                        <td>
                            @if ($doc->to_gd)
                                <span class="block font-bold">
                                    {{ $doc->gd_name }}
                                </span>
                                <span class="block text-sm mt-1">
                                    {{ $doc->department_name }}
                                </span>
                            @else
                                <span class="block font-bold">
                                    {{ $doc->personel_name }}
                                </span>
                                <span class="block text-sm mt-1">
                                    {{ $doc->personel_position }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if ($doc->status == 'បានបោះបង់')
                                <flux:badge size="sm" color="red">បានបោះបង់</flux:badge>
                            @elseif ($doc->status == 'បានប្រគល់ត្រឡប់')
                                <flux:badge size="sm" color="lime">បានប្រគល់ត្រឡប់</flux:badge>
                            @elseif ($doc->status == 'កំពុងរងចាំ')
                                <flux:badge size="sm" color="amber">កំពុងរងចាំ</flux:badge>
                            @else
                                <flux:badge size="sm" color="red">Unknown</flux:badge>
                            @endif
                        </td>
                        <td>{{ Carbon::parse($doc->article_at)->format('Y-m-d') }}</td>
                        <td>{{ Carbon::parse($doc->send_at)->format('Y-m-d') }}</td>
                        <td class="flex items-center gap-3">
                            @if (Gate::forUser(auth('admin')->user())->allows('view-note-document'))
                                <flux:tooltip content="{{ __('View') }}">
                                    <a href="{{ route('admin.note-document.send-to.view', ['id' => $doc->id, 'send_to_id' => $doc->send_to_id]) }}"
                                        class="cursor-default" wire:navigate>
                                        <x-ri-eye-line class="w-6 h-6 text-amber-600" />
                                    </a>
                                </flux:tooltip>

                                @if ($doc->status == 'កំពុងរងចាំ')
                                    {{-- <a href="{{ route('admin.note-document.send-to.edit', ['id' => $doc->id,'send_to_id'=>$doc->send_to_id]) }}" class="cursor-default" wire:navigate>
                                        <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                                    </a> --}}

                                    {{-- ==== Cancel Action ===== --}}
                                    <flux:tooltip content="{{ __('Delete') }}">
                                        <x-ri-delete-bin-5-line class="w-6 h-6 text-red-500"
                                            x-on:click="$flux.modal('delete-{{ $doc->send_to_id }}').show()" />
                                    </flux:tooltip>

                                    <flux:modal name="delete-{{ $doc->send_to_id }}">
                                        <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                            {{ __('Confirm') }}
                                        </flux:heading>
                                        <flux:text class="mt-2 mb-6">
                                            {{ __('Are you sure to ') }}{{ __('Delete Code') }} :<span
                                                class="font-bold">{{ $doc->code }}</span>
                                            <br>
                                            @if ($doc->to_gd)
                                                {{ __('Reciever') }}:
                                                <br>
                                                <span class="font-bold">{{ $doc->gd_name }}</span>
                                                <br>
                                                {{ $doc->department_name }}
                                            @else
                                                {{ __('Reciever') }}:
                                                <br>
                                                <span class="font-bold">{{ $doc->personel_name }}</span>
                                                <br>
                                                {{ $doc->personel_position }}
                                            @endif
                                        </flux:text>
                                        <flux:button variant="danger" wire:click="delete('{{ $doc->send_to_id }}')"
                                            class="float-end">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </flux:modal>
                                    {{-- ==== Return Action ==== --}}
                                    <flux:tooltip content="{{ __('Returned') }}">
                                        <x-ri-text-wrap class="w-6 h-6 text-blue-600"
                                            x-on:click="$flux.modal('return-{{ $doc->send_to_id }}').show()" />
                                    </flux:tooltip>

                                    <flux:modal name="return-{{ $doc->send_to_id }}" class="w-xl text-left">
                                        <flux:heading class="text-left text-lg font-bold text-green-600 ">
                                            {{ __('Confirm Return') }}
                                        </flux:heading>
                                        <flux:text class="mt-2 mb-6">
                                            {{ __('Code') }} :
                                            <span class="font-bold"> {{ $doc->code }}</span>
                                            <br>
                                            @if ($doc->to_gd)
                                                {{ __('Reciever') }}:
                                                <br>
                                                <span class="font-bold">{{ $doc->gd_name }}</span>
                                                <br>
                                                {{ $doc->department_name }}
                                            @else
                                                {{ __('Reciever') }}:
                                                <br>
                                                <span class="font-bold">{{ $doc->personel_name }}</span>
                                                <br>
                                                {{ $doc->personel_position }}
                                            @endif
                                        </flux:text>
                                        <flux:field class="mt-4">
                                            <flux:label>
                                                {{ __('Returned At') }}
                                                <flux:badge size="xs" color="red" class="ml-1">
                                                    {{ __('Require') }}
                                                </flux:badge>
                                            </flux:label>
                                            <flux:input type="date" max="2999-12-31" wire:model="return_at" />
                                        </flux:field>
                                        <flux:field class="my-4">
                                            <flux:label>
                                                {{ __('Document Note') }}
                                                <flux:badge size="xs" class="ml-1">
                                                    {{ __('Optional') }}
                                                </flux:badge>
                                            </flux:label>
                                            <flux:textarea rows="auto" wire:model="return_note" />
                                        </flux:field>
                                        <flux:field class="mt-4">
                                            <flux:label>
                                                {{ __('Documentation File') }}
                                            </flux:label>
                                            <x-file-upload accept=".pdf,.doc,.docx" maxSize="20"
                                                wire:model="return_file" class="w-full" />
                                        </flux:field>
                                        <flux:button variant="primary" wire:click="return('{{ $doc->send_to_id }}')"
                                            class="float-end">
                                            {{ __('Save') }}
                                        </flux:button>
                                    </flux:modal>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <div class="flex items-center justify-between mt-4 mb-2 text-sm">
        <div>
            <flex:field class="flex text-nowrap gap-2">
                <flux:select size="sm" wire:model.live='perPage'>
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
            {{ $this->docs->links() }}
        </div>
    </div>
</div>
