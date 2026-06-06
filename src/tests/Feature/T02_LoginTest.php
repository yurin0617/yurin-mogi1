<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User; // 最後の「正しい情報」のテストでユーザーを作るために必要です
use Illuminate\Foundation\Testing\RefreshDatabase;

class T02_LoginTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 1. メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_メールアドレスが入力されていない場合にエラーが発生する()
    {
        // 1. ログインページを開く（生存確認）
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. メールアドレスを入力せずに他の必要項目（パスワード）を入力して送信する
        $response = $this->post('/login', [
            'email' => '', // 空にする
            'password' => 'password123',
        ]);

        // 3. 期待挙動：バリデーションエラーが発生し、メッセージが表示される
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * 2. パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_パスワードが入力されていない場合にエラーが発生する()
    {
        // 1. パスワードを入力せずに他の必要項目（メールアドレス）を入力して送信する
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '', // 空にする
        ]);

        // 2. 期待挙動：バリデーションエラーが発生し、メッセージが表示される
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 3. 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_入力情報が間違っている場合にエラーが発生する()
    {
        // 1. 登録されていない情報を入力して送信する
        $response = $this->post('/login', [
            'email' => 'notfound@example.com', // 存在しないメールアドレス
            'password' => 'wrongpassword',       // 間違ったパスワード
        ]);

        // 2. 期待挙動：ログイン失敗のエラーメッセージが表示される
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /**
     * 4. 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_正しい情報が入力された場合にログイン処理が実行される()
    {
        // 1. テスト用のユーザーをあらかじめデータベースに1人作っておく（重要！）
        $user = User::factory()->create([
            'email' => 'success@example.com',
            'password' => bcrypt('password123'), // パスワードは暗号化して保存します
        ]);

        // 2. そのユーザーの正しい情報を入力してログインボタンを押す
        $response = $this->post('/login', [
            'email' => 'success@example.com',
            'password' => 'password123',
        ]);

        // 3. 期待挙動①：ログインが成功し、トップページ（/）へ移動する指示が出るか
        $response->assertRedirect('/');

        // 4. 期待挙動②：本当にログイン状態（認証済み）になっているかチェック！
        $this->assertAuthenticatedAs($user);
    }
}