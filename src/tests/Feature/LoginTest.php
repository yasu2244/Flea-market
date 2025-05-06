<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function メールアドレス未入力でバリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * @test
     */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // パスワードを空にして送信
        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => '',
        ]);

        // パスワード未入力のエラーメッセージを検証
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * @test
     */
    public function 入力情報が間違っている場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 存在しないユーザー情報で送信
        $response = $this->post('/login', [
            'email'    => 'nouser@example.com',
            'password' => 'password123',
        ]);

        // 認証失敗時のエラーメッセージを検証
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    /**
     * @test
     */
    public function 正しい情報が入力された場合、ログイン処理が実行される()
    {
        // 事前に認証済み状態を前提としないユーザーを作成
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        // セッションにエラーが発生していないこと
        $response->assertSessionHasNoErrors();

        // 認証済みユーザーが先ほど作成した$userであること
        $this->assertAuthenticatedAs($user);
    }
}
