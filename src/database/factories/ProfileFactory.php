<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Faker\JapaneseProvider;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        // Faker のロケールを日本に設定
        $this->faker = \Faker\Factory::create('ja_JP');

        // カスタムプロバイダーを登録
        $this->faker->addProvider(new JapaneseProvider($this->faker));

        // 郵便番号を「###-####」形式に整形
        $postalCode = sprintf('%03d-%04d', $this->faker->numberBetween(0, 999), $this->faker->numberBetween(0, 9999));

        // 住所を日本の形式に合わせて生成
        $address = $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress();

        // 建物情報
        $building = $this->faker->secondaryAddress();

        return [
            'name'        => $this->faker->name(),
            'postal_code' => $postalCode,
            'address'     => $address,
            'building'    => $building,
            // Seeder 側でユーザー画像を設定するため、初期値は null としておく
            'profile_image' => null,
        ];
    }
}
