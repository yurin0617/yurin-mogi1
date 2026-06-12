<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Stripe\Checkout\Session;

class T12_PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    protected $me;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $this->me = User::where('email', 'test@example.com')->first();
        $this->me->markEmailAsVerified();
        
        $otherUser = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password')
        ]);
        $this->item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'description' => 'テスト説明',
            'condition' => '新品',
            'image_path' => 'test.jpg'
        ]);
    }

    /**
     * 配送先変更機能と購入時の住所紐付けテスト
     */
    public function test_配送先変更と購入時の住所反映のテスト()
    {
        $newAddressData = [
            'postal_code' => '999-9999',
            'address'     => '東京都渋谷区テスト町',
            'building'    => 'テストビル101'
        ];

        // 1. 配送先変更画面で住所を更新（セッションへ保存）
        $this->actingAs($this->me)
             ->post(route('purchase.address.update', $this->item->id), $newAddressData);

        // 2. 商品購入画面を再度開く
        $response = $this->get(route('purchase.show', $this->item->id));

        // 3. 登録した住所が画面に反映されているかチェック
        $response->assertSee('999-9999');
        $response->assertSee('東京都渋谷区テスト町');
        $response->assertSee('テストビル101');

        // 4. Stripeのモック設定
        $mockSession = json_decode(json_encode(['url' => route('purchase.success')]));
        $this->mock('alias:' . Session::class)
             ->shouldReceive('create')
             ->andReturn($mockSession);

        // 5. 商品を購入する
        $this->actingAs($this->me)
             ->post(route('purchase.store', $this->item->id), [
                 'payment_method' => 'カード払い'
             ]);

        // 6. 購入した商品に送付先住所が紐づいて登録されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id'              => $this->me->id,
            'item_id'              => $this->item->id,
            'shipping_postal_code' => '999-9999',
            'shipping_address'     => '東京都渋谷区テスト町',
            'shipping_building'    => 'テストビル101',
        ]);
    }
}