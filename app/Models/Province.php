<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Mendefinisikan relasi: satu Province memiliki banyak City.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Mendefinisikan relasi: satu Province memiliki banyak Address.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Mendefinisikan relasi: satu Province memiliki banyak User (jika user memiliki provinsi default).
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
