<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'name',
    ];

    /**
     * Mendefinisikan relasi: satu City dimiliki oleh satu Province.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Mendefinisikan relasi: satu City memiliki banyak Address.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Mendefinisikan relasi: satu City memiliki banyak User (jika user memiliki kota default).
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
