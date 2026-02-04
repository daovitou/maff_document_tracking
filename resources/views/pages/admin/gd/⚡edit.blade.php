<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Gd;
new #[Layout('layouts::admin.app'), Title('Departments | Edit Department')] class extends Component {
    //
    public $gd;
     public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('edit-general-department')) {
            abort(403);
        }
    }

    public function mount($id)
    {
        $this->gd = Gd::find($id);
    }
    public function rules()
    {
        return [
            'gd.name' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Gd::whereRaw('LOWER(name) = ? AND is_active = ? AND id != ?', [strtolower($value), true, $this->gd->id])->exists();
                    if ($exists) {
                        $fail(__('General Department already exists'));
                    }
                },
                'min:3',
            ],
            'gd.description' => ['nullable'],
            'gd.phone' => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'gd.name.required' => __('General Department name is required'),
            'gd.name.min' => __('General Department name must be at least 3 characters'),
        ];
    }
    public function save()
    {
        $this->validate();
        $this->validate();
        $this->gd->save();
        return $this->redirectIntended(route('admin.gd.index'), true);
    }
};
?>
<div>
    <flux:heading size="xl" level="1">{{ __('General Department') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Edit General Department') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
        <flux:field class="mt-4">
            <flux:label>
                {{ __('General Department Name') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="building-office" type="text" wire:model="gd.name" />
            <flux:error name="gd.name" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Description') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="chat-bubble-left-ellipsis" type="text" wire:model="gd.description" />
            <flux:error name="gd.description" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Phone Number') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="device-phone-mobile" type="text" wire:model="gd.phone" />
            <flux:error name="gd.phone" />
        </flux:field>
        <div class="mt-6 float-right flex gap-4 nowrap">
            <flux:button variant='filled' icon="x-circle" href="{{ route('admin.gd.index') }}" class="cursor-default"
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
