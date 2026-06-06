<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T03_LogoutTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 1. ログアウトができる
     */
    public function test_ログアウトができる()
    {
        // 1. テスト用の使い捨てユーザーを1人データベースに作る
        $user = User::factory()->create();

        // 2. ユーザーにログインをする（$user さんになりきって、ログイン状態でスタート！）
        // 3. ログアウトボタンを押す（/logout に POST 送信する）
        $response = $this->actingAs($user)->post('/logout');

        // 4. 期待挙動①：ログアウト後、トップページ（/）に移動する指示が出るか
        $response->assertRedirect('/');

        // 5. 期待挙動②：本当にログアウト状態（未認証の一般人）になっているかチェック！
        $this->assertGuest();
    }
}
