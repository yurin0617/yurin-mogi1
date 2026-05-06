<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T01_RegisterTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_名前が入力されていない場合にエラーが発生する()
    {
        // 1. 会員登録ページを開く（教材のテスト手順1）
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. 名前を空にして送信する（教材のテスト手順2, 3）
        $response = $this->post('/register', [
            'name' => '', // 名前を空にする
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 3. 期待挙動：バリデーションエラーが発生し、メッセージが表示される
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }
}
