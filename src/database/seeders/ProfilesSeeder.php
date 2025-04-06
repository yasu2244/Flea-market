<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class ProfilesSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'test1@example.com')->first();
        $user2 = User::where('email', 'test2@example.com')->first();

        Profile::factory()->create([
            'user_id' => $user1->id,
            'profile_image' => 'assets/images/items/Test_User1.jpg',
        ]);

        Profile::factory()->create([
            'user_id' => $user2->id,
            'profile_image' => 'assets/images/items/Test_User2.jpg',
        ]);
    }
}
