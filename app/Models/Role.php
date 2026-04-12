<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi secara massal
    protected $fillable = ['name', 'guard_name', 'description'];

    /**
     * Relasi ke User: Satu Role bisa dimiliki oleh banyak User.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}