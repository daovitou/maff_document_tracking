<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, HasUuids, Notifiable;

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/avatar.jpg');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, "role_id", "id");
    }
    public function gd()
    {
        return $this->belongsTo(Gd::class, "gd_id", "id");
    }
    public function department()
    {
        return $this->belongsTo(Department::class, "department_id", "id");
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select('admins.*', 'roles.name as role_name', 'gds.name as gd_name', 'departments.name as department_name')
            ->join('roles', 'admins.role_id', '=', 'roles.id')
            ->leftJoin('gds', 'admins.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'admins.department_id', '=', 'departments.id')
            ->where('admins.is_system', '0')
            ->whereNull('admins.deleted_at')
            ->where(function ($q) use ($value) {
                $q->whereAny([
                    'admins.username',
                    'admins.display_name',
                    'admins.email',
                    'roles.name',
                    'gds.name',
                    'departments.name'
                ], 'like', "%{$value}%");
            });
    }
}
