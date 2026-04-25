<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
    ];

    // --- リレーション定義 ---

    // ユーザー：1つの「いいね」は、1人のユーザーによるもの
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品：1つの「いいね」は、1つの商品に対するもの
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
