<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Admin;
use App\Models\Role;
new #[Layout('layouts::admin.app'), Title('Authentication | Edit User')] class extends Component {
    //
    use WithFileUploads;
    public $avatar;
    public $id;
    public $user;
    public $roles;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('edit-user')) {
            abort(403);
        }
    }
    public function mount($id)
    {
        // $this->id = $id;
        $this->user = Admin::find($id);
        $this->roles = Role::all();
    }
    // #[Computed]
    // public function user()
    // {
    //     return Admin::find($this->id);
    // }
    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName);
    // }
    public function rules()
    {
        return [
            'user.display_name' => ['required'],
            'user.role_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Role::whereRaw('id = ?', [strtolower($value)])->exists();
                    if (!$exists) {
                        $fail(__('Select a role'));
                    }
                },
            ],
        ];
    }
    public function messages()
    {
        return [
            'user.display_name.required' => __('Display Name is required'),
            'user.role_id.required' => __('Role is required'),
        ];
    }
    public function save()
    {
        $this->validate();
        $this->user->save();
        return $this->redirectIntended(route('admin.user.index'), true);
    }
};
?>
<div>
    <flux:heading size="xl" level="1">{{ __('Edit User') }}</flux:heading>
    {{-- <flux:text class="mb-6 mt-2 text-xl">{{ __('Edit User') }}</flux:text> --}}
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div class="flex flex-col items-center">
            @if ($avatar)
                <div class="my-2">
                    <img src="{{ $avatar->temporaryUrl() }}" class="logo !rounded-full">
                </div>
            @else
                <div class="my-2">
                    <img src="{{ $user->avatar_url }}" class="logo !rounded-full">
                </div>
            @endif
        </div>
        <div class="flex-1 space-y-6">
            <flux:field>
                <flux:label>
                    {{ __('Display Name') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="computer-desktop" type="text" wire:model="user.display_name" />
                <flux:error name="user.display_name" />
            </flux:field>
            <flux:field>
                <flux:label>
                    {{ __('Role') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <flux:select wire:model="user.role_id">
                    <flux:select.option value="" class="text-zinc-300">Please select a role</flux:select.option>
                    @foreach ($roles as $role)
                        <flux:select.option value="{{ $role->id }}" wire:key="{{$role->id}}">{{ $role->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="user.role_id" />
            </flux:field>
            <div class="mt-6 float-right flex gap-4 nowrap">
                <flux:button variant='filled' icon="x-circle" href="{{ route('admin.user.index') }}"
                    class="cursor-default" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check-circle" class="cursor-default">
                    {{ __('Save') }}
                </flux:button>
            </div>
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
