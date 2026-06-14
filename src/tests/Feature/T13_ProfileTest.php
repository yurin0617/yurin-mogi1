<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\CategorySeeder;

class T13_ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $me;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. 必要なデータをシーダーで準備
        $this->seed(CategorySeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        // 2. ログインユーザーの設定
        $this->me = User::where('email', 'test@example.com')->first();
        $this->me->markEmailAsVerified();

        // プロフィール情報の作成
        $this->me->profile()->create([
            'name' => 'テストユーザー',
            'image_path' => 'profile.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都'
        ]);

        // 3. 自分の「出品商品」を準備（UserSeederのユーザーが出品したものとする）
        // ItemSeederですでに商品が作られているので、それを自分の出品として利用
        $item = Item::first();
        $item->update(['user_id' => $this->me->id]);

        // 4. 自分の「購入商品」を準備（別のユーザーから購入）
        $otherUser = User::factory()->create();
        $boughtItem = Item::create([
            'user_id' => $otherUser->id,
            'name' => '購入済みテスト商品',
            'price' => 2000,
            'image_path' => 'bought.jpg',
            'description' => 'テスト用説明文',
            'condition'   => '良好',
        ]);
        Purchase::create([
            'user_id' => $this->me->id,
            'item_id' => $boughtItem->id,
            'payment_method' => 'カード払い',
            'shipping_postal_code' => '123-4567',
            'shipping_address'     => '東京都渋谷区',
            'shipping_building'    => 'テストビル',
        ]);
    }

    /**
     * プロフィールページで必要な情報が表示されているかテスト
     */
    public function test_プロフィールページに必要情報が表示されること()
    {
        $response = $this->actingAs($this->me)
            ->get(route('mypage.show'));

        $response->assertStatus(200);

        // プロフィール画像とユーザー名の確認
        $response->assertSee('テストユーザー');
        $response->assertSee('profile.jpg');

        // 出品一覧の確認（ItemSeederで作られた商品名）
        $response->assertSee('腕時計');

        //「購入一覧」を表示するためにページを遷移（クエリパラメータを追加）
        $response = $this->actingAs($this->me)
            ->get(route('mypage.show', ['page' => 'buy']));

        $response->assertStatus(200);

        // 購入一覧の確認
        $response->assertSee('購入済みテスト商品');
    }
}
