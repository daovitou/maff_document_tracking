<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory, HasUuids;
    protected $casts = [
        'permissions' => 'array',
    ];
    public function users()
    {
        return $this->hasMany(Admin::class, "id", "role_id");
    }
    public function gd()
    {
        return $this->belongsTo(Gd::class, "gd_id", "id");
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select('roles.*')
            ->where(function ($q) use ($value) {
                $q->whereAny(['roles.name', 'roles.description'], 'like', "%{$value}%");
            });
    }
}
