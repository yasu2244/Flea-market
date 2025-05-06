<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class StatusesSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => '良好'],
            ['name' => '目立った傷や汚れなし'],
            ['name' => 'やや傷や汚れあり'],
            ['name' => '状態が悪い'],
        ];


        foreach ($statuses as $status) {
            // 既に同名レコードがあればスキップ、なければ作成
            Status::firstOrCreate(['name' => $status['name']]);
        }
    }
}
