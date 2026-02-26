<?php
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Admin;
use App\Models\Role;
new #[Layout('layouts::admin.app'), Title('Profile | Information')] class extends Component {
    //
    use WithFileUploads;
    public $avatar;
    public $id;
    public $user;
    public $roles;
    public function mount()
    {
        $this->user = Admin::find(auth('admin')->user()->id);
        $this->roles = Role::all();
    }
    public function rules()
    {
        return [
            'user.username' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Admin::whereRaw('LOWER(username) = ? AND id != ?', [strtolower($value), $this->user->id])->exists();
                    if ($exists) {
                        $fail(__('Username already exists'));
                    }
                },
            ],
            'user.email' => ['required'],
            'user.phone' => ['nullable'],
            'avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,JPG,gif,svg'],
        ];
    }
    public function messages()
    {
        return [
            'user.username.required' => __('Username is required'),
            'user.email.required' => __('Email is required'),
            'avatar.image' => __('File must be an image'),
            'avatar.max' => __('Image size too large'),
            'avatar.mimes' => __('Invalid image type'),
        ];
    }
    public function save()
    {
        $this->validate();
        if ($this->avatar) {
            if ($this->avatar && $this->user->avatar) {
                Storage::disk('public')->delete($this->user->avatar);
            }
            $this->user->avatar = $this->avatar->store('profiles', 'public');
        }
        $this->user->save();
        session()->flash('notify', [
            'message' => __('User info updated successfully'),
            'type' => 'success',
        ]);
        return $this->redirectIntended(route('admin.dashboard'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Information') }}</flux:heading>
    {{-- <flux:text class="mb-6 mt-2 text-xl">{{ __('Information') }}</flux:text> --}}
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
                    {{ __('Phone') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="device-phone-mobile" type="text" wire:model="user.phone" />
                <flux:error name="user.phone" />
            </flux:field>
            <div class="mt-6 float-right flex gap-4 nowrap">
                <flux:button variant='filled' icon="x-circle" href="{{ route('admin.dashboard') }}"
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
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{ __('Processing your request') }}...</p>
        </div>
    </div>
</div>
