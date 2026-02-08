<?php
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Role;
new #[Layout('layouts::admin.app'), Title('Settings | Role List')] class extends Component {
    //
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;
    public $sortDirection = 'DESC';
    public $sortField = 'created_at';
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

    public function delete($id,$status) {
        $role = Role::find($id);
        $role->is_active = !$status;
        $role->save();
        Flux::modal('delete-' . $id)->close();
    }
    #[Computed]
    public function roles()
    {
        return Role::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Role List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.setting.role.create') }}"
            class="cursor-default" wire:navigate>
            {{ __('New Role') }}</flux:button>
    </div>
    <table class="min-w-full table mt-6">
        <thead class="">
            <tr>
                <th>{{ __('Nº') }}</th>
                <th class="text-left" wire:click="doSort('name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Name') }}" field="name" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('description')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Description') }}" field="description" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('is_active')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Status') }}" field="is_active" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (count($this->roles) < 1)
                <tr>
                    <td colspan="6" class="text-center font-semibold bg-zinc-100">
                        {{ __('No Role Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->roles as $role)
                    <tr wire:key="{{$role->id}}">
                        <th>{{ $loop->index + 1 }}</th>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->description }}</td>
                        <td> <flux:badge color="{{$role->is_active ? 'lime' : 'red'}}" size="sm">{{ $role->is_active ? 'Active' : 'Inactive' }}</flux:badge></td>
                        <td class="flex items-center gap-3">
                            <a href="{{ route('admin.setting.role.edit', $role->id) }}" class="cursor-default"
                                wire:navigate>
                                <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                            </a>
                            <x-ri-loop-right-line class="w-6 h-6 text-blue-600"
                                x-on:click="$flux.modal('delete-{{ $role->id }}').show()" />
                            <flux:modal name="delete-{{ $role->id }}">
                                <flux:heading class="text-left text-lg font-bold text-amber-500 ">
                                    {{ __('Confirm Status') }}
                                </flux:heading>
                                <flux:text class="mt-2 mb-6">Are you sure to switch status on : {{ $role->name }}?
                                </flux:text>
                                <flux:button variant="danger" wire:click="delete('{{ $role->id }}','{{ $role->is_active }}')"
                                    class="float-end">
                                    {{ __('Confirm') }}
                                </flux:button>
                            </flux:modal>
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
            {{ $this->roles->links() }}
        </div>
    </div>
</div>
