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
        if (Auth::guard('admin')->attempt(['username' => $this->username, 'password' => $this->password, 'deleted_at' => null])) {
            return $this->redirectIntended(route('admin.dashboard'), true);
        }
        // $this->addError('auth', 'Invalid credentials.');
        $this->error = 'Invalid credentials.';
        return;
    }
};
?>

<div class="flex flex-col items-center justify-center min-h-screen bg-zinc-50 dark:bg-zinc-900 px-6">
    <div class="w-full max-w-sm space-y-6">
        <div class="text-center">
            <flux:heading size="xl" level="1">{{ __('Welcome back') }}</flux:heading>
            <flux:subheading>Enter your credentials to access your account</flux:subheading>
        </div>

        <x-card>
            <form wire:submit="authenticate" class="space-y-6">
                <flux:input wire:model="username" label="{{ __('Username') }}" type="text"
                    placeholder="{{ __('Username') }}" autofocus />

                <div class="space-y-2">
                    <flux:input wire:model="password" label="{{ __('Password') }}" type="password"
                        placeholder="••••••••" viewable />
                    {{-- <div class="flex justify-end">
                        <flux:link href="/forgot-password" variant="subtle" class="text-sm">
                            {{__("Forgot password")}}?
                        </flux:link>
                    </div> --}}
                </div>
                <flux:text class="text-rose-500">{{ $error }}</flux:text>

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Sign In') }}
                </flux:button>
            </form>
        </x-card>
    </div>
    <div wire:loading.flex wire:target="authenticate"
        class="fixed inset-0 bg-zinc-100/20 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{__("Processing your request")}}...</p>
        </div>
    </div>
</div>
