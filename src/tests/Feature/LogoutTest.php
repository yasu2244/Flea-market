<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

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
