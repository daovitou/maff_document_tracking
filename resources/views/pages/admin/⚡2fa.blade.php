<?php

use Livewire\Component;
use App\Models\Admin;
new class extends Component {
    //
    public $facode;
    public $error;

    public function __construct()
    {
        if (Auth::guard('admin')->check()) {
            return $this->redirectIntended(route('admin.dashboard'), true);
        }
    }

    public function authenticate()
    {
        $userId = session()->get('auth.2fa_attempted_user_id') ?? "";
        $user = Admin::where("id",$userId)->where("facode",$this->facode)->first();
        if ($user) {
            Auth::guard('admin')->login($user);
            session()->forget('auth.2fa_attempted_user_id');
            $user->facode = null;
            $user->save();
            return $this->redirectIntended(route('admin.dashboard'), true);
        }
        
        $this->error = 'Invalid 2FA Code.';
        return;
    }
};
?>

<div class="flex flex-col items-center min-h-screen bg-zinc-100 dark:bg-zinc-900 px-6">
    <div class="p-12 flex flex-col md:flex-row items-center mt-36 gap-12 bg-white rounded-2xl shadow-md">

        <img src="{{ asset('assets/img/logo.png') }}" alt="logo" srcset=""
            style="width: 196px; height: 196px; margin: 0 auto; ">
        <form wire:submit="authenticate" class="w-80">
            <flux:input icon="command-line" wire:model="facode" label="{{ __('2FA Code') }}" type="text"
                placeholder="{{ __('2FA Code') }}" autofocus />

            {{ session()->get('auth.2fa_attempted_user_id') }}
            <flux:text class="text-rose-500">{{ $error }}</flux:text>

            <flux:button type="submit" variant="primary" class="w-full mt-8">
                {{ __('Verify 2FA') }}
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
