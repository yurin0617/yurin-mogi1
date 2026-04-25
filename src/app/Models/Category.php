<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    // --- リレーション定義 ---

    // 商品：1つのカテゴリには、複数の商品が属する（多対多）
    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
