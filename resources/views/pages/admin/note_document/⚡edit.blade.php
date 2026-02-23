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
    use WithFileUploads, WithPagination, WithoutUrlPagination;
    public $document;
    public $document_file;
    public $article_at;
    public $personels;
    public $tos;
    public $send_to_options;
    public $reciever;
    public $validateReciever;
    public $existTos;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('edit-note-document')) {
            abort(403);
        }
    }
    public function rules()
    {
        $results = [
            'document.code' => ['required'],
            'document.article' => ['required'],
            'article_at' => ['required'],
            'document.source' => ['required'],
            'document.note' => ['nullable'],
            'document_file' => [
                'nullable',
                File::types(['pdf', 'doc', 'docx'])
                    ->min('1kb')
                    ->max(30 * 1024),
            ],
        ];

        return $results;
    }
    public function messages()
    {
        $results = [
            'document.code.required' => __('Code is required'),
            'document.article.required' => __('Article is required'),
            'article_at.required' => __('Article At is required'),
            'document.source.required' => __('Source is required'),
            'document_file.max' => __('File size allow only 20MB'),
            'document_file.upload' => __('File cannot upload, please try again later.'),
        ];
        return $results;
    }
    public function clearReciever()
    {
        $this->reciever = [
            'id' => (string) Str::uuid(),
            'to_gd' => true,
            'gd' => [
                'id' => '',
                'name' => '',
            ],
            'dept' => [
                'id' => '',
                'name' => '',
            ],
            'personel' => [
                'id' => '',
                'name' => '',
                'position' => 'pn',
            ],
            'send_at' => Carbon::now()->format('Y-m-d'),
        ];
    }
    public function mount($id)
    {
        $this->document = NoteDocument::find($id);
        $this->clearReciever();
        $this->send_to_options = [
            [
                'value' => true,
                'label' => __('Organization'),
            ],
            [
                'value' => false,
                'label' => __('Personel'),
            ],
        ];

        $this->tos = [];
        $this->article_at = Carbon::now()->format('Y-m-d');
        $qpersonels = Personel::where('deleted_at', null)->orderBy('name', 'asc')->get();
        $this->personels = $qpersonels->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name . ' - ( ' . $item->position . ' ) ',
            ];
        });
        $this->existTos = NoteDocumentSendTo::where('note_document_id', $id)->get();
    }
    #[Computed]
    public function gds()
    {
        return Gd::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });
    }
    #[Computed]
    public function departments()
    {
        return Department::where('is_active', true)
            ->where('gd_id', $this->reciever['gd']['id'] ?? '')
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });
    }
    #[Computed]
    public function allDepartments()
    {
        return Department::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });
    }
    public function save()
    {
        if (!$this->document->disable) {
            $this->validate();
            $this->validateReciever = '';
            $this->document->article_at = $this->article_at;
            if ($this->document_file) {
                Storage::disk('public')->delete($this->document->document_file);
                $this->document->document_file = $this->document_file->store('files', 'public');
            }
            $this->document->save();
        }
        foreach ($this->tos as $item) {
            $rec = new NoteDocumentSendTo();
            if ($item['to_gd']) {
                $rec->note_document_id = $this->document->id;
                $rec->to_gd = true;
                $rec->gd_id = $item['gd']['id'] ?: null;
                $rec->department_id = $item['dept']['id'] ?: null;
            } else {
                $rec->to_gd = false;
                $rec->note_document_id = $this->document->id;
                $rec->personel_id = $item['personel']['id'] ?: null;
            }
            $rec->send_at = $item['send_at'];
            $rec->save();
        }
        return $this->redirectIntended(route('admin.note-document.index'), true);
    }
    public function addReciever()
    {
        $id = $this->reciever['id'];
        $rec = [
            'id' => (string) Str::uuid(),
            'to_gd' => $this->reciever['to_gd'],
        ];
        if ($rec['to_gd']) {
            $rec['gd'] = $this->reciever['gd'];
            $rec['dept'] = $this->reciever['dept'];
        } else {
            $rec['personel'] = $this->reciever['personel'];
        }
        $rec['send_at'] = $this->reciever['send_at'];
        $this->tos = [...$this->tos, $rec];
        Flux::modal('new_reciever')->close();
        $this->clearReciever();
    }
    public function deleteReciever($id)
    {
        // Wrap the array in collect() to use the filter method
        $this->tos = collect($this->tos)
            ->filter(function ($item) use ($id) {
                return $item['id'] != $id;
            })
            ->all(); // Use ->all() if you need to turn it back into a plain array
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Note Documentation') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Edit Documentation') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full xl:w-2/3">
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Document Code') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="text" icon="code-bracket" wire:model="document.code"
                placeholder="{{ __('Document Code') }}" :readonly="(bool) $this->document->disable" />
            <flux:error name="document.code" />
        </flux:field>

        <flux:field class="mt-4">
            <flux:label>
                {{ __('Article') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="text" icon="bold" wire:model="document.article" placeholder="{{ __('Article') }}"
                :readonly="$this->document->disable" />
            <flux:error name="document.article" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Article At') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="date" max="2999-12-31" wire:model="article_at"
                :readonly="(bool) $this->document->disable" />
            <flux:error name="article_at" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Source') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="hashtag" type="text" wire:model="document.source"
                :readonly="(bool) $this->document->disable" />
            <flux:error name="document.source" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Document Note') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:textarea placeholder="{{ __('Document Note') }}.." wire:model="document.note"
                :readonly="(bool) $this->document->disable" />
            <flux:error name="document.note" />
        </flux:field>
        @if ($this->document->disable)
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Documentation File') }} :
                </flux:label>
                <flux:link href="{{ route('view-pdf', $this->document->pdfName) }}" target="_blank" variant="ghost">
                    {{ __('View PDF') }}</flux:link>
                <flux:error name="document_file" />
            </flux:field>
        @else
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Old Documentation File') }} :
                </flux:label>
                <flux:link href="{{ route('view-pdf', $this->document->pdfName) }}" target="_blank" variant="ghost">
                    {{ __('View PDF') }}</flux:link>
                <flux:error name="document_file" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('New Documentation File') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <x-file-upload accept=".pdf,.doc,.docx" maxSize="20" wire:model="document_file" class="w-full"
                    :readonly="(bool) $this->document->disable" />
                <flux:error name="document_file" />
            </flux:field>
        @endif

        <table class="min-w-full table mt-6">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>{{ __('Orgianization Personel Recieve') }}</th>
                    <th>{{ __('Send At') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->existTos as $to)
                    <tr wire:key="{{ $to->id }}">
                        <th>{{ $loop->index + 1 }}</th>
                        <td>
                            @if ($to->to_gd)
                                {{ __('Organization') }}:
                                {{ $this->gds->firstWhere('value', $to->gd_id)['label'] }}
                                @if ($to->department_id != '')
                                    <br />
                                    {{ __('Department') }}:
                                    {{-- {{ $this->departments->firstWhere('value', $to['dept']['id'])['label'] }} --}}
                                    {{ $this->allDepartments->firstWhere('value', $to->department_id)['label'] }}
                                    {{-- {{ $to['dept']['id'] }} --}}
                                @endif
                            @else
                                {{ __('Personel') }}:
                                {{ $this->personels->firstWhere('value', $to->personel_id)['label'] }}
                            @endif
                        </td>
                        <td>
                            {{ Carbon::parse($to->send_at)->format('Y-m-d') }}
                        </td>
                        <td>

                        </td>
                    </tr>
                @endforeach

                @foreach ($tos as $to)
                    <tr wire:key="{{ $to['id'] }}">
                        <th>{{ $loop->index + 1 }}</th>
                        <td>
                            @if ($to['to_gd'])
                                {{ __('Organization') }}:
                                {{ $this->gds->firstWhere('value', $to['gd']['id'])['label'] }}
                                @if ($to['dept']['id'] != '')
                                    <br />
                                    {{ __('Department') }}:
                                    {{-- {{ $this->departments->firstWhere('value', $to['dept']['id'])['label'] }} --}}
                                    {{ $this->allDepartments->firstWhere('value', $to['dept']['id'])['label'] }}
                                    {{-- {{ $to['dept']['id'] }} --}}
                                @endif
                            @else
                                {{ __('Personel') }}:
                                {{ $this->personels->firstWhere('value', $to['personel']['id'])['label'] }}
                            @endif
                        </td>
                        <td>
                            {{ Carbon::parse($to['send_at'])->format('Y-m-d') }}
                        </td>
                        <td>
                            <flux:button variant="danger" icon="x-circle" size="sm"
                                wire:click="deleteReciever('{{ $to['id'] }}')" class="cursor-default">
                                {{ __('Delete') }}
                            </flux:button>
                        </td>
                    </tr>
                @endforeach
                <tr class="border-t border-zinc-300" wire:key="add-reciever">
                    <td colspan="4" class="text-center">
                        <flux:button variant="primary" icon="plus-circle"
                            x-on:click="$flux.modal('new_reciever').show()" class="cursor-default">
                            {{ __('New Send To') }}
                        </flux:button>
                        <flux:modal name="new_reciever" class="w-xl text-left">
                            <flux:field class="mt-4">
                                <flux:label>
                                    {{ __('Send At') }}
                                </flux:label>
                                <flux:input type="date" max="2999-12-31" wire:model="reciever.send_at" />
                            </flux:field>
                            <flux:field class="mt-4">
                                <flux:label>
                                    {{ __('To GD or Personel') }}
                                </flux:label>
                                <x-searchable-select wire:model.live="reciever.to_gd" icon="building-office"
                                    placeholder="{{ __('Select send to') }}..." :options="$this->send_to_options" />
                            </flux:field>
                            @if ($this->reciever['to_gd'])
                                <div wire:key="to-gd-container-true">
                                    <flux:field class="mt-4">
                                        <flux:label>
                                            {{ __('General Department') }}
                                        </flux:label>
                                        <x-searchable-select wire:model.live="reciever.gd.id" icon="building-office-2"
                                            placeholder="{{ __('Select general department') }}..." :options="$this->gds" />
                                    </flux:field>
                                    <flux:field class="mt-4"
                                        wire:key="dept-container-{{ $this->reciever['gd']['id'] }}">
                                        <flux:label>
                                            {{ __('Department') }}
                                        </flux:label>
                                        <x-searchable-select wire:model="reciever.dept.id" icon="building-office"
                                            placeholder="{{ __('Select department') }}..." :options="$this->departments" />
                                    </flux:field>
                                </div>
                            @else
                                <flux:field class="mt-4" wire:key="to-gd-container-false">
                                    <flux:label>
                                        {{ __('Personel') }}
                                    </flux:label>
                                    <x-searchable-select wire:model.live="reciever.personel.id" icon="user"
                                        placeholder="{{ __('Select personel') }}..." :options="$this->personels" />
                                </flux:field>
                            @endif
                            <flux:button variant="primary" wire:click="addReciever" class="float-end mt-8 mb-32">
                                {{ __('Add Reciever') }}
                            </flux:button>
                        </flux:modal>
                    </td>
                </tr>
            </tbody>
        </table>
        @if ($this->validateReciever)
            <span class="text-danger text-sm mt-2 font-semibold">{{ $this->validateReciever }}</span>
        @endif
        <div class="mt-6 float-right flex gap-4 nowrap">
            <flux:button variant='filled' icon="x-circle" href="{{ route('admin.note-document.index') }}"
                class="cursor-default" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check-circle" class="cursor-default">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
    <div wire:loading.flex wire:target="save"
        class="fixed inset-0 bg-zinc-100/20 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{ __('Processing your request') }}...</p>
        </div>
    </div>
</div>
