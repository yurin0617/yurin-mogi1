<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Checkout\Session as StripeSession;

class T10_PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. 「購入する」ボタンを押下すると購入が完了する
     * 2. 購入した商品は商品一覧画面にて「Sold」と表示される
     * 3. 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_商品購入が完了し一覧へのsold表示とプロフィールへの追加が正しく行われる()
    {
        // データの初期化
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 自分の準備（メール認証と、DBへのプロフィール住所登録）
        $me = User::where('email', 'test@example.com')->first();
        $me->markEmailAsVerified();
        $me->profile()->create([
            'name'        => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
        ]);
        $me = $me->fresh(); // ロボットの記憶を最新に同期

        // 出品者の準備
        $otherUser = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password')
        ]);

        // テスト用商品の準備（自分以外の人が出品したもの）
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => '高級腕時計',
            'price' => 50000,
            'description' => '美品です',
            'condition' => '目立った傷や汚れなし',
            'image_path' => 'watch.jpg'
        ]);

        // Stripeの通信を「偽物の身代わり」に差し替えて罠を無効化
        $mockSession = json_decode(json_encode(['url' => 'http://localhost/?tab=all']));
        $this->mock('alias:' . StripeSession::class)
            ->shouldReceive('create')
            ->andReturn($mockSession);


        // ==========================================
        // 要件1: 「購入する」ボタンを押下すると購入が完了する
        // ==========================================

        // 🏃‍♂️ ログインして購入ボタン（POST）を押す！
        // 💡 不要な住所セッションや、送信データ内の住所を削ってスッキリさせました！
        $response = $this->actingAs($me)->post(route('purchase.store', $item->id), [
            'payment_method' => 'コンビニ払い',
        ]);

        // 🎯 'purchases' テーブルに購入履歴が保存されたか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);


        // ==========================================
        // 要件2: 購入した商品は商品一覧画面にて「Sold」と表示される
        // ==========================================

        // 🏃‍♂️ 商品一覧画面を開く
        $indexResponse = $this->get('/?tab=all');

        // 🎯 一覧画面に「Sold」という文字が表示されているか確認
        $indexResponse->assertSee('Sold');


        // ==========================================
        // 要件3: 「プロフィール/購入した商品一覧」に追加されている
        // ==========================================

        // 🏃‍♂️ 本物のマイページURL ＆ 正しいタブの合言葉（?page=buy）で開く
        $profileResponse = $this->get(route('mypage.show') . '?page=buy');

        // 🎯 プロフィールの購入一覧に、商品名が正しく表示されているか確認
        $profileResponse->assertSee('高級腕時計');
    }
}
