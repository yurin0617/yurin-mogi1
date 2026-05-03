<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // コンディションの選択肢を定数として定義
    const CONDITIONS = [
        '良好',
        '目立った傷や汚れなし',
        'やや傷や汚れあり',
        '状態が悪い'
    ];

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'price',
        'description',
        'image_path',
        'condition',
    ];

    // --- リレーション定義 ---

    // 出品者：1つの商品は1人のユーザーに属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ：1つの商品は複数のカテゴリに属しうる（多対多）
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // いいね：1つの商品は複数のいいねを持つ
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // コメント：1つの商品は複数のコメントを持つ
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入履歴：1つの商品は1つの購入履歴に紐付く
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
