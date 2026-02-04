<?php
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
new #[Layout('layouts::admin.app'), Title('Profile | Change Password')] class extends Component {
    public $user;
    public $oldPassword = '';
    public $password = '';
    public $password_confirmation = '';
    public function mount()
    {
        $this->user = Admin::find(auth('admin')->user()->id);
    }
    public function rules()
    {
        return [
            'oldPassword' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
        ];
    }
    public function messages()
    {
        return [
            'oldPassword.required' => __('Old Password is required'),
            'password.required' => __('New Password is required'),
        ];
    }
    public function save()
    {
        $this->validate();
        if (!Hash::check($this->oldPassword, $this->user->password)) {
            $this->addError('oldPassword', __('The provided password does not match'));
            return;
        } else {
            $this->user->password = Hash::make($this->password);
            $this->user->save();
            return $this->redirectIntended(route('admin.dashboard'), true);
        }
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Profile') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Change Password') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Old Password') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="key" type="password" wire:model="oldPassword" viewable />

            <flux:error name="oldPassword" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('New Password') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="key" type="password" wire:model="password" id="password" viewable />
            <flux:error name="password" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Confirm Password') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="key" type="password" id="password_confirmation"
                wire:model="password_confirmation" viewable />
            <flux:error name="password_confirmation" />
        </flux:field>
        <div class="mt-6 float-right flex gap-4 nowrap">
            <flux:button variant='filled' icon="x-circle" href="{{ route('admin.dashboard') }}" class="cursor-default"
                wire:navigate>
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
