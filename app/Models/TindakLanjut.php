<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjut extends Model
{
    protected $table = 'tindak_lanjut';
    use HasFactory;

    protected $fillable = [
        'arahan_id', 'unit_kerja_id', 'periode_bulan', 'periode_tahun',
        'tindak_lanjut', 'kendala', 'evidence_url', 'created_by', 'status'
    ];

    public function arahan()
    {
        return $this->belongsTo(Arahan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function getCurrentApprovalStage()
    {
        $approvedCount = $this->approvals()->where('status', 'approved')->count();
        return $approvedCount + 1;
    }

    public function isFullyApproved()
    {
        return $this->approvals()->where('status', 'approved')->count() === 5;
    }
}