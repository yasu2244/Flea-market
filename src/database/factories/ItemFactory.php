<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Status;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * 対象モデル
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * デフォルトの属性定義
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'name'        => $this->faker->unique()->word(),
            'brand'        => $this->faker->word(),
            'description'  => $this->faker->sentence(),
            'price'        => $this->faker->numberBetween(100, 10000),
            'image_path'   => null,
            'status_id'   => Status::first()->id,
            'is_sold'      => false,
        ];
    }
}
