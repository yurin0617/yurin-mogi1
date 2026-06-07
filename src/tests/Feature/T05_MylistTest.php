<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;     // 👈 いいねモデルを使うために追加！
use App\Models\Purchase; // 👈 購入モデル
use App\Models\Profile; // 👈 住所データを作るために追加！
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T05_MylistTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 1. いいねした商品だけが表示される
     */
    public function test_いいねした商品だけが表示される()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $me = User::where('email', 'test@example.com')->first();

        // マイグレーションの設計通り、必須項目（name, postal_code, address）をすべて網羅！
        $me->profile()->create([
            'name'        => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
        ]);

        // 実在する「他人」のユーザーデータを1人作成する
        $otherUser = User::create([
            'name'              => '他人ユーザー',
            'email'             => 'other@example.com',
            'password'          => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // シーダーの最初の商品を取得
        $likeItem = Item::first();

        // 商品の出品者を、幽霊IDの999ではなく、実在する「他人」のIDに書き換える！
        $likeItem->update([
            'user_id' => $otherUser->id
        ]);

        // 自分がこの商品に「いいね」したデータを作る
        Like::create([
            'user_id' => $me->id,
            'item_id' => $likeItem->id,
        ]);

        // ログインしてマイリストを開く（もうリダイレクトされません！）
        $response = $this->actingAs($me)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee($likeItem->name);
    }

    /**
     * 2. 購入済み商品は「Sold」と表示される
     */
    public function test_マイリストでも購入済み商品はSoldと表示される()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $me = User::where('email', 'test@example.com')->first();

        $me->profile()->create([
            'name'        => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
        ]);

        // 実在する「他人」を1人作成！
        $otherUser = User::create([
            'name'              => '他人ユーザー2',
            'email'             => 'other2@example.com',
            'password'          => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $soldItem = Item::first();

        // 出品者をその他人のIDに書き換える！
        $soldItem->update([
            'user_id' => $otherUser->id
        ]);

        Like::create([
            'user_id' => $me->id,
            'item_id' => $soldItem->id,
        ]);

        Purchase::create([
            'item_id'              => $soldItem->id,
            'user_id'              => $me->id,
            'payment_method'       => 'konbini',
            'shipping_postal_code' => '123-4567',
            'shipping_address'     => '東京都渋谷区1-1-1',
            'shipping_building'    => 'テストビル101',
        ]);

        $response = $this->actingAs($me)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee($soldItem->name);
        $response->assertSee('Sold');
    }

    /**
     * 3. 未認証の場合は何も表示されない
     */
    public function test_未認証の場合は何も表示されない()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        $firstItem = Item::first();
        $response->assertDontSee($firstItem->name);
    }
}
