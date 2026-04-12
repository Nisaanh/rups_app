<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $table = 'approvals';
    use HasFactory;

    protected $fillable = [
        'tindak_lanjut_id', 'stage', 'stage_name', 'status', 
        'result', 'approved_by', 'approved_at', 'note'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function tindakLanjut()
    {
        return $this->belongsTo(TindakLanjut::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    
}