<?php

namespace Database\Seeders;

use App\Models\CheckoutRequest;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Admin user for yourself to log in with
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@email.com',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        // Generate users and equipment
        $users = User::factory(10)->create();
        $equipments = Equipment::factory(50)->create();

        // Create 30 random checkout requests linking the existing users and equipment
        CheckoutRequest::factory(30)->create([
            'user_id' => fn() => $users->random()->id,
            'equipment_id' => fn() => $equipments->random()->id,
        ]);
    }
}
