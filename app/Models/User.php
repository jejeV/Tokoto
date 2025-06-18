<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Address;
use App\Models\City;
use App\Models\Province;
use App\Models\Order;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'address_line_1',
        'address_line_2',
        'city_id',
        'province_id',
        'zip_code',
        'phone_number',
        'first_name',
        'last_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Cek apakah user memiliki role tertentu.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Relasi ke model City (untuk alamat utama user jika disimpan di tabel users).
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relasi ke model Province (untuk alamat utama user jika disimpan di tabel users).
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Relasi ke model Order.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Mendefinisikan relasi: satu User memiliki banyak Address tersimpan.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }
}
