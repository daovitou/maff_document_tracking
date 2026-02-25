<?php
use App\Models\Department;
use App\Models\Gd;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
new #[Layout('layouts::admin.app'), Title('Departments | Edit Department')] class extends Component {
    //
    public $department;
    public $gds;
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('edit-department')) {
            abort(403);
        }
    }
    public function mount($id)
    {
        $this->department = Department::find($id);
        $this->is_active = true;
        $q = Gd::where('is_active', true)->orderBy('name', 'asc')->get();
        $this->gds = $q->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
            ];
        });
    }
    public function rules()
    {
        return [
            'department.name' => [
                'required',
                'min:3'
                // function ($attribute, $value, $fail) {
                //     $exists = Department::whereRaw('LOWER(name) = ? AND is_active = ? AND id != ?', [strtolower($value), true, $this->department->id])->exists();
                //     if ($exists) {
                //         $fail(__('Department already exists'));
                //     }
                // },
            ],
            'department.gd_id' => ['required'],
            'department.location' => ['nullable'],
            'department.description' => ['nullable'],
            'department.phone' => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'department.name.required' => __('Department name is required'),
            'department.gd_id.required' => __('General Department is required'),
        ];
    }
    public function save()
    {
        $this->validate();
        $this->department->save();
        return $this->redirectIntended(route('admin.department.index'), true);
    }
};
?>
<div>
    <flux:heading size="xl" level="1">{{ __('Edit Department') }}</flux:heading>
    {{-- <flux:text class="mb-6 mt-2 text-xl">{{ __('Edit Department') }}</flux:text> --}}
    <flux:separator variant="subtle" class="my-6" />
    <form wire:submit.prevent="save" enctype="multipart/form-data" class="w-full max-w-lg">
        <flux:field class="mt-4">
            <flux:label>
                {{ __('General Department') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <x-searchable-select wire:model="department.gd_id" icon="building-office"
                placeholder="Select a genderal department..." :options="$this->gds" />
            <flux:error name="department.gd_id" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Department Name') }}
                <flux:badge size="xs" color="red" class="ml-1">
                    {{ __('Require') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="building-office" type="text" wire:model="department.name" />
            <flux:error name="department.name" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Description') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="chat-bubble-left-ellipsis" type="text" wire:model="department.description" />
            <flux:error name="department.description" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Location') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="map-pin" type="text" wire:model="department.location" />
            <flux:error name="department.location" />
        </flux:field>
        <flux:field class="mt-4">
            <flux:label>
                {{ __('Phone Number') }}
                <flux:badge size="xs" class="ml-1">
                    {{ __('Optional') }}
                </flux:badge>
            </flux:label>
            <flux:input icon="device-phone-mobile" type="text" wire:model="department.phone" />
            <flux:error name="department.phone" />
        </flux:field>


        <div class="mt-6 float-right flex gap-4 nowrap">
            <flux:button variant='filled' icon="x-circle" href="{{ route('admin.department.index') }}"
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
            <p class="mt-4 text-zinc-700 font-semibold animate-pulse">{{__("Processing your request")}}...</p>
        </div>
    </div>
</div>
