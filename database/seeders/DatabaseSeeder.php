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
            'no_wa' => '081234567890',
        ]);

        //Memanggil LabSeeder LabSeeder
        $this->call([
            LabSeeder::class,
    ]);
        $this->call(SchedulesTableSeeder::class);
        $this->call(LabsTableSeeder::class);
        $this->call(AssistantSchedulesTableSeeder::class);
    }
}
