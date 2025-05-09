<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Status;
use Database\Seeders\StatusesSeeder;

class PaymentMethodBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function 小計画面で変更が即時反映される()
    {
        $this->seed(StatusesSeeder::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);
        Profile::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create(['status_id' => Status::first()->id]);

        $response = $this->actingAs($user)
                        ->get(route('purchase.show', $item->id));

        $response->assertStatus(200)
                ->assertSee('支払い方法を選択')
                ->assertSee('コンビニ払い')
                ->assertSee('カード支払い');
    }
}
