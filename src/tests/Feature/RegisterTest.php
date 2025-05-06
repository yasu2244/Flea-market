<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register'); // 会員登録画面へ遷移
        $response->assertStatus(200); // 正常にアクセスできているかチェック

        $response = $this->post('/register', [
            'name'                  => '',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください',]);
    }

    /** @test */
    public function メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name'                  => 'テスト太郎',
            'email'                 => '',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください',]);
    }

    /** @test */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name'                  => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function パスワードが7文字以下の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name'                  => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => '4578',
            'password_confirmation' => '4578',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /** @test */
    public function パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name'                  => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => '123password',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /** @test */
    public function 全ての項目が入力されている場合、会員情報が登録され、ログイン画面に遷移される()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name'                  => 'テスト',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録後はログイン画面へリダイレクト
        $response->assertRedirect('/login');

        // users テーブルにレコードが存在すること
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
