<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arahan extends Model
{
    protected $table = 'arahan';
    use HasFactory;

    protected $fillable = [
        'keputusan_id', 'unit_kerja_id', 'pic_unit_kerja_id', 
        'tanggal_terbit', 'strategi', 'status'
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function keputusan()
    {
        return $this->belongsTo(Keputusan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_unit_kerja_id');
    }

    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class);
    }
}