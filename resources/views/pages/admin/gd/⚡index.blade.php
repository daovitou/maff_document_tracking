<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Gd;
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
        if (!Gate::forUser(auth('admin')->user())->allows('view-general-department')) {
            abort(403);
        }
    }

    #[Computed]
    public function gds()
    {
        return Gd::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
    public function delete($id, $status)
    {
        $gd = Gd::find($id);
        $gd->is_active = !$status;
        $gd->save();
        Flux::modal('delete-' . $id)->close();
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
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('General Department') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('General Department List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        @if (Gate::forUser(auth('admin')->user())->allows('create-general-department'))
            <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.gd.create') }}"
                class="cursor-default" wire:navigate>
                {{ __('New General Department') }}</flux:button>
        @endif
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
                <th class="text-left" wire:click="doSort('phone')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Phone Number') }}" field="phone" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('is_active')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Status') }}" field="is_active" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('created_at')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Created At') }}" field="created_at" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>

                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (count($this->gds) < 1)
                <tr>
                    <td colspan="7" class="text-center font-semibold bg-zinc-100">
                        {{ __('No General Department Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->gds as $gd)
                    <tr wire:key="{{ $gd->id }}">
                        <th>{{ $loop->index + 1 }}</th>
                        <td>{{ $gd->name }}</td>
                        <td>{{ $gd->description }}</td>
                        <td>{{ $gd->phone }}</td>
                        <td>
                            <flux:badge color="{{ $gd->is_active ? 'lime' : 'red' }}" size="sm">
                                {{ $gd->is_active ? 'Active' : 'Inactive' }}</flux:badge>
                        </td>
                        <td>{{ Carbon::parse($gd->created_at)->diffForHumans() }}</td>
                        <td class="flex items-center gap-3">
                            @if (Gate::forUser(auth('admin')->user())->allows('edit-general-department'))
                                <a href="{{ route('admin.gd.edit', $gd->id) }}" class="cursor-default" wire:navigate>
                                    <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                                </a>
                            @endif
                            @if (Gate::forUser(auth('admin')->user())->allows('delete-general-department'))
                                <x-ri-delete-bin-5-line class="w-6 h-6 text-red-500"
                                    x-on:click="$flux.modal('delete-{{ $gd->id }}').show()" />
                                <flux:modal name="delete-{{ $gd->id }}">
                                    <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                        {{ __('Confirm') }}
                                    </flux:heading>
                                    <flux:text class="mt-2 mb-6">{{__("Are you sure to ")}}{{__('delete')}} : {{ $gd->name }}?
                                    </flux:text>
                                    <flux:button variant="danger"
                                        wire:click="delete('{{ $gd->id }}','{{ $gd->is_active }}')"
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
            {{ $this->gds->links() }}
        </div>
    </div>
</div>
