<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Personel;
new #[Layout('layouts::admin.app'), Title('Personel | New Personel')] class extends Component {
    //
    public $personel;
    public function mount()
    {
        $this->personel = new Personel();
        $this->personel->order =1;
    }
    public function rules()
    {
        return [
            'personel.name' => ['required'],
            'personel.organization' => ['nullable'],
            'personel.position' => ['nullable'],
            'personel.phone' => ['nullable'],
            'personel.email' => ['nullable'],
            'personel.note' => ['nullable'],
            'personel.order' => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'personel.name.required' => __('Personel fullname is required'),
        ];
    }
    public function save()
    {
        $this->validate();
        $this->personel->save();
        return $this->redirectIntended(route('admin.personel.index'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Personel') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('New Personel') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
        <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Personel Fullname') }}
                    <flux:badge size="xs" color="red" class="ml-1">
                        {{ __('Require') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="user" type="text" wire:model="personel.name" />
                <flux:error name="personel.name" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Organization') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="building-office" type="text" wire:model="personel.organization" />
                <flux:error name="personel.organization" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Position') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="tag" type="text" wire:model="personel.position" />
                <flux:error name="personel.position" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Email') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="envelope" type="email" wire:model="personel.email" />
                <flux:error name="personel.email" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Phone Number') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="phone" type="text" wire:model="personel.phone" />
                <flux:error name="personel.phone" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Order') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:input icon="numbered-list" type="number" wire:model="personel.order" />
                <flux:error name="personel.order" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>
                    {{ __('Note') }}
                    <flux:badge size="xs" class="ml-1">
                        {{ __('Optional') }}
                    </flux:badge>
                </flux:label>
                <flux:textarea rows="auto" wire:model="personel.note" />
                <flux:error name="personel.note" />
            </flux:field>
            <div class="mt-6 float-right flex gap-4 nowrap">
                <flux:button variant='filled' icon="x-circle" href="{{ route('admin.personel.index') }}"
                    class="cursor-default" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check-circle" class="cursor-default">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </form>
    <div wire:loading.flex wire:target="save"
        class="fixed inset-0 bg-zinc-100/20 bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600"></div>
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{ __('Processing your request') }}...</p>
        </div>
    </div>
</div>
