<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'shipping_postal_code',
        'shipping_address',
        'shipping_building',
    ];

    // --- リレーション定義 ---

    // 購入者：1つの購入履歴は、1人のユーザーに属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品：1つの購入履歴は、1つの商品に属する
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}