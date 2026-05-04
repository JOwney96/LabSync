<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\DuskTestCase;

class PermissionUiTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_student_cannot_see_admin_controls(): void
    {
        $this->browse(function (Browser $browser) {
            $student = $this->createUser('Student', 'student1@email.com', 'student');

            $browser->loginAs($student)
                ->visit("/dashboard") // ✅ FIXED ROUTE
                ->waitForText('Equipment Portal', 10) // from your blade

                // Admin-only UI should NOT be visible
                ->assertDontSee('+ Add Equipment')
                ->assertDontSee('Manage all lab assets')
                ->assertDontSee('Next Calibration');
        });
    }

    private function createUser(string $name, string $email, string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        $user = User::createOrFirst(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make("password"),
                'role' => $roleName,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([$role]);

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
