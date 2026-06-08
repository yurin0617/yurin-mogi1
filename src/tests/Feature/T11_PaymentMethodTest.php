<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Stripe\Checkout\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T11_PaymentMethodTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 支払い方法選択画面で選択した支払い方法が画面に反映されること
     */
    public function test_支払い方法の全パターン自動テスト()
    {
        // データの初期化
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 1. Stripeのモック設定
        $mockSession = json_decode(json_encode(['url' => route('purchase.success')]));
        $this->mock('alias:' . Session::class)
            ->shouldReceive('create')
            ->andReturn($mockSession);

        // 2. ユーザー（購入者）の準備
        $me = User::where('email', 'test@example.com')->first();
        $me->markEmailAsVerified();
        $me->profile()->create([
            'name' => 'テスト',
            'postal_code' => '111-1111',
            'address' => '東京都渋谷区'
        ]);

        // 3. ユーザー（出品者）の準備
        $otherUser = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password')
        ]);

        // 4. 商品の準備（手動作成）
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'description' => 'テスト説明',
            'condition' => '新品',
            'image_path' => 'test.jpg'
        ]);

        // 5. テストしたい支払い方法のリスト
        $allMethods = ['カード払い', 'コンビニ払い'];

        // 6. foreachで自動ループ実行！
        foreach ($allMethods as $method) {
            \App\Models\Purchase::query()->delete();
            $response = $this->actingAs($me)
                ->post(route('purchase.store', $item->id), [
                    'payment_method' => $method,
                ]);

            // DBに正しく保存されたか検証
            $this->assertDatabaseHas('purchases', [
                'user_id'        => $me->id,
                'item_id'        => $item->id,
                'payment_method' => $method,
            ]);
        }
    }
}
