<?php

use Livewire\Component;

new class extends Component {
    //
    public $username;
    public $password;
    public $error;

    public function __construct()
    {
        if (Auth::guard('admin')->check()) {
            return $this->redirectIntended(route('admin.dashboard'), true);
        }
    }

    public function authenticate()
    {
        if (Auth::guard('admin')->attempt(['username' => $this->username, 'password' => $this->password, 'status' => 'active', 'deleted_at' => null])) {
            return $this->redirectIntended(route('admin.dashboard'), true);
        }
        // $this->addError('auth', 'Invalid credentials.');
        $this->error = 'Invalid credentials.';
        return;
    }
};
?>

<div class="flex flex-col items-center min-h-screen bg-zinc-100 dark:bg-zinc-900 px-6">
    <div class="p-12 flex items-center mt-36 gap-12 bg-white rounded-2xl shadow-md">

        <img src="{{ asset('assets/img/logo.png') }}" alt="logo" srcset=""
            style="width: 196px; height: 196px; margin: 0 auto; ">
        <form wire:submit="authenticate" class="w-80">
            <flux:input icon="user-circle" wire:model="username" label="{{ __('Username') }}" type="text"
                placeholder="{{ __('Username') }}" autofocus />

            <div class="space-y-2 mt-4">
                <flux:input icon="key" type="password" label="{{ __('Password') }}" wire:model="password" viewable
                    placeholder="••••••••" />
                {{-- <div class="flex justify-end">
                        <flux:link href="/forgot-password" variant="subtle" class="text-sm">
                            {{__("Forgot password")}}?
                        </flux:link>
                    </div> --}}
            </div>
            <flux:text class="text-rose-500">{{ $error }}</flux:text>

            <flux:button type="submit" variant="primary" class="w-full mt-8">
                {{ __('Sign In') }}
            </flux:button>
        </form>
    </div>
    <div wire:loading.flex wire:target="authenticate"
        class="fixed inset-0 bg-zinc-100/20 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{ __('Processing your request') }}...</p>
        </div>
    </div>
</div>
