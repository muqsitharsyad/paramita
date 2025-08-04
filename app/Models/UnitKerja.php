<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'keterangan',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
