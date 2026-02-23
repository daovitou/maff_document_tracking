<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BeDocument extends Model
{
    /** @use HasFactory<\Database\Factories\BeDocumentFactory> */
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
                'be_documents.*',
            )
            ->join('be_document_send_tos', 'be_documents.id', '=', 'be_document_send_tos.be_document_id')
            ->leftJoin('gds', 'be_document_send_tos.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'be_document_send_tos.department_id', '=', 'departments.id')
            ->leftJoin('personels', 'be_document_send_tos.personel_id', '=', 'personels.id')
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    [
                        'be_documents.code',
                        'be_documents.article',
                        'be_documents.article_at',
                        'be_document_send_tos.send_at',
                        'be_document_send_tos.respect_at',
                        'be_document_send_tos.status',
                        'be_documents.source',
                        'gds.name',
                        'departments.name',
                        'personels.name',
                        'personels.position'
                    ],
                    'like',
                    "%{$value}%"
                );
            })
            ->groupBy('be_documents.id'); // Replaced ->distinct()
    }
    public function scopeMainGroupFollowUp($query, $value)
    {
        return $query
            ->select(
                'be_documents.*',
            )
            ->join('be_document_send_tos', 'be_documents.id', '=', 'be_document_send_tos.be_document_id')
            ->leftJoin('gds', 'be_document_send_tos.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'be_document_send_tos.department_id', '=', 'departments.id')
            ->leftJoin('personels', 'be_document_send_tos.personel_id', '=', 'personels.id')
            ->where('be_document_send_tos.status', 'កំពុងរងចាំ')
            ->where('be_document_send_tos.respect_at', '<=', Carbon::now())
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    [
                        'be_documents.code',
                        'be_documents.article',
                        'be_documents.article_at',
                        'be_document_send_tos.send_at',
                        'be_document_send_tos.respect_at',
                        'be_document_send_tos.status',
                        'be_documents.source',
                        'gds.name',
                        'departments.name',
                        'personels.name',
                        'personels.position'
                    ],
                    'like',
                    "%{$value}%"
                );
            })
            ->groupBy('be_documents.id'); // Replaced ->distinct()
    }
    public function scopeSearch($query, $value)
    {
        return $query
            ->select(
                'be_documents.*',
                'be_document_send_tos.id as send_to_id',
                'be_document_send_tos.deleted_at as send_to_deleted_at',
                'be_document_send_tos.status as status',
                'be_document_send_tos.to_gd as to_gd',
                'be_document_send_tos.send_at as send_at',
                'be_document_send_tos.respect_at as respect_at',
                'gds.name as gd_name',
                'departments.name as department_name',
                'personels.name as personel_name',
                'personels.position as personel_position',
            )
            ->join('be_document_send_tos', 'be_documents.id', '=', 'be_document_send_tos.be_document_id')
            ->leftJoin('gds', 'be_document_send_tos.gd_id', '=', 'gds.id')
            ->leftJoin('departments', 'be_document_send_tos.department_id', '=', 'departments.id')
            ->leftJoin('personels', 'be_document_send_tos.personel_id', '=', 'personels.id')
            ->where(function ($q) use ($value) {
                $q->whereAny(
                    [
                        'be_documents.code',
                        'be_documents.article',
                        'be_documents.article_at',
                        'be_document_send_tos.send_at',
                        'be_document_send_tos.respect_at',
                        'be_document_send_tos.status',
                        'be_documents.source',
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
