<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personel extends Model
{
    /** @use HasFactory<\Database\Factories\PersonelFactory> */
    use HasFactory, HasUuids;
    public function scopeSearch($query, $value)
    {
        return $query
            ->select('personels.*')
            ->where('deleted_at', '=', null)
            ->where(function ($q) use ($value) {
                $q->whereAny(['personels.name', 'personels.organization', 'personels.position', 'personels.phone'], 'like', "%{$value}%");
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
