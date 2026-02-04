<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


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
    public function scopeSearch($query, $value)
    {
        // return $query
        //     ->select('admins.*', 'groups.name as group_name')
        //     ->join('groups', 'admins.group_id', '=', 'groups.id')
        //     ->where('admins.is_system', '=', '0')
        //     ->whereAny([
        //         'admins.fullname',
        //         'admins.email',
        //         'groups.name'
        //     ], 'like', "%{$value}%");
        // return $query
        //     ->select('admins.*')
        //     ->where('admins.is_system', '=', '0')
        //     ->whereAny(['admins.username', 'admins.display_name', 'admins.email', 'admins.role'], 'like', "%{$value}%");
        return $query
            ->select('admins.*','roles.name as role_name')
            ->join('roles', 'admins.role_id', '=', 'roles.id')
            ->where('admins.is_system', '0')
            ->whereNull('admins.deleted_at')
            ->where(function ($q) use ($value) {
                $q->whereAny([
                    'admins.username',
                    'admins.display_name',
                    'admins.email',
                    'roles.name'
                ], 'like', "%{$value}%");
            });
    }
}
