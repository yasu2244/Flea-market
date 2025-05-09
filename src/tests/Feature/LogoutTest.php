<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Http\Middleware\VerifyCsrfToken;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /**
     * @test
     */
    public function ログアウトができる()
    {
        // 事前にファクトリーでユーザーを作成＆ログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        // ログアウト後はゲスト状態になっていること
        $this->assertGuest();

        $response->assertRedirect('/');
    }
}
