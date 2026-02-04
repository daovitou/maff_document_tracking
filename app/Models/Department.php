<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory, HasUuids;
    public function gd()
    {
        return $this->belongsTo(Gd::class, "gd_id", "id");
    }
    public function scopeInGd($query)
    {
       
        return $query;
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select('departments.*', 'gds.name as gd_name', 'gds.is_active as gd_is_active')
            ->join('gds', 'departments.gd_id', '=', 'gds.id')
            ->where('gds.is_active', 1)
            ->where(function ($q) use ($value) {
                $q->where('departments.is_active', 1)->whereAny(['gds.name', 'departments.name', 'departments.description', 'departments.phone'], 'like', "%{$value}%");
            });
    }
}
