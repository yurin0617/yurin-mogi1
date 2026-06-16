<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class T15_ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $me;

    protected function setUp(): void
    {
        parent::setUp();
        $this->me = User::factory()->create();
        $this->me->markEmailAsVerified();
    }

    /**
     * 商品出品情報が正しく保存されること
     */
    public function test_商品出品情報が正しく保存されること()
    {
        // ★重要：バリデーション（exists）を通過させるために、先にカテゴリーを作る
        $category = \App\Models\Category::create(['name' => 'テストカテゴリ']);

        // 1. ログインして出品ページへ
        $response = $this->actingAs($this->me)
            ->post(route('item.store'), [
                'name'        => 'テスト商品名',
                'brand'       => 'テストブランド',
                'price'       => 5000,
                'description' => 'これはテスト用の商品です。',
                'condition'   => '新品、未使用',
                'category_id' => 1,
                'image' => \Illuminate\Http\UploadedFile::fake()->create('test_image.jpg'),
                'category_ids' => [$category->id],
            ]);
        // 2. もしバリデーションエラーがあればここで詳細を表示して止める
        $response->assertSessionHasNoErrors();
        // 2. 保存処理が成功し、トップページ等にリダイレクトされるか確認


        // 3. データベースに正しく保存されているか確認
        $this->assertDatabaseHas('items', [
            'user_id'     => $this->me->id,
            'name'        => 'テスト商品名',
            'brand'       => 'テストブランド',
            'price'       => 5000,
            'description' => 'これはテスト用の商品です。',
            'condition'   => '新品、未使用',
        ]);
    }
}
