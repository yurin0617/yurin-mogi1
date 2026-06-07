<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item; // 商品モデルを使うために追加しています
use Database\Seeders\UserSeeder; // ユーザーシーダーを使うために追加！
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T04_ItemListTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 1. 全商品を取得できる
     */
    public function test_全商品を取得できる()
    {
        // 1. 【超重要】まずはユーザー（出品者）をシーダーで作成する！
        $this->seed(UserSeeder::class);

        // 2. そのあとに商品をシーダーでドバッと入れる（これでエラーが完全に消えます！）
        $this->seed(ItemSeeder::class);

        // 3. データベースに今、商品が全部で何個あるか自動で数える
        $expectedCount = Item::count();

        // 4. 商品一覧ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // 5. 画面に届いた商品の数と、さっき数えた数が一致しているかチェック！
        $response->assertViewHas('items', function ($items) use ($expectedCount) {
            return count($items) === $expectedCount;
        });

        // 6. 最初の1件目の商品名を自動で引っ張ってきて、画面に表示されているかチェック！
        $firstItem = Item::first();
        $response->assertSee($firstItem->name);
    }

    /**
     * 2. 購入済み商品は「Sold」と表示される
     */
    public function test_購入済み商品はSoldと表示される()
    {
        // 1. ユーザーと商品のシーダーを順番に実行
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 2. シーダーが入れてくれた商品の中から、最初の1件を引っ張ってくる
        $soldItem = Item::first();

        // 3. 【ファクトリーなし版】直接 Purchase データを作成して、この商品を「売り切れ」にする！
        // 💡 必須になりそうな項目（支払い方法や住所など）のダミーデータをすべて詰め込みます
        \App\Models\Purchase::create([
            'item_id'              => $soldItem->id,
            'user_id'              => User::first()->id,
            'payment_method'       => 'konbini',
            'shipping_postal_code' => '123-4567',
            'shipping_address'     => '東京都渋谷区1-1-1',
            'shipping_building'    => 'テストビル101',
        ]);

        // 4. 商品一覧ページを開く
        $response = $this->get('/');

        // 5. 期待挙動：画面にその商品の名前と、「Sold」という文字が表示されていること
        $response->assertSee($soldItem->name);
        $response->assertSee('Sold');
    }

    /**
     * 3. 自分が出品した商品は表示されない
     */
    public function test_自分が出品した商品は表示されない()
    {
        // 1. ユーザーと商品のシーダーを順番に実行
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 2. シーダーで作られた「テストユーザー（自分）」をメールアドレスで探して引っ張ってくる
        $me = User::where('email', 'test@example.com')->first();

        // 3. 自分自身にログインした状態になりきって、商品一覧ページを開く！
        $response = $this->actingAs($me)->get('/');

        // 4. そのユーザー（自分）が出品している商品のデータを1件自動で探す
        $myItem = Item::where('user_id', $me->id)->first();

        // 5. 期待挙動：自分が出品した商品の名前が、画面に【表示されていない】ことを確認する
        $response->assertDontSee($myItem->name);
    }
}