<?php

namespace Database\Seeders;

use App\Models\Client;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Client::factory(10)->create();

        Client::factory()->create([
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);
    }
}
