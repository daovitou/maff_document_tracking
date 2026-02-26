<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
new #[Layout('layouts::admin.app'), Title('Settings | Edit Role')] class extends Component {
    //
    public $role;
    public $permissions;
    public $selectedPermissions;
    public function __construct()
    {
        if (!Auth::guard('admin')->user()->is_system) {
            abort(403);
        }
    }
    public function rules()
    {
        return [
            'role.name' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Role::whereRaw('LOWER(name) = ? AND id != ?', [strtolower($value), $this->role->id])->exists();
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
    public function mount($id)
    {
        $this->role = Role::find($id);
        $this->permissions = Permission::all();
        $this->selectedPermissions = $this->role->permissions;
    }
    public function save()
    {
        $this->validate();
        $this->role->permissions = $this->selectedPermissions;
        $this->role->save();
        // 1. Flash the notification to the session manually
        session()->flash('notify', [
            'message' => __('Role updated successfully')
            'type' => 'success',
        ]);
        return $this->redirectIntended(route('admin.setting.role.index'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Edit Role') }}</flux:heading>
    {{-- <flux:text class="mb-6 mt-2 text-xl">{{ __('Edit Role') }}</flux:text> --}}
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full lg:w-2/3">
        <flux:field class="mt-4">
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
        <table class="table mt-6 w-full">
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
                        <flux:checkbox wire:model="selectedPermissions" value="view-user" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-user" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-user" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-user" class="block mx-auto" />
                    </td>
                </tr>
                <tr>
                    <th colspan="5" class="bg-zinc-400 text-left">
                        {{ __('Organization2') }}
                    </th>
                </tr>
                <tr>
                    <th class="text-right nowrap">
                        - {{ __('General Department') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-general-department"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-general-department"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-general-department"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-general-department"
                            class="block mx-auto" />
                    </td>
                </tr>
                <tr>
                    <th class="text-right nowrap">
                        - {{ __('Department') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-department" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-department"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-department" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-department"
                            class="block mx-auto" />
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
                        <flux:checkbox wire:model="selectedPermissions" value="view-personel" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-personel" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-personel" class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-personel" class="block mx-auto" />
                    </td>
                </tr>

                <tr>
                    <th colspan="5" class="bg-zinc-400 text-left">
                        {{ __('Documentation') }}
                    </th>
                </tr>
                <tr>
                    <th class="text-right">
                        - {{ __('Note Documentation') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-note-document"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-note-document"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-note-document"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-note-document"
                            class="block mx-auto" />
                    </td>
                </tr>
                <tr>
                    <th class="text-right">
                        - {{ __('BE Document') }}
                    </th>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="view-be-document"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="create-be-document"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="edit-be-document"
                            class="block mx-auto" />
                    </td>
                    <td>
                        <flux:checkbox wire:model="selectedPermissions" value="delete-be-document"
                            class="block mx-auto" />
                    </td>
                </tr>
            </tbody>
        </table>
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
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{ __('Processing your request') }}...</p>
        </div>
    </div>
</div>
