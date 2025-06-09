<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'subtotal_amount',
        'discount_amount',
        'shipping_cost',
        'status',
        'payment_method',
        'transaction_id_midtrans',
        'midtrans_snap_token',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_province_id',
        'billing_city_id',
        'billing_zip_code',
        'billing_phone_number',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_province_id',
        'shipping_city_id',
        'shipping_zip_code',
        'shipping_phone_number',
    ];

    /**
     * Mendefinisikan relasi: satu Order dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi: satu Order memiliki banyak OrderItem.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Provinsi untuk alamat penagihan.
     */
    public function billingProvince()
    {
        return $this->belongsTo(Province::class, 'billing_province_id');
    }

    /**
     * Relasi ke Kota untuk alamat penagihan.
     */
    public function billingCity()
    {
        return $this->belongsTo(City::class, 'billing_city_id');
    }

    /**
     * Relasi ke Provinsi untuk alamat pengiriman.
     */
    public function shippingProvince()
    {
        return $this->belongsTo(Province::class, 'shipping_province_id');
    }

    /**
     * Relasi ke Kota untuk alamat pengiriman.
     */
    public function shippingCity()
    {
        return $this->belongsTo(City::class, 'shipping_city_id');
    }
}
