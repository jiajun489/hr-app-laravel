<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat 1 user test default
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Panggil HRSeeder
        $this->call([
            HRSeeder::class,
        ]);
    }
}
