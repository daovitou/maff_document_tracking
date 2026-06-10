<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocFile extends Model
{
    /** @use HasFactory<\Database\Factories\DocFileFactory> */
    use HasFactory, HasUuids;
    public function getPdfNameAttribute()
    {
        $parts = explode("/", $this->document_file);
        return $this->document_file ? array_pop($parts) : "default.pdf";
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
    public function NoteDoc()
    {
        return $this->belongsTo(NoteDocument::class, "document_id", "id");
    }
}
