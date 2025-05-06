<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'item_id'            => Item::factory(),
            'payment_method'     => $this->faker->randomElement(['カード支払い','コンビニ払い']),
            'postal_code'        => '123-4567',
            'address'            => '東京都千代田区1-1-1',
            'building'           => 'ビル101',
            'price'              => $this->faker->numberBetween(100,10000),
            'is_completed'       => true,
            'stripe_session_id'  => null,
        ];
    }
}
