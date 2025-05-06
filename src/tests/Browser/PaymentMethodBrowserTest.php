<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Status;

class PaymentMethodBrowserTest extends DuskTestCase
{

    /** @test */
    public function 小計画面で変更が即時反映される()
    {
        $status = \App\Models\Status::firstOrCreate(['name' => '良好']);

        $user = User::factory()->verifiedWithProfile()->create();

        $item = Item::factory()->create([
            'user_id'   => $user->id,
            'status_id' => $status->id,
            'price'     => 1500,
        ]);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser
                // ログインして購入ページへ
                ->loginAs($user)
                ->visit("/purchase/{$item->id}")

                // セレクトボックスを開く
                ->click('#customSelectDisplay')

                // 「カード支払い」を選択
                ->click('#customSelectOptions li[data-value="カード支払い"]')

                // summary の支払い方法欄に反映されていること
                ->assertSeeIn('#summary-method', 'カード支払い')

                // 再度開いて「コンビニ払い」を選び直してチェック
                ->click('#customSelectDisplay')
                ->click('#customSelectOptions li[data-value="コンビニ支払い"]')
                ->assertSeeIn('#summary-method', 'コンビニ払い');
        });
    }
}
