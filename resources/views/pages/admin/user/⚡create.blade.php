<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
new #[Layout('layouts::admin.app'), Title('Create User')] class extends Component {
    //
    use WithFileUploads;
    #[Validate('image|max:2048')]
    public $avatar;
    public $user;
    public $roles;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('create-user')) {
            abort(403);
        }
    }
    public function rules()
    {
        return [
            'user.display_name' => ['required'],
            'user.username' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Admin::whereRaw('LOWER(username) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail(__('Username already exists'));
                    }
                },
            ],
            'user.email' => ['required'],
            'user.password' => ['required', 'string', Password::min(8)->mixedCase()->letters()->numbers()],
            'user.role_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Role::whereRaw('id = ?', [strtolower($value)])->exists();
                    if (!$exists) {
                        $fail(__('Select a role'));
                    }
                },
            ],
            'user.phone' => ['nullable'],
            'user.avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg'],
        ];
    }
    public function messages()
    {
        return [
            'user.display_name.required' => __('Display Name is required'),
            'user.username.required' => __('Username is required'),
            'user.email.required' => __('Email is required'),
            'user.password.required' => __('Password is required'),
            'user.role_id.required' => __('Role is required'),
            'user.avatar.image' => __('File must be an image'),
            'user.avatar.max' => __('Image size too large'),
            'user.avatar.mimes' => __('Invalid image type'),
        ];
    }
    public function mount()
    {
        $this->user = new Admin();
        $this->roles = Role::all();
        $this->user->status = 'active';
    }
    public function generatePassword()
    {
        // Str::password(length, letters, numbers, symbols, space);
        $this->user->password = Str::password(rand(8, 20), true, true, true, false);
    }
    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName);
    // }
    public function save()
    {
        $this->validate();
        sleep(2);
        if ($this->avatar) {
            $this->user->avatar = $this->avatar->store('profiles', 'public');
        }
        $this->user->password = Hash::make($this->user->password);
        $this->user->save();
        return $this->redirectIntended(route('admin.user.index'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Authentication') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('New User') }}</flux:text>
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
            <flux:field>
                <flux:label class="px-5 py-2.5 rounded-lg bg-accent/10 text-accent-content hover:bg-accent/20">
                    {{ __('Profile Picture') }}
                </flux:label>
                <flux:input type="file" wire:model="avatar" class="hidden" />
                <flux:error name="user.avatar" />
            </flux:field>

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
                    {{ __('Username') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="user-circle" type="text" wire:model="user.username" />
                <flux:error name="user.username" />
            </flux:field>
            <flux:field>
                <flux:label>
                    {{ __('Email') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="envelope" type="email" wire:model="user.email" />
                <flux:error name="user.email" />
            </flux:field>
            <flux:field>
                <flux:label>
                    {{ __('Password') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="key" type="password" wire:model="user.password" viewable />
                <flux:link wire:click.prevent="generatePassword" class="text-xs float-right cursor-pointer"
                    variant="subtle">{{ __('Generate Password') }}</flux:link>
                <flux:error name="user.password" />
            </flux:field>
            <flux:field>
                <flux:label>
                    {{ __('Phone') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="device-phone-mobile" type="text" wire:model="user.phone" />
                <flux:error name="user.phone" />
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
                    {{-- <flux:select.option value="admin">Admin</flux:select.option>
                    <flux:select.option value="user">User</flux:select.option>
                    <flux:select.option value="guest">Guest</flux:select.option> --}}
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
    {{-- <div wire:loading.flex wire:target="save"
        class="fixed inset-0 bg-zinc-900 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold">{{__("Processing your request")}}...</p>
        </div>
    </div> --}}
    <div wire:loading.flex wire:target="save"
        class="fixed inset-0 bg-zinc-100/20 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{__("Processing your request")}}...</p>
        </div>
    </div>
</div>
