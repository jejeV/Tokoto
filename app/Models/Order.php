<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Province;
use App\Models\City;


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
        'shipping_method',
        'order_status',
        'payment_status',
        'payment_method',
        'midtrans_transaction_id',
        'midtrans_snap_token',
        'midtrans_payment_type',
        'midtrans_gross_amount',
        'midtrans_masked_card',
        'midtrans_bank',
        'midtrans_va_numbers',
        'payment_redirect_url',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_province_id',
        'billing_city_id',
        'billing_zip_code',
        'billing_phone',
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

    protected $casts = [
        'midtrans_va_numbers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function billingProvince()
    {
        return $this->belongsTo(Province::class, 'billing_province_id');
    }

    public function billingCity()
    {
        return $this->belongsTo(City::class, 'billing_city_id');
    }

    public function shippingProvince()
    {
        return $this->belongsTo(Province::class, 'shipping_province_id');
    }

    public function shippingCity()
    {
        return $this->belongsTo(City::class, 'shipping_city_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function histories()
    {
    return $this->hasMany(OrderHistory::class)->latest();
    }
}
