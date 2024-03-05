<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        for ($i = 1; $i < 5; $i++) {
            DB::table('users')->insert([
                'name' => 'User'.$i,
                'email' => 'user'.$i.'@user.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 1, // Default role for users
                'status' => 1, // Default status for active users
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
