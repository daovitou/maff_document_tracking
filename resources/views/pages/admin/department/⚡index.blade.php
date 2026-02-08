<?php
use App\Models\Department;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
new #[Layout('layouts::admin.app'), Title('Depatments | Department List')] class extends Component {
    //
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;
    public $sortDirection = 'DESC';
    public $sortField = 'created_at';

    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('view-department')) {
            abort(403);
        }
    }
    #[Computed]
    public function departments()
    {
        return Department::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
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
    public function delete($id, $status)
    {
        $department = Department::find($id);
        $department->is_active = !$status;
        $department->save();
        Flux::modal('delete-' . $id)->close();
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Department') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Department List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        @if (Gate::forUser(auth('admin')->user())->allows('create-department'))
            <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.department.create') }}"
                class="cursor-default" wire:navigate>
                {{ __('New Department') }}</flux:button>
        @endif

    </div>
    <table class="min-w-full table mt-6">
        <thead class="">
            <tr>
                <th>{{ __('Nº') }}</th>

                <th class="text-left" wire:click="doSort('gd_name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('General Department') }}" field="gd_name"
                            :sortField="$sortField" :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Department') }}" field="name" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('description')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Description') }}" field="description" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('phone')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Phone') }}" field="phone" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (count($this->departments) < 1)
                <tr>
                    <td colspan="6" class="text-center font-semibold bg-zinc-100">
                        {{ __('No Department Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->departments as $department)
                    <tr>
                        <th>{{ $loop->index + 1 }}</th>
                        <td>{{ $department->gd_name }}</td>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->description }}</td>
                        <td>{{ $department->phone }}</td>
                        <td class="flex items-center gap-3">
                            @if (Gate::forUser(auth('admin')->user())->allows('edit-department'))
                                <a href="{{ route('admin.department.edit', $department->id) }}" class="cursor-default"
                                    wire:navigate>
                                    <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                                </a>
                            @endif
                            @if (Gate::forUser(auth('admin')->user())->allows('delete-department'))
                                <x-ri-delete-bin-5-line class="w-6 h-6 text-red-500"
                                    x-on:click="$flux.modal('delete-{{ $department->id }}').show()" />
                                <flux:modal name="delete-{{ $department->id }}">
                                    <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                        {{ __('Confirm') }}
                                    </flux:heading>
                                    <flux:text class="mt-2 mb-6">{{__("Are you sure to ")}}{{__('delete')}} : {{ $department->name }}?
                                    </flux:text>
                                    <flux:button variant="danger"
                                        wire:click="delete('{{ $department->id }}','{{ $department->is_active }}')"
                                        class="float-end">
                                        {{ __('Ok') }}
                                    </flux:button>
                                </flux:modal>
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
            {{ $this->departments->links() }}
        </div>
    </div>
</div>
