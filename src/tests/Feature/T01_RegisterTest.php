<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class T01_RegisterTest extends TestCase
{
    // テストごとにデータベースをきれいにする魔法の言葉
    use RefreshDatabase;

    /**
     * 1. 名前が入力されていない場合、バリデーションメッセージが表示される
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

    /**
     * 2. メールアドレスが入力されていない場合にエラーが発生する
     */
    public function test_メールアドレスが入力されていない場合にエラーが発生する()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '', // 空にする
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * 3. パスワードが入力されていない場合にエラーが発生する
     */
    public function test_パスワードが入力されていない場合にエラーが発生する()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '', // 空にする
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 4. パスワードが7文字以下の場合にエラーが発生する
     */
    public function test_パスワードが7文字以下の場合にエラーが発生する()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123', // 7文字
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * 5. パスワードが確認用パスワードと一致しない場合にエラーが発生する
     */
    public function test_パスワードが確認用パスワードと一致しない場合にエラーが発生する()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123', // 違うパスワード
        ]);

        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);
    }

    /**
     * 6. 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
     */
    public function test_全ての項目が正しく入力されている場合に登録されて遷移する()
    {
        // 1. データを送信して会員登録する
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 【確認1】まずはトップページ（/）へ向かう指示が出るはず
        $response->assertRedirect('/');

        // 2. トップページを実際に開く
        $followResponse = $this->get('/');

        // 【確認2】最初はメール認証画面（verification.notice）に飛ばされるはず
        $followResponse->assertRedirect(route('verification.notice'));

        // ==========================================
        // ここからが「プロフィール設定」まで追うコード！
        // ==========================================

        // 3. 今ログインしているテスト用ユーザーを一度引っ張り出す
        $user = auth()->user();

        // 4. 【魔法の1行】このユーザーのメール認証を「完了（合格）」のステータスに書き換える！
        $user->markEmailAsVerified();

        // 5. メール認証が済んだ状態で、もう一度トップページ（/）を開いてみる！
        $profileResponse = $this->actingAs($user)->get('/');

        // 【確認3】メール認証の壁を突破したので、次はプロフィール設定画面（profile.setup）に飛ばされるはず！
        $profileResponse->assertRedirect(route('profile.setup'));

        // 6. データベースに会員登録したユーザーが本当に存在するかチェック
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
    }
}
