<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
       $user = User::first(); 

        $items = [
            ['name' => '腕時計', 'price' => 15000, 'brand' => 'Rolax', 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'image_path' => 'images/Armani+Mens+Clock.jpg', 'condition' => '良好', 'categories' => ['ファッション', 'メンズ', 'アクセサリー']],
            ['name' => 'HDD', 'price' => 5000, 'brand' => '西芝', 'description' => '高速で信頼性の高いハードディスク', 'image_path' => 'images/HDD+Hard+Disk.jpg', 'condition' => '目立った傷や汚れなし', 'categories' => ['家電']],
            ['name' => '玉ねぎ3束', 'price' => 300, 'brand' => 'なし', 'description' => '新鮮な玉ねぎ3束のセット', 'image_path' => 'images/iLoveIMG+d.jpg', 'condition' => 'やや傷や汚れあり', 'categories' => ['キッチン']],
            ['name' => '革靴', 'price' => 4000, 'brand' => null, 'description' => 'クラシックなデザインの革靴', 'image_path' => 'images/Leather+Shoes+Product+Photo.jpg', 'condition' => '状態が悪い', 'categories' => ['ファッション', 'メンズ']],
            ['name' => 'ノートPC', 'price' => 45000, 'brand' => null, 'description' => '高性能なノートパソコン', 'image_path' => 'images/Living+Room+Laptop.jpg', 'condition' => '良好', 'categories' => ['家電']],
            ['name' => 'マイク', 'price' => 8000, 'brand' => 'なし', 'description' => '高音質のレコーディング用マイク', 'image_path' => 'images/Music+Mic+4632231.jpg', 'condition' => '目立った傷や汚れなし', 'categories' => ['家電']],
            ['name' => 'ショルダーバッグ', 'price' => 3500, 'brand' => null, 'description' => 'おしゃれなショルダーバッグ', 'image_path' => 'images/Purse+fashion+pocket.jpg', 'condition' => 'やや傷や汚れあり', 'categories' => ['ファッション', 'レディース']],
            ['name' => 'タンブラー', 'price' => 500, 'brand' => 'なし', 'description' => '使いやすいタンブラー', 'image_path' => 'images/Tumbler+souvenir.jpg', 'condition' => '状態が悪い', 'categories' => ['キッチン']],
            ['name' => 'コーヒーミル', 'price' => 4000, 'brand' => 'Starbacks', 'description' => '手動のコーヒーミル', 'image_path' => 'images/Waitress+with+Coffee+Grinder.jpg', 'condition' => '良好', 'categories' => ['インテリア', 'キッチン']],
            ['name' => 'メイクセット', 'price' => 2500, 'brand' => null, 'description' => '便利なメイクアップセット', 'image_path' => 'images/makeset.jpg', 'condition' => '目立った傷や汚れなし', 'categories' => ['レディース', 'コスメ']],
        ];

        foreach ($items as $itemData) {
            $categoryNames = $itemData['categories'];
            unset($itemData['categories']);
            
            $itemData['user_id'] = $user->id;

            $item = Item::create($itemData);

            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');
            $item->categories()->attach($categoryIds);
        }
    }
}
