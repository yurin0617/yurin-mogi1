<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T06_SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. 「商品名」で部分一致検索ができる
     */
    public function test_商品名で部分一致検索ができる()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 💡yurinさんの指摘通り！実在する「他人」のユーザーデータを1人作成します
        $otherUser = User::create([
            'name'              => '他人ユーザー',
            'email'             => 'other@example.com',
            'password'          => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // 検索に引っかかるテスト用の商品を、実実在する他人のIDで作る！
        $searchItem = Item::create([
            'user_id'     => $otherUser->id, // 👈 これでもうエラーになりません！
            'name'        => '高級なメンズ腕時計',
            'price'       => 5000,
            'description' => 'テスト用の腕時計です',
            'condition'   => 'good',
            'image_path'  => 'images/test.jpg',
        ]);

        // 「時計」というキーワードをURLに入れて、トップページを開く
        $response = $this->get('/?keyword=時計');

        $response->assertStatus(200);

        // 期待挙動：部分一致した商品の名前が、画面にちゃんと表示されていること！
        $response->assertSee($searchItem->name);
    }

    /**
     * 2. 検索状態がマイリストでも保持されている
     */
    public function test_検索状態がマイリストでも保持されている()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 自分（ログインユーザー）
        $me = User::where('email', 'test@example.com')->first();

        // マイリストを開くための住所データ
        $me->profile()->create([
            'name'        => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
        ]);

        // ログインして、マイリストタブかつ検索キーワードを指定して開く！
        $response = $this->actingAs($me)->get('/?tab=mylist&keyword=時計');

        $response->assertStatus(200);

        // 期待挙動：HTMLの検索入力欄（inputタグ）の中に、キーワードが保持されていること！
        $response->assertSee('name=');
        $response->assertSee('keyword');
        $response->assertSee('時計');
    }
}
