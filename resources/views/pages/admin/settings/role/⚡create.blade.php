<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Role;
use App\Models\Permission;

new #[Layout('layouts::admin.app'), Title('Settings | New Role')] class extends Component {
    //
    public $role;
    public $permissions;
    public $selectedPermissions;
    public function __construct() {}
    public function rules()
    {
        return [
            'role.name' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Role::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail(__('Role already exists'));
                    }
                },
            ],
            'role.description' => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'role.name.required' => __('Role name is required'),
        ];
    }
    public function mount()
    {
        $this->role = new Role();
        $this->role->is_active = true;
        $this->permissions = Permission::all();
        $this->selectedPermissions = [];
    }
    public function save()
    {
        $this->validate();
        $this->role->permissions = $this->selectedPermissions;
        $this->role->save();
        return $this->redirectIntended(route('admin.setting.role.index'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('New Role') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
        <flux:field>
            <flux:label>
                {{ __('Role Name') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="computer-desktop" type="text" wire:model="role.name" />
            <flux:error name="role.name" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Description') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="chat-bubble-left-ellipsis" type="text" wire:model="role.description" />
            <flux:error name="role.description" />
        </flux:field>
        <table class="table mt-6 max-w-lg">
            <thead class="">
                <tr>
                    <th>
                        {{ __('Module') }}
                    </th>
                    <th>View</th>
                    <th>Create</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <th colspan="5" class="bg-zinc-400 text-left">
                        {{ __('Authentication') }}
                    </th>
                </tr>
                <tr>
                    <th class="text-right">
                        - {{ __('User') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-user" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-user" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-user" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-user" />
                    </td>
                </tr>


                <tr>
                    <th colspan="5" class="bg-zinc-400 text-left">
                        {{ __('Organization') }}
                    </th>
                </tr>
                <tr>
                    <th class="text-right nowrap">
                        - {{ __('General Department') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-general-department" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-general-department" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-general-department" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-general-department" />
                    </td>
                </tr>
                <tr>
                    <th class="text-right nowrap">
                        - {{ __('Department') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-department" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-department" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-department" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-department" />
                    </td>
                </tr>

                <tr>
                    <th colspan="5" class="bg-zinc-400 text-left">
                        {{ __('Personel') }}
                    </th>
                </tr>
                <tr>
                    <th class="text-right">
                        - {{ __('Personel') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-personel" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-personel" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-personel" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-personel" />
                    </td>
                </tr>

                <tr>
                    <th colspan="5" class="bg-zinc-400 text-left">
                        {{ __('Documentation') }}
                    </th>
                </tr>
                <tr>
                    <th class="text-right">
                        - {{ __('Document') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-document" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-document" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-document" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-document" />
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- 
        <hr class="border-zinc-200 dark:border-zinc-700 mt-4" />
         <flux:text>Selected: {{ json_encode($selectedPermissions) }}</flux:text>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Permissions') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($permissions as $permission)
                    <flux:checkbox wire:model="selectedPermissions" wire:key="{{$permission->id}}" value="{{ $permission->slug }}"
                        label="{{ ucfirst($permission->name) }}" />
                @endforeach
            </div>
        </flux:field> 
        --}}
        <div class="mt-6 float-right flex gap-4 nowrap">
            <flux:button variant='filled' icon="x-circle" href="{{ route('admin.setting.role.index') }}"
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
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{__("Processing your request")}}...</p>
        </div>
    </div>
</div>
