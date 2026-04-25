<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'postal_code',
        'address',
        'building',
        'image_path',
    ];

    // --- リレーション定義 ---

    // ユーザー：1つのプロフィールは、1人のユーザーに属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}