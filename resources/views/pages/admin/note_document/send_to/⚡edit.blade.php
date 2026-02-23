<?php
use App\Models\NoteDocument;
use App\Models\Gd;
use App\Models\Department;
use App\Models\Personel;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
new #[Layout('layouts::admin.app'), Title('Create Document')] class extends Component {
    //
    use WithFileUploads;
    
    public function __construct()
    {
        if (!Gate::forUser(auth('admin')->user())->allows('edit-note-document')) {
            abort(403);
        }
    }
    public function rules()
    {
        return [];
    }
    public function messages()
    {
        return [];
    }
    public function save()
    {
        $this->validate();
        return $this->redirectIntended(route('admin.note-document.index'), true);
    }
};
?>

<div>
    <flux:heading size="xl" level="1">{{ __('Note Documentation') }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-xl">{{ __('Edit Documentation') }}</flux:text>
    <flux:separator variant="subtle" class="my-6" />
</div>
