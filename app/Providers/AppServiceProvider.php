<?php

namespace App\Providers;

use App\Policies\DepartmentPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\GeneralDepartmentPolicy;
use App\Policies\PersonelPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        // ======= Admin =======
        Gate::define('view-user', [UserPolicy::class, 'view']);
        Gate::define('create-user', [UserPolicy::class, 'create']);
        Gate::define('edit-user', [UserPolicy::class, 'edit']);
        Gate::define('delete-user', [UserPolicy::class, 'delete']);
        // ======= Document =======
        Gate::define('view-document', [DocumentPolicy::class, 'view']);
        Gate::define('create-document', [DocumentPolicy::class, 'create']);
        Gate::define('edit-document', [DocumentPolicy::class, 'edit']);
        Gate::define('delete-document', [DocumentPolicy::class, 'delete']);
        // ======= Department =======
        Gate::define('view-department', [DepartmentPolicy::class, 'view']);
        Gate::define('create-department', [DepartmentPolicy::class, 'create']);
        Gate::define('edit-department', [DepartmentPolicy::class, 'edit']);
        Gate::define('delete-department', [DepartmentPolicy::class, 'delete']);
        // ======= General Department =======
        Gate::define('view-general-department', [GeneralDepartmentPolicy::class, 'view']);
        Gate::define('create-general-department', [GeneralDepartmentPolicy::class, 'create']);
        Gate::define('edit-general-department', [GeneralDepartmentPolicy::class, 'edit']);
        Gate::define('delete-general-department', [GeneralDepartmentPolicy::class, 'delete']);
        // ======= Personel =======
        Gate::define('view-personel', [PersonelPolicy::class, 'view']);
        Gate::define('create-personel', [PersonelPolicy::class, 'create']);
        Gate::define('edit-personel', [PersonelPolicy::class, 'edit']);
        Gate::define('delete-personel', [PersonelPolicy::class, 'delete']);
    }
}
