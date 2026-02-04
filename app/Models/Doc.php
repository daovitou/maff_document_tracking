<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    /** @use HasFactory<\Database\Factories\DocFactory> */
    use HasFactory, HasUuids;
    protected $casts = [
        'post_at' => 'date:Y-m-d'
    ];
    public function departments() {
        return $this->hasMany(Department::class, "id", "department_id");
    }
    public function scopeSearch($query, $value) {
        return $query
            ->select('docs.*', 'departments.name as department_name')
            ->join('departments', 'docs.department_id', '=', 'departments.id')
            ->where(function ($q) use ($value) {
                $q->whereAny(['docs.title', 'docs.code', 'departments.name','docs.status'], 'like', "%{$value}%");
            });
    }
    public function getPdfUrlAttribute()
    {
        return $this->original_file ? asset('storage/' . $this->original_file ) : asset('images/avatar.jpg');
    }
    public function getPdfNameAttribute()
    {
        $parts = explode("/", $this->original_file);
        return $this->original_file ? array_pop($parts) : "default.pdf";
    }
    public function getReturnPdfNameAttribute()
    {
        $parts = explode("/", $this->return_file);
        return $this->return_file ? array_pop($parts) : "default.pdf";
    }
}
