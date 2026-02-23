 <flux:sidebar sticky collapsible="mobile"
     class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
     <flux:sidebar.header>
         <flux:sidebar.brand href="#" logo="{{ asset('images/logo.jpg') }}"
             logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png" name="{{ __('Document Application') }}" />
         <flux:sidebar.collapse class="lg:hidden" />
     </flux:sidebar.header>
     <flux:sidebar.nav>
         <flux:sidebar.item icon="home" href="{{ route('admin.dashboard') }}"
             :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
         @if (Gate::forUser(auth('admin')->user())->allows('view-note-document'))
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.note-document.*')" icon="document-text"
                 heading="{{ __('Note Document') }}" class="grid">
                 @if (Gate::forUser(auth('admin')->user())->allows('view-note-document'))
                     <flux:sidebar.item href="{{ route('admin.note-document.index') }}"
                         :current="request()->routeIs('admin.note-document.index')" wire:navigate>
                         {{ __('Document List') }}
                     </flux:sidebar.item>
                     <flux:sidebar.item href="{{ route('admin.note-document.followup') }}"
                         :current="request()->routeIs('admin.note-document.followup')" wire:navigate>
                         {{ __('Follow Up Document List') }}
                     </flux:sidebar.item>
                 @endif
                 @if (Gate::forUser(auth('admin')->user())->allows('create-note-document'))
                     <flux:sidebar.item href="{{ route('admin.note-document.create') }}"
                         :current="request()->routeIs('admin.note-document.create')" wire:navigate>
                         {{ __('New Document') }}
                     </flux:sidebar.item>
                 @endif
             </flux:sidebar.group>
         @endif
         @if (Gate::forUser(auth('admin')->user())->allows('view-be-document'))
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.be-document.*')" icon="document-text"
                 heading="{{ __('BE Document') }}" class="grid">
                 @if (Gate::forUser(auth('admin')->user())->allows('view-be-document'))
                     <flux:sidebar.item href="{{ route('admin.be-document.index') }}"
                         :current="request()->routeIs('admin.be-document.index')" wire:navigate>
                         {{ __('Document List') }}
                     </flux:sidebar.item>
                      <flux:sidebar.item href="{{ route('admin.be-document.followup') }}"
                         :current="request()->routeIs('admin.be-document.followup')" wire:navigate>
                         {{ __('Follow Up Document List') }}
                     </flux:sidebar.item>
                 @endif
                 @if (Gate::forUser(auth('admin')->user())->allows('create-be-document'))
                     <flux:sidebar.item href="{{ route('admin.be-document.create') }}"
                         :current="request()->routeIs('admin.be-document.create')" wire:navigate>
                         {{ __('New Document') }}
                     </flux:sidebar.item>
                 @endif
             </flux:sidebar.group>
         @endif
         @if (Gate::forUser(auth('admin')->user())->allows('view-user'))
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.user.*')" icon="shield-check"
                 heading="{{ __('Authentication') }}" class="grid">
                 @if (Gate::forUser(auth('admin')->user())->allows('view-user'))
                     <flux:sidebar.item href="{{ route('admin.user.index') }}"
                         :current="request()->routeIs('admin.user.index')" wire:navigate>{{ __('User List') }}
                     </flux:sidebar.item>
                 @endif
                 @if (Gate::forUser(auth('admin')->user())->allows('create-user'))
                     <flux:sidebar.item href="{{ route('admin.user.create') }}"
                         :current="request()->routeIs('admin.user.create')" wire:navigate>{{ __('New User') }}
                     </flux:sidebar.item>
                 @endif
             </flux:sidebar.group>
         @endif
         @if (Gate::forUser(auth('admin')->user())->allows('view-department'))
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.department.*')" icon="building-office"
                 heading="{{ __('Department') }}" class="grid">
                 @if (Gate::forUser(auth('admin')->user())->allows('view-department'))
                     <flux:sidebar.item href="{{ route('admin.department.index') }}"
                         :current="request()->routeIs('admin.department.index')" wire:navigate>
                         {{ __('Department List') }}
                     </flux:sidebar.item>
                 @endif
                 @if (Gate::forUser(auth('admin')->user())->allows('create-department'))
                     <flux:sidebar.item href="{{ route('admin.department.create') }}"
                         :current="request()->routeIs('admin.department.create')" wire:navigate>
                         {{ __('New Department') }}
                     </flux:sidebar.item>
                 @endif
             </flux:sidebar.group>
         @endif
         @if (Gate::forUser(auth('admin')->user())->allows('view-general-department'))
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.gd.*')" icon="building-office-2"
                 heading="{{ __('General Department') }}" class="grid">
                 @if (Gate::forUser(auth('admin')->user())->allows('view-general-department'))
                     <flux:sidebar.item href="{{ route('admin.gd.index') }}"
                         :current="request()->routeIs('admin.gd.index')" wire:navigate>
                         {{ __('General Department List') }}
                     </flux:sidebar.item>
                 @endif
                 @if (Gate::forUser(auth('admin')->user())->allows('create-general-department'))
                     <flux:sidebar.item href="{{ route('admin.gd.create') }}"
                         :current="request()->routeIs('admin.gd.create')" wire:navigate>
                         {{ __('New General Department') }}
                     </flux:sidebar.item>
                 @endif
             </flux:sidebar.group>
         @endif
         @if (Gate::forUser(auth('admin')->user())->allows('view-personel'))
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.personel.*')" icon="user"
                 heading="{{ __('Personel') }}" class="grid">
                 @if (Gate::forUser(auth('admin')->user())->allows('view-personel'))
                     <flux:sidebar.item href="{{ route('admin.personel.index') }}"
                         :current="request()->routeIs('admin.personel.index')" wire:navigate>
                         {{ __('Personel List') }}
                     </flux:sidebar.item>
                 @endif
                 @if (Gate::forUser(auth('admin')->user())->allows('create-personel'))
                     <flux:sidebar.item href="{{ route('admin.personel.create') }}"
                         :current="request()->routeIs('admin.personel.create')" wire:navigate>
                         {{ __('New Personel') }}
                     </flux:sidebar.item>
                 @endif
             </flux:sidebar.group>
         @endif
         @if (Auth::guard('admin')->user()->is_system)
             <flux:sidebar.group expandable :expanded="request()->routeIs('admin.setting.*')" icon="cog-8-tooth"
                 heading="{{ __('Configuration') }}" class="grid">
                 <flux:sidebar.item href="{{ route('admin.setting.role.index') }}" wire:navigate>{{ __('Role') }}
                 </flux:sidebar.item>
                 {{-- <flux:sidebar.item href="{{ route('admin.setting.users.index') }}" wire:navigate>
                     {{ __('User List') }}</flux:sidebar.item> --}}
             </flux:sidebar.group>
         @endif
     </flux:sidebar.nav>
     <flux:sidebar.spacer />
     {{-- <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
        </flux:sidebar.nav> --}}
     <flux:dropdown position="top" align="start" class="max-lg:hidden">
         <flux:sidebar.profile avatar="{{ Auth::guard('admin')->user()->avatar_url }}"
             name="{{ Auth::guard('admin')->user()->display_name }}" />
         <flux:menu>
             {{-- <flux:menu.radio.group>
                 <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                 <flux:menu.radio>Truly Delta</flux:menu.radio>
             </flux:menu.radio.group>
             <flux:menu.separator /> --}}
             <a href={{ route('admin.profile') }} wire:navigate>
                 <flux:menu.item icon="user-circle">{{ __('Profile') }}</flux:menu.item>
             </a>
             <a href={{ route('admin.changepassword') }} wire:navigate>
                 <flux:menu.item icon="key">{{ __('Change Password') }}</flux:menu.item>
             </a>
             <a href={{ route('admin.signout') }} wire:navigate>
                 <flux:menu.item icon="arrow-right-start-on-rectangle">{{ __('Sign Out') }}</flux:menu.item>
             </a>
         </flux:menu>
     </flux:dropdown>
 </flux:sidebar>
