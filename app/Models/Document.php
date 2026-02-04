<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, HasUuids;
    public function department()
    {
        return $this->belongsTo(Department::class, "department_id", "id");
    }
    public function gd()
    {
        return $this->belongsTo(Gd::class, "gd_id", "id");
    }
    public function personel()
    {
        return $this->belongsTo(Personel::class, "personel_id", "id");
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select('documents.*', 'departments.name as department_name', 'gds.name as gd_name', 'personels.name as personel_name')
            ->leftJoin('departments', 'documents.department_id', '=', 'departments.id')
            ->leftJoin('gds', 'documents.gd_id', '=', 'gds.id')
            ->leftJoin('personels', 'documents.personel_id', '=', 'personels.id')
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    ['documents.article', 'documents.article_at', 'documents.source', 'documents.code', 'departments.name', 'documents.status', 'gds.name', 'personels.name'],
                    'like',
                    "%{$value}%"
                );
            });
    }
    public function getPdfNameAttribute()
    {
        $parts = explode("/", $this->document_file);
        return $this->document_file ? array_pop($parts) : "default.pdf";
    }
    public function getPdfUrlAttribute()
    {
        return $this->document_file ? asset('storage/' . $this->document_file) : asset('images/avatar.jpg');
    }
}
