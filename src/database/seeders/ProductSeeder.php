<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Status;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'test1@example.com')->first();
        $user2 = User::where('email', 'test2@example.com')->first();

        $products = [
            // user1 の商品（上から5件）
            [
                'user' => $user1,
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'images/products/Armani+Mens+Clock.jpg',
                'status' => '良好',
            ],
            [
                'user' => $user1,
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'images/products/HDD+Hard+Disk.jpg',
                'status' => '目立った傷や汚れなし',
            ],
            [
                'user' => $user1,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'images/products/iLoveIMG+d.jpg',
                'status' => 'やや傷や汚れあり',
            ],
            [
                'user' => $user1,
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'images/products/Leather+Shoes+Product+Photo.jpg',
                'status' => '状態が悪い',
            ],
            [
                'user' => $user1,
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'images/products/Living+Room+Laptop.jpg',
                'status' => '良好',
            ],

            // user2 の商品（残り5件）
            [
                'user' => $user2,
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'images/products/Music+Mic+4632231.jpg',
                'status' => '目立った傷や汚れなし',
            ],
            [
                'user' => $user2,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'images/products/Purse+fashion+pocket.jpg',
                'status' => 'やや傷や汚れあり',
            ],
            [
                'user' => $user2,
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'images/products/Tumbler+souvenir.jpg',
                'status' => '状態が悪い',
            ],
            [
                'user' => $user2,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'images/products/Waitress+with+Coffee+Grinder.jpg',
                'status' => '良好',
            ],
            [
                'user' => $user2,
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'images/products/外出メイクアップセット.jpg',
                'status' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($products as $item) {
            $status = Status::where('name', $item['status'])->first();

            Product::create([
                'user_id' => $item['user']->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'image_path' => $item['image'],
                'status_id' => $status->id,
                'is_sold' => false,
            ]);
        }
    }
}
