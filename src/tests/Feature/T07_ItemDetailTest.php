<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\CategorySeeder; // 💡カテゴリシーダーを読み込みます
use Illuminate\Foundation\Testing\RefreshDatabase;

class T07_ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品詳細ページに必要な情報がすべて表示される
     */
    public function test_商品詳細ページに必要な情報と複数カテゴリが表示される()
    {
        // 1. 各種シーダーを実行（共有してもらったCategorySeederもここで実行！）
        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);
        $this->seed(CategorySeeder::class);

        // 2. 実在する「出品者」と「コメントをくれる人」を準備
        $seller = User::create([
            'name'     => '出品者',
            'email'    => 'seller@example.com',
            'password' => bcrypt('password')
        ]);
        $commenter = User::create([
            'name'     => 'コメントした人',
            'email'    => 'user@example.com',
            'password' => bcrypt('password')
        ]);

        // 3. テスト用のモリモリ商品を1つ作成（image_path も忘れずに！）
        $item = Item::create([
            'user_id'     => $seller->id,
            'name'        => '最高級の腕時計',
            'price'       => 98000,
            'description' => 'これは素晴らしい腕時計です',
            'condition'   => '新品',
            'image_path'  => 'images/watch.jpg',
        ]);

        // 4. 💡【重複エラー対策！】シーダーで入った中から「ファッション」と「家電」を引っ張ってくる
        $cat1 = Category::where('name', 'ファッション')->first();
        $cat2 = Category::where('name', '家電')->first();

        // 商品とカテゴリを複数紐付ける（これで「複数選択されたカテゴリ」の状態が作れます）
        $item->categories()->attach([$cat1->id, $cat2->id]);

        // 5. この商品へのコメントを作成
        $item->comments()->create([
            'user_id' => $commenter->id,
            'comment' => 'この時計は防水ですか？',
        ]);

        // 6. この商品へのいいねを作成
        $item->likes()->create([
            'user_id' => $commenter->id,
        ]);

        // 🏃‍♂️ いざ、商品詳細ページを開く！（ルーティングのURLに合わせて調整してください）
        // ※ もし詳細ページのURLが「/item/商品ID」ではなく「/products/商品ID」などの場合は、ここを書き換えてくださいね！
        $response = $this->get('/item/' . $item->id);

        // 7. 🤖 ロボットに裏側のコード（HTML）を確認させる
        $response->assertStatus(200);
        $response->assertSee('最高級の腕時計');       // 商品名
        $response->assertSee('98,000');                // 価格
        $response->assertSee('これは素晴らしい腕時計です'); // 商品説明
        $response->assertSee('新品');                 // 商品の状態
        $response->assertSee('ファッション');         // 複数カテゴリ1
        $response->assertSee('家電');                 // 複数カテゴリ2
        $response->assertSee('この時計は防水ですか？'); // コメント内容
        $response->assertSee('コメントした人');       // コメントしたユーザー名
    }
}
