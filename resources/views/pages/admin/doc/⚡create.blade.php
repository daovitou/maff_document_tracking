<?php
use App\Models\Document;
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
    public $send_at;
    public $send_to_options;
    public $personels;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('create-document')) {
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
            'document.description' => ['nullable'],
            'document.to_gd' => ['required'],
            'document.department_id' => ['nullable'],
            'document_file' => [
                'required',
                File::types(['pdf', 'doc', 'docx'])
                    ->min('1kb')
                    ->max(30 * 1024),
            ],
            'document.send_note' => ['nullable'],
            'send_at' => ['required'],
        ];
        if ($this->document->to_gd) {
            $results = [...$results, 'document.gd_id' => ['required'], 'document.personel_id' => ['nullable']];
        } else {
            $results = [...$results, 'document.gd_id' => ['nullable'], 'document.personel_id' => ['required']];
        }

        return $results;
    }
    public function messages()
    {
        $results = [
            'document.code.required' => __('Code is required'),
            'document.article.required' => __('Article is required'),
            'article_at.required' => __('Article At is required'),
            'document.source.required' => __('Source is required'),
            'document.to_gd.required' => __('To GD or Personel is required'),
            'document_file.required' => __('File is required'),
            'document_file.max' => __('File size allow only 20MB'),
            'document_file.upload' => __('File cannot upload, please try again later.'),
            'document.gd_id.required' => __('General Department is required'),
            'document.personel_id.required' => __('Personel is required'),
            'send_at.required' => __('Send At is required'),
        ];
        return $results;
    }
    public function mount()
    {
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

        $qpersonels = Personel::where('deleted_at', null)->orderBy('name', 'asc')->get();
        $this->personels = $qpersonels->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name . ' - ( ' . $item->position . ' ) ',
            ];
        });
        $this->document = new Document();
        $this->document->to_gd = true;
        $this->article_at = Carbon::now()->format('Y-m-d');
        $this->send_at = Carbon::now()->format('Y-m-d');
    }
    public function updatedDocumentGdId()
    {
        $this->document->department_id = null;
        $this->document->personel_id = null;
    }
    public function updatedDocumentPersonelId()
    {
        $this->document->gd_id = null;
        $this->document->department_id = null;
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
            ->where('gd_id', $this->document->gd_id ?? '')
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
        $this->validate();
        $this->document->article_at = $this->article_at;
        $this->document->send_at = $this->send_at;
        if ($this->document_file) {
            $this->document->document_file = $this->document_file->store('files', 'public');
        }
        $this->document->save();
        return $this->redirectIntended(route('admin.doc.index'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Documentation') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('New Document') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Document Code') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="text" icon="code-bracket" wire:model="document.code"
                placeholder="{{ __('Document Code') }}" />
            <flux:error name="document.code" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Article') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="text" icon="bold" wire:model="document.article" placeholder="{{ __('Article') }}" />
            <flux:error name="document.article" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Article At') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="date" max="2999-12-31" wire:model="article_at" />
            <flux:error name="article_at" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Source') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="hashtag" type="text" wire:model="document.source" />
            <flux:error name="document.source" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('To GD or Personel') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <x-searchable-select wire:model.live="document.to_gd" icon="building-office"
                placeholder="{{ __('Select send to') }}..." :options="$this->send_to_options" />
            <flux:error name="document.to_gd" />
        </flux:field>
        @if ($this->document->to_gd)
            <div wire:key="to-gd-container-true">
                <flux:field class="mt-4">
                    <flux:label>
                        {{ __('General Department') }}
                        <flux:badge size="xs" color="red" class="ml-1">
                            {{ __('Require') }}
                        </flux:badge>
                    </flux:label>
                    <x-searchable-select wire:model.live="document.gd_id" icon="building-office-2"
                        placeholder="{{ __('Select general department') }}..." :options="$this->gds" />
                    <flux:error name="document.gd_id" />
                </flux:field>
                <flux:field class="mt-4" wire:key="dept-container-{{ $this->document->gd_id }}">
                    <flux:label>
                        {{ __('Department') }}
                        <flux:badge size="xs" class="ml-1">
                            {{ __('Optional') }}
                        </flux:badge>
                    </flux:label>
                    <x-searchable-select wire:model.live="document.department_id" icon="building-office"
                        placeholder="{{ __('Select department') }}..." :options="$this->departments" />
                    <flux:error name="document.department_id" />
                </flux:field>
            </div>
        @else
            <flux:field class="mt-4" wire:key="to-gd-container-false">
                <flux:label>
                    {{ __('Personel') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <x-searchable-select wire:model.live="document.personel_id" icon="user"
                    placeholder="{{ __('Select personel') }}..." :options="$this->personels" />
                <flux:error name="document.personel_id" />
            </flux:field>
        @endif
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Admit Date') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input type="date" max="2999-12-31" wire:model="send_at" />
            <flux:error name="send_at" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Document Note') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:textarea placeholder="{{ __('Document Note') }}.." wire:model="document.send_note" />
            <flux:error name="document.send_note" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Documentation File') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <x-file-upload accept=".pdf,.doc,.docx" maxSize="20" wire:model="document_file" />
            <flux:error name="document_file" />
        </flux:field>
        <div class="mt-6 float-right flex gap-4 nowrap">
            <flux:button variant='filled' icon="x-circle" href="{{ route('admin.doc.index') }}"
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
