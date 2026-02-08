<?php
use App\Models\Document;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
new #[Layout('layouts::admin.app'), Title('Documentation | Document List')] class extends Component {
    //
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;
    public $sortDirection = 'DESC';
    public $sortField = 'article_at';
    public $cancel_note = '';
    public $return_note = '';
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('view-document')) {
            abort(403);
        }
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
        return Document::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
    public function cancel($id)
    {
        $doc = Document::find($id);
        $doc->status = 'បានបោះបង់';
        $doc->cancel_at = Carbon::now()->format('Y-m-d');
        $doc->cancel_note = $this->cancel_note;
        $doc->save();
        Flux::modal('cancel-' . $id)->close();
    }
    public function return($id)
    {
        $doc = Document::find($id);
        $doc->status = 'បានប្រគល់ត្រឡប់';
        $doc->return_at = Carbon::now()->format('Y-m-d');
        $doc->return_note = $this->return_note;
        $doc->save();
        Flux::modal('cancel-' . $id)->close();
    }
};
?>
<div>
    <flux:heading size="xl" level="1">{{ __('Documentation') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Document List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        @if (Gate::forUser(auth('admin')->user())->allows('create-document'))
            <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.doc.create') }}"
                class="cursor-default" wire:navigate>
                {{ __('New Document') }}</flux:button>
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
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="">
            @if (count($this->docs) < 1)
                <tr>
                    <td colspan="7" class="text-center font-semibold bg-zinc-100">
                        {{ __('No Document Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->docs as $doc)
                    <tr wire:key="{{ $doc->id }}">
                        <th class="text-left">{{ $doc->code }}</th>
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
                        <td class="flex items-center gap-3">

                            @if (Gate::forUser(auth('admin')->user())->allows('view-document'))
                                <a href="{{ route('admin.doc.view', $doc->id) }}" class="cursor-default" wire:navigate>
                                    <x-ri-eye-line class="w-6 h-6 text-amber-600" />
                                </a>
                                @if ($doc->status == 'កំពុងរងចាំ')
                                    <a href="{{ route('admin.doc.edit', $doc->id) }}" class="cursor-default"
                                        wire:navigate>
                                        <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                                    </a>
                                    {{-- ==== Cancel Action ===== --}}
                                    <x-ri-prohibited-2-line class="w-6 h-6 text-red-500"
                                        x-on:click="$flux.modal('cancel-{{ $doc->id }}').show()" />
                                    <flux:modal name="cancel-{{ $doc->id }}">
                                        <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                            {{ __('Confirm') }}
                                        </flux:heading>
                                        <flux:text class="mt-2 mb-6">{{ __('Are you sure to ') }}{{ __('cancel') }} :
                                            <span class="font-bold">{{ $doc->code }} ?</span>
                                        </flux:text>
                                        <flux:field class="my-4">
                                            <flux:label>
                                                {{ __('Document Note') }}
                                                <flux:badge size="xs" class="ml-1">
                                                    {{ __('Optional') }}
                                                </flux:badge>
                                            </flux:label>
                                            <flux:textarea rows="auto" wire:model="cancel_note" />
                                        </flux:field>
                                        <flux:button variant="danger" wire:click="cancel('{{ $doc->id }}')"
                                            class="float-end">
                                            {{ __('Confirm') }}
                                        </flux:button>
                                    </flux:modal>
                                    {{-- ==== Return Action ==== --}}
                                    <x-ri-text-wrap class="w-6 h-6 text-blue-600"
                                        x-on:click="$flux.modal('return-{{ $doc->id }}').show()" />
                                    <flux:modal name="return-{{ $doc->id }}">
                                        <flux:heading class="text-left text-lg font-bold text-green-600 ">
                                            {{ __('Confirm') }}
                                        </flux:heading>
                                        <flux:text class="mt-2 mb-6">
                                            {{ __('Code') }}
                                            <span class="font-bold"> {{ $doc->code }}</span>
                                            {{ __('is turn back') }}
                                        </flux:text>
                                        <flux:field class="my-4">
                                            <flux:label>
                                                {{ __('Document Note') }}
                                                <flux:badge size="xs" class="ml-1">
                                                    {{ __('Optional') }}
                                                </flux:badge>
                                            </flux:label>
                                            <flux:textarea rows="auto" wire:model="return_note" />
                                        </flux:field>
                                        <flux:button variant="primary" wire:click="return('{{ $doc->id }}')"
                                            class="float-end">
                                            {{ __('Confirm') }}
                                        </flux:button>
                                    </flux:modal>
                                @endif
                            @endif
                            {{-- @if (Gate::forUser(auth('admin')->user())->allows('delete-document'))
                                <x-ri-delete-bin-5-line class="w-6 h-6 text-red-500"
                                    x-on:click="$flux.modal('delete-{{ $doc->id }}').show()" />
                                <flux:modal name="delete-{{ $doc->id }}">
                                    <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                        {{ __('Confirm Delete') }}
                                    </flux:heading>
                                    <flux:text class="mt-2 mb-6">Are you sure to delete : {{ $doc->name }}?
                                    </flux:text>
                                    <flux:button variant="danger"
                                        wire:click="delete('{{ $doc->id }}','{{ $doc->status }}')"
                                        class="float-end">
                                        {{ __('Confirm') }}
                                    </flux:button>
                                </flux:modal>
                            @endif --}}

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
