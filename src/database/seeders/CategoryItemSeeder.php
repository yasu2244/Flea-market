<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class CategoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $mapping = [
            1 => ['ファッション', 'メンズ', 'アクセサリー'],
            2 => ['家電'],
            3 => ['キッチン'],
            4 => ['ファッション', 'メンズ'],
            5 => ['家電'],
            6 => ['家電'],
            7 => ['ファッション', 'レディース'],
            8 => ['キッチン'],
            9 => ['キッチン', 'ハンドメイド', 'インテリア'],
            10 => ['コスメ', 'レディース'],
        ];

        foreach ($mapping as $itemId => $categoryNames) {
            $item = Item::find($itemId);

            if ($item) {
                $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');
                $item->categories()->attach($categoryIds);
            }
        }
    }
}
