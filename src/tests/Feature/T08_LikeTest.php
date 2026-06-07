<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T08_LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. いいねアイコンを押下することによって、いいねした商品として登録することができる
     * （追加済みのアイコンは色が変化する条件も含む）
     */
    public function test_いいねアイコンを押下すると登録され合計値が増加しアイコンがピンクになる()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $me = User::where('email', 'test@example.com')->first();

        $otherUser = User::create([
            'name' => '他人',
            'email' => 'other@example.com',
            'password' => bcrypt('password')
        ]);
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => '説明',
            'condition' => '新品',
            'image_path' => 'test.jpg'
        ]);

        // 1. 押す前に一度詳細ページを開いて、普通のハートであることを確認しておく
        $beforeResponse = $this->actingAs($me)->get('/item/' . $item->id);
        $beforeResponse->assertSee('heartlogo_default.png');
        $beforeResponse->assertDontSee('heartlogo_pink.png');

        // 2. 🏃‍♂️ いいねボタン（POST）を押す！
        $response = $this->actingAs($me)->post('/item/' . $item->id . '/like');
        $response->assertRedirect('/item/' . $item->id);

        // 3. 押した後に詳細ページを開いてチェック
        $movedResponse = $this->get('/item/' . $item->id);

        // 🎯 期待挙動：合計が「1」になり、アイコンが「ピンク」に変化していること！
        $movedResponse->assertSee('1');
        $movedResponse->assertSee('heartlogo_pink.png');    // 💡ピンクがある！
        $movedResponse->assertDontSee('heartlogo_default.png'); // 💡通常版は消えた！
    }

    /**
     * 2. 再度いいねアイコンを押下することによって、いいねを解除することができる
     */
    public function test_再度いいねアイコンを押下すると解除され合計値が減少しアイコンが元に戻る()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $me = User::where('email', 'test@example.com')->first();

        $otherUser = User::create([
            'name' => '他人',
            'email' => 'other@example.com',
            'password' => bcrypt('password')
        ]);
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => '説明',
            'condition' => '新品',
            'image_path' => 'test.jpg'
        ]);

        // 1. 最初からいいねしている状態を作る
        \App\Models\Like::create([
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        // 2. ログインした状態で、商品詳細ページを開く（最初からピンクのハートであることを確認）
        $beforeResponse = $this->actingAs($me)->get('/item/' . $item->id);
        $beforeResponse->assertSee('heartlogo_pink.png');

        // 3. 🏃‍♂️ 解除ボタン（unlike宛てのPOST）を押す！
        $response = $this->actingAs($me)->post('/item/' . $item->id . '/unlike');
        $response->assertRedirect('/item/' . $item->id);

        // 4. 解除した後に、もう一度詳細ページを開き直して最終チェック
        $movedResponse = $this->actingAs($me)->get('/item/' . $item->id);

        // 🎯【ここを修正！】「1」ではなく、いいね数が「0」になったことと、普通のハートに戻ったことを確認します
        $movedResponse->assertSee('<span class="count">0</span>', false); // カウントが0になったこと
        $movedResponse->assertSee('heartlogo_default.png');             // 通常ハートに戻ったこと
        $movedResponse->assertDontSee('heartlogo_pink.png');            // ピンクのハートが消えたこと
    }
}
