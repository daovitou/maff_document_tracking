<?php

use App\Models\Personel;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
new #[Layout('layouts::admin.app'), Title('Personel | Personel List')] class extends Component {
    //
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;
    public $sortDirection = 'DESC';
    public $sortField = 'created_at';
    public $cancel_note = '';
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
    public function personels()
    {
        return Personel::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
    public function delete($id) {
        $personel = Personel::find($id);
        $personel->deleted_at = Carbon::today();
        $personel->save();
        Flux::modal('delete-' . $id)->close();
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Personel') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Personel List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        @if (Gate::forUser(auth('admin')->user())->allows('create-personel'))
            <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.personel.create') }}"
                class="cursor-default" wire:navigate>
                {{ __('New Personel') }}</flux:button>
        @endif
    </div>
    <table class="min-w-full table mt-6">
        <thead class="">
            <tr>
                <th class="text-left">
                    Nº
                </th>
                <th class="text-left" wire:click="doSort('name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Personel Name') }}" field="name" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('organization')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Organization') }}" field="organization"
                            :sortField="$sortField" :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('position')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Position') }}" field="position" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('phone')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Phone Number') }}" field="phone" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left">
                    {{ __('Actions') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @if (count($this->personels) < 1)
                <tr>
                    <td colspan="6" class="text-center font-semibold bg-zinc-100">
                        {{ __('No Personel Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->personels as $personel)
                    <tr>
                        <th class="text-left">{{ $loop->index + 1 }}</th>
                        <td>{{ $personel->name }}</td>
                        <td>{{ $personel->organization }}</td>
                        <td>{{ $personel->position }}</td>
                        <td>{{ $personel->phone }}</td>
                        <td class="flex items-center gap-3">
                            @if (Gate::forUser(auth('admin')->user())->allows('edit-general-department'))
                                <a href="{{ route('admin.personel.edit', $personel->id) }}" class="cursor-default" wire:navigate>
                                    <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                                </a>
                            @endif
                            @if (Gate::forUser(auth('admin')->user())->allows('delete-general-department'))
                                <x-ri-delete-bin-5-line class="w-6 h-6 text-red-500"
                                    x-on:click="$flux.modal('delete-{{ $personel->id }}').show()" />
                                <flux:modal name="delete-{{ $personel->id }}">
                                    <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                        {{ __('Confirm') }}
                                    </flux:heading>
                                    <flux:text class="mt-2 mb-6">{{ __('Are you sure to ') }}{{ __('delete') }} :
                                        {{ $personel->name }}?
                                    </flux:text>
                                    <flux:button variant="danger"
                                        wire:click="delete('{{ $personel->id }}')"
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
            {{ $this->personels->links() }}
        </div>
    </div>
</div>
