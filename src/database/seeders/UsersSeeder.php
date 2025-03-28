<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email' => 'test1@example.com',
            'password' => Hash::make('1234test1'),
        ]);

        User::create([
            'email' => 'test2@example.com',
            'password' => Hash::make('1234test2'),
        ]);
    }
}
