<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
        * Seed the application's database.
        */
    public function run(): void
    {
        // Seed the single user for EnglishOS
        User::factory()->create([
            'name' => 'Learner',
            'email' => 'learner@englishos.local',
            'password' => Hash::make('password'),
        ]);
    }
}
