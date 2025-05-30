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
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'email' => 'test2@example.com',
            'password' => Hash::make('1234test2'),
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'email' => 'test3@example.com',
            'password' => Hash::make('1234test3'),
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);
    }
}
