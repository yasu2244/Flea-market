<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Status;

class ItemsSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'test1@example.com')->first();
        $user2 = User::where('email', 'test2@example.com')->first();

        $items = [
            // user1 の商品（上から5件）
            [
                'user' => $user1,
                'name' => '腕時計',
                'brand_name'  => 'watchA',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'assets/images/items/Armani+Mens+Clock.jpg',
                'status' => '良好',
                'is_sold' => false, // 在庫あり
            ],
            [
                'user' => $user1,
                'name' => 'HDD',
                'brand_name'  => 'NIWA',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'assets/images/items/HDD+Hard+Disk.jpg',
                'status' => '目立った傷や汚れなし',
                'is_sold' => false, // 在庫あり
            ],
            [
                'user' => $user1,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'assets/images/items/iLoveIMG+d.jpg',
                'status' => 'やや傷や汚れあり',
                'is_sold' => true, // 売り切れ
            ],
            [
                'user' => $user1,
                'name' => '革靴',
                'brand_name'  => 'shoesB',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'assets/images/items/Leather+Shoes+Product+Photo.jpg',
                'status' => '状態が悪い',
                'is_sold' => false,
            ],
            [
                'user' => $user1,
                'name' => 'ノートPC',
                'brand_name'  => 'HURUTA',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'assets/images/items/Living+Room+Laptop.jpg',
                'status' => '良好',
                'is_sold' => false,
            ],

            // user2 の商品（残り5件）
            [
                'user' => $user2,
                'name' => 'マイク',
                'brand_name'  => 'オーディオC',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'assets/images/items/Music+Mic+4632231.jpg',
                'status' => '目立った傷や汚れなし',
                'is_sold' => false,
            ],
            [
                'user' => $user2,
                'name' => 'ショルダーバッグ',
                'brand_name'  => 'Bag-D',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'assets/images/items/Purse+fashion+pocket.jpg',
                'status' => 'やや傷や汚れあり',
                'is_sold' => false,
            ],
            [
                'user' => $user2,
                'name' => 'タンブラー',
                'brand_name'  => '家具ショップE',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'assets/images/items/Tumbler+souvenir.jpg',
                'status' => '状態が悪い',
                'is_sold' => true,
            ],
            [
                'user' => $user2,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'assets/images/items/Waitress+with+Coffee+Grinder.jpg',
                'status' => '良好',
                'is_sold' => true,
            ],
            [
                'user' => $user2,
                'name' => 'メイクセット',
                'brand_name'  => 'コスメティックスF',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'assets/images/items/外出メイクアップセット.jpg',
                'status' => '目立った傷や汚れなし',
                'is_sold' => false,
            ],
        ];

        foreach ($items as $item) {
            $status = Status::where('name', $item['status'])->first();

            Item::create([
                'user_id' => $item['user']->id,
                'name' => $item['name'],
                'brand' => $item['brand_name'] ?? null,
                'price' => $item['price'],
                'description' => $item['description'],
                'image_path' => $item['image'],
                'status_id' => $status->id,
                'is_sold' => $item['is_sold'],
            ]);
        }
    }
}
