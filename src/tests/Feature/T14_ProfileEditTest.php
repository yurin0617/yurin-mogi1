<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;

class T14_ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    protected $me;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->me = User::where('email', 'test@example.com')->first();
        $this->me->markEmailAsVerified();

        // 既存のプロフィール情報を作成（これが初期値になる）
        $this->me->profile()->create([
            'name'        => '変更前の名前',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区テスト町',
            'image_path'  => 'current_profile.jpg'
        ]);
    }

    /**
     * プロフィール変更画面に現在の情報が初期値として表示されていること
     */
    public function test_プロフィール変更画面に初期値が表示されること()
    {
        $response = $this->actingAs($this->me)
            ->get(route('profile.setup')); // プロフィール編集画面のルート

        $response->assertStatus(200);

        // 1. 各項目の初期値が表示されているか確認
        // inputタグのvalue属性に値が入っているかをチェック
        $response->assertSee('value="変更前の名前"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="東京都渋谷区テスト町"', false);

        // 画像はパスが含まれているか確認
        $response->assertSee('current_profile.jpg');
    }
}
