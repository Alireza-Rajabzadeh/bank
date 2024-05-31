<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AccountAndCardSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Creating sample users...');

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        User::factory(10)->create();

        $this->call([
            AccountAndCartStatuses::class,
            AccountAndCardSeeder::class,
            TransactionTypesSeeder::class,
            TransactionStatusesSeeder::class,
        ]);
    }
}
