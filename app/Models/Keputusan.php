<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keputusan extends Model
{
    protected $table = 'keputusan';
    use HasFactory;

    protected $fillable = ['nomor_keputusan','periode_year', 'status', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function arahan()
    {
        return $this->hasMany(Arahan::class);
    }
}