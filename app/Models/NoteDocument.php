<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NoteDocument extends Model
{
    /** @use HasFactory<\Database\Factories\NoteDocumentFactory> */
    use HasFactory, HasUuids;
    public function getPdfNameAttribute()
    {
        $parts = explode("/", $this->document_file);
        return $this->document_file ? array_pop($parts) : "default.pdf";
    }
    public function scopeMainGroup($query, $value)
    {
        return $query
            ->select(
                'note_documents.*',
            )
            ->join('note_document_send_tos', 'note_documents.id', '=', 'note_document_send_tos.note_document_id')
            ->leftJoin('gds', 'note_document_send_tos.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'note_document_send_tos.department_id', '=', 'departments.id')
            ->leftJoin('personels', 'note_document_send_tos.personel_id', '=', 'personels.id')
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    [
                        'note_documents.code',
                        'note_documents.article',
                        'note_documents.source',
                        'note_documents.article_at',
                        'note_document_send_tos.send_at',
                        'note_document_send_tos.status',
                        'gds.name',
                        'departments.name',
                        'personels.name',
                        'personels.position'
                    ],
                    'like',
                    "%{$value}%"
                );
            })
            ->groupBy('note_documents.id'); // Replaced ->distinct()
    }
    public function scopeMainGroupFollowUp($query, $value)
    {
        $threeDaysAgo = Carbon::now()->subDays(3);
        return $query
            ->select(
                'note_documents.*',
            )
            ->join('note_document_send_tos', 'note_documents.id', '=', 'note_document_send_tos.note_document_id')
            ->leftJoin('gds', 'note_document_send_tos.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'note_document_send_tos.department_id', '=', 'departments.id')
            ->leftJoin('personels', 'note_document_send_tos.personel_id', '=', 'personels.id')
            ->where('note_document_send_tos.status', 'កំពុងរងចាំ')
            ->where('note_document_send_tos.send_at', '<=', $threeDaysAgo)
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    [
                        'note_documents.code',
                        'note_documents.article',
                        'note_documents.source',
                        'note_documents.article_at',
                        'note_document_send_tos.send_at',
                        'note_document_send_tos.status',
                        'gds.name',
                        'departments.name',
                        'personels.name',
                        'personels.position'
                    ],
                    'like',
                    "%{$value}%"
                );
            })
            ->groupBy('note_documents.id'); // Replaced ->distinct()
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select(
                'note_documents.*',
                'note_document_send_tos.id as send_to_id',
                'note_document_send_tos.deleted_at as send_to_deleted_at',
                'note_document_send_tos.status as status',
                'note_document_send_tos.to_gd as to_gd',
                'note_document_send_tos.send_at as send_at',
                'gds.name as gd_name',
                'departments.name as department_name',
                'personels.name as personel_name',
                'personels.position as personel_position',
            )
            ->join('note_document_send_tos', 'note_documents.id', '=', 'note_document_send_tos.note_document_id')
            ->leftJoin('gds', 'note_document_send_tos.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'note_document_send_tos.department_id', '=', 'departments.id')
            ->leftJoin('personels', 'note_document_send_tos.personel_id', '=', 'personels.id')
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    [
                        'note_documents.code',
                        'note_documents.article',
                        'note_documents.source',
                        'note_documents.article_at',
                        'note_document_send_tos.send_at',
                        'note_document_send_tos.status',
                        'gds.name',
                        'departments.name',
                        'personels.name',
                        'personels.position'
                    ],
                    'like',
                    "%{$value}%"
                );
            });
    }
}
