 <flux:header class="lg:hidden">
     <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
     <flux:spacer />
     <flux:dropdown position="top" align="start">
         <flux:profile avatar="{{ Auth::guard('admin')->user()->avatar_url }}" />
         <flux:menu>
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
 </flux:header>
