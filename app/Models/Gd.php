<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gd extends Model
{
    /** @use HasFactory<\Database\Factories\GdFactory> */
    use HasFactory, HasUuids;
    public function departments()
    {
        return $this->hasMany(Department::class, "id", "gd_id");
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select('gds.*')
            ->where('gds.is_active', 1)
            ->where(function ($q) use ($value) {
                $q->whereAny(['gds.name', 'gds.description', 'gds.phone'], 'like', "%{$value}%");
            });
    }
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, "created_by", "id");
    }
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, "updated_by", "id");
    }
    public function deletedBy()
    {
        return $this->belongsTo(Admin::class, "deleted_by", "id");
    }
}
