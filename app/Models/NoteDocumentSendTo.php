<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteDocumentSendTo extends Model
{
    /** @use HasFactory<\Database\Factories\NoteDocumentSendToFactory> */
    use HasFactory, HasUuids;
    public function document()
    {
        return $this->belongsTo(NoteDocument::class, "note_document_id", "id");
    }
    public function gd()
    {
        return $this->belongsTo(Gd::class, "gd_id", "id");
    }
    public function department()
    {
        return $this->belongsTo(Department::class, "department_id", "id");
    }
    public function personel()
    {
        return $this->belongsTo(Personel::class, "personel_id", "id");
    }
     public function getReturnPdfNameAttribute()
    {
        $parts = explode("/", $this->return_file);
        return $this->return_file ? array_pop($parts) : "default.pdf";
    }
}
