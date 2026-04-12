<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

   protected $fillable = [
    'badge', 'name', 'email', 'password', 'unit_kerja_id', 
    'pic_unit_kerja_id', 'status' ];
    
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    // Ini relasi ke Atasan (berdasarkan field pic_unit_kerja_id)
    public function picUnit()
    {
        return $this->belongsTo(User::class, 'pic_unit_kerja_id');
    }

    // Relasi sebaliknya: Siapa saja bawahan user ini
    public function subordinates()
    {
        return $this->hasMany(User::class, 'pic_unit_kerja_id');
    }
public function arahanPic()
{
    // pastikan di tabel 'arahan', nama kolomnya adalah 'pic_id' atau 'user_id'
    return $this->hasMany(Arahan::class, 'pic_unit_kerja_id'); 
}
    public function keputusan()
    {
        return $this->hasMany(Keputusan::class, 'created_by');
    }

    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approved_by');
    }
}