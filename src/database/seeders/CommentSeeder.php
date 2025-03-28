<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        $users = User::all();
        $items = Item::all();

        // ユーザーまたは商品がいなければ処理をスキップ
        if ($users->isEmpty() || $items->isEmpty()) {
            $this->command->warn('コメントシーディングをスキップ：ユーザーまたは商品が存在しません。');
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            Comment::create([
                'user_id' => $users->random()->id,
                'item_id' => $items->random()->id,
                'content' => $faker->realText(50),
            ]);
        }
    }
}
