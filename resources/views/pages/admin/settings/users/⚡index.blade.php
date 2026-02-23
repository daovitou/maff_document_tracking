<?php
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Admin;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
new #[Layout('layouts::admin.app'), Title('Authentication | User List')] class extends Component {
    //
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;
    public $sortDirection = 'DESC';
    public $sortField = 'created_at';

    public function __construct()
    {
        if (!Auth::guard('admin')->user()->is_system) {
            abort(403);
        }
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

    public function delete($id)
    {
        $user = Admin::find($id);
        // if ($user->avatar) {
        //     Storage::disk('public')->delete($user->avatar);
        // }
        $user->deleted_at = Carbon::today();
        $user->save();
        Flux::modal('delete-' . $id)->close();
        // session()->flash('message', 'Group deleted successfully.');
        // Toaster::success('User deleted  successfully.');
    }
    #[Computed]
    public function users()
    {
        return Admin::search($this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Authentication') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('User List') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <div class="flex items-center justify-between mb-4">
        <form wire:submit="search" class="flex gap-4 items-center">
            <flux:input icon="magnifying-glass" class="max-w-sm" class:input="font-mono" type="text"
                wire:model.live.debounce.300ms="search" wire:model="search" placeholder="{{ __('Search') }}..." />
        </form>
        <flux:button variant="primary" icon='plus-circle' href="{{ route('admin.setting.users.create') }}"
            class="cursor-default" wire:navigate>
            {{ __('Create User') }}</flux:button>
    </div>

    <table class="min-w-full table mt-6">
        <thead class="">
            <tr>
                <th>{{ __('Nº') }}</th>
                <th class="text-left" wire:click="doSort('username')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Username') }}" field="username" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('role_name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Role') }}" field="role_name" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('gd_name')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Organization') }}" field="gd_name" :sortField="$sortField"
                            :sortDirection="$sortDirection" />
                    </span>
                </th>
                <th class="text-left" wire:click="doSort('email')">
                    <span class="flex items-center justify-between">
                        <x-datatable-header displayName="{{ __('Email') }}" field="email" :sortField="$sortField"
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
            @if (count($this->users) < 1)
                <tr>
                    <td colspan="7" class="text-center font-semibold bg-zinc-100">
                        {{ __('No User Found') }}
                    </td>
                </tr>
            @else
                @foreach ($this->users as $user)
                    <tr wire:key="{{ $user->id }}">
                        <th>{{ $loop->index + 1 }}</th>
                        <td class="flex items-center gap-3">
                            <flux:avatar src="{{ $user->avatar_url }}" alt="{{ $user->username }}" circle />
                            <div>
                                <span class="font-semibold">{{ __('Username') }}</span>: {{ $user->username }}
                                <br />
                                <span class="text-xs text-zinc-500">{{ __('Display Name') }}:
                                    {{ $user->display_name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->role->name }}</td>
                        <td>
                            <span class="font-semibold">{{ $user->gd->name }}</span>
                            <br>
                            {{ $user->department?->name ?? '' }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ Carbon::parse($user->created_at)->diffForHumans() }}</td>
                        <td class="flex items-center gap-3">
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="cursor-default" wire:navigate>
                                <x-ri-edit-2-line class="w-6 h-6 text-accent-content" />
                            </a>
                            <x-ri-delete-bin-5-line class="w-6 h-6 text-danger-content"
                                x-on:click="$flux.modal('delete-{{ $user->id }}').show()" />
                            <flux:modal name="delete-{{ $user->id }}">
                                <flux:heading class="text-left text-lg font-bold text-red-500 ">
                                    {{ __('Confirm') }}
                                </flux:heading>
                                <flux:text class="mt-2 mb-6">{{ __('Are you sure to ') }}{{ __('delete') }} :
                                    {{ $user->display_name }}?
                                </flux:text>
                                <flux:button variant="danger" wire:click="delete('{{ $user->id }}')"
                                    class="float-end">
                                    {{ __('Ok') }}
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
            {{ $this->users->links() }}
        </div>
    </div>
</div>
