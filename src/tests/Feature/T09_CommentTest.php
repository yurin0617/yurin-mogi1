<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T09_CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. ログイン済みのユーザーはコメントを送信できる
     */
    public function test_ログイン済みのユーザーはコメントを送信できコメント数が増加する()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

        $me = User::where('email', 'test@example.com')->first();
        $me->markEmailAsVerified();
        $me->profile()->create([
            'name'        => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
        ]);
        $me = $me->fresh();

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

        // コメントを送信（POST）する前に、まずロボットに詳細ページを「GET」で普通に開かせます。
        // これによって、ロボットの頭の中に「いま詳細ページ（/item/XX）を開いているぞ」という完璧な記憶（履歴）が作られます！
        $this->actingAs($me)->get('/item/' . $item->id);

        // 🏃‍♂️ その状態のまま、続けてコメントを送信（POST）します。
        // 直前に詳細ページを開いているので、コントローラーの back() は100%詳細ページを指してくれます！
        $response = $this->post('/item/' . $item->id . '/comment', [
            'comment' => 'これはテストコメントです'
        ]);

        $response->assertRedirect('/item/' . $item->id);

        // データベースに保存されているか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $me->id,
            'item_id' => $item->id,
            'comment' => 'これはテストコメントです'
        ]);

        // 詳細ページを開いて、文字の塊でコメント数をチェック！
        $movedResponse = $this->get('/item/' . $item->id);
        $movedResponse->assertSee('コメント (1)');
    }

    /**
     * 2. ログイン前のユーザーはコメントを送信できない
     */
    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);

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

        // ログインせずにコメント送信を試みる
        $response = $this->post('/item/' . $item->id . '/comment', [
            'comment' => '未ログインのコメント'
        ]);

        // データベースに保存されて「いない」ことをチェック！
        $this->assertDatabaseMissing('comments', [
            'comment' => '未ログインのコメント'
        ]);
    }

    /**
     * 3. コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_コメントが未入力の場合バリデーションエラーが発生する()
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

        // コメントを空っぽにして送信
        $response = $this->actingAs($me)->post('/item/' . $item->id . '/comment', [
            'comment' => ''
        ]);

        // システムにエラーが残っているかチェック！
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * 4. コメントが255文字以上の場合、バリデーションメッセージが表示される
     */
    public function test_コメントが255文字以上の場合バリデーションエラーが発生する()
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

        // 256文字の長いコメントを用意して送信
        $longComment = str_repeat('あ', 256);
        $response = $this->actingAs($me)->post('/item/' . $item->id . '/comment', [
            'comment' => $longComment
        ]);

        // システムにエラーが残っているかチェック！
        $response->assertSessionHasErrors(['comment']);
    }
}
