<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja';
    
    protected $fillable = ['name', 'level', 'report_to'];

    // Relasi ke parent unit (atasan unit)
    public function parent()
    {
        return $this->belongsTo(UnitKerja::class, 'report_to');
    }

    // Relasi ke anak unit
    public function children()
    {
        return $this->hasMany(UnitKerja::class, 'report_to');
    }

    // Relasi ke user yang menjadi PIC di unit ini
    // public function picUser()
    // {
    //     return $this->hasOne(User::class, 'pic_unit_kerja_id');
    // }

    // Relasi ke semua user di unit ini
    public function users()
    {
        return $this->hasMany(User::class, 'unit_kerja_id');
    }

    public function arahan()
    {
        return $this->hasMany(Arahan::class);
    }

    public function tindakLanjut(): HasMany
    {
        // Pastikan foreign key-nya adalah 'unit_kerja_id'
        return $this->hasMany(TindakLanjut::class, 'unit_kerja_id');
    }
}