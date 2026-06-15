<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users = [
            [
                'email' => 'superadmin@gmail.com',
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'irfanzul1808@gmail.com',
                'name' => 'Muhammad Irfan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],

        ];

        foreach ($users as $user) {
            $usr = User::updateOrCreate(
                ['email' => $user['email']],
                Arr::except($user, ['email'])
            );
        }
    }
}
