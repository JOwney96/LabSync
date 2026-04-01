<?php

namespace Database\Seeders;

use App\Models\CheckoutRequest;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // spatie roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        // faculty user
        $faculty = User::factory()->create([
            'name' => 'Professor Smith',
            'email' => 'faculty@email.com',
            'password' => bcrypt('password'),
        ]);
        $faculty->assignRole($facultyRole);

        // student
        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@email.com',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole($studentRole);

        //
        $users = User::factory(10)->create();


        foreach ($users as $user) {
            $user->assignRole($studentRole);
        }

        $equipments = Equipment::factory(50)->create();


        CheckoutRequest::factory(30)->create([
            'user_id' => fn() => $users->random()->id,
            'equipment_id' => fn() => $equipments->random()->id,
        ]);
    }
}
