<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver',
        'receiverTitle',
        'receiverMobile',
        'receiverEmail',
        'receiverAddress',
        'message',
        'couponCode',
        'subtotal',
        'shipCost',
        'status',
        'pay_type',
        'trade_no',
        'pay_at',
        'reply_desc',
        'type'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_item', 'order_id', 'item_id')->withPivot('id', 'quantity')->withTimestamps();
    }
}
