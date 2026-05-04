<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\DuskTestCase;

class EquipmentManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_can_add_new_equipment(): void
    {
        $this->browse(function (Browser $browser) {
            $user = $this->createUser('Admin', 'adminTest@email.com', 'admin');

            $browser->loginAs($user);

            // Step 4: Navigate to equipment page and click Add Equipment
            $browser
                ->visit('/admin/equipment')
                ->waitForText('Equipment Inventory')
                ->waitForText('+ Add Equipment')
                ->press('+ Add Equipment')
                ->waitForText('Add Equipment'); // wait for modal title

            // Step 5: Fill out the form
            $browser
                ->waitFor('input[wire\\:model="name"]')
                ->type('input[wire\\:model="name"]', 'Dusk Test Oscilloscope')
                ->type('input[wire\\:model="tag_id"]', 'DUSK-' . time())
                ->type('input[wire\\:model="category"]', 'Automated Test')
                ->select('select[wire\\:model="status"]', 'available')
                ->type('input[wire\\:model="calibration_due"]', '12/31/2026');

            // Step 6: Submit
            $browser
                ->press('Add Equipment')
                ->waitUntilMissing('[x-show="show"]'); // wait for modal to close

            // Step 7: Verify item appears — search by tag ID to avoid pagination
            $browser
                ->waitFor('input[placeholder="Search by name or ID..."]')
                ->type('input[placeholder="Search by name or ID..."]', 'DUSK-')
                ->waitForText('Dusk Test Oscilloscope')
                ->assertSee('Dusk Test Oscilloscope')
                ->assertSee('Automated Test');
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

    public function test_admin_can_edit_equipment_details(): void
    {
        $this->browse(function (Browser $browser) {
            $user = $this->createUser('Admin', 'adminTest@email.com', 'admin');

            $browser->loginAs($user);

            // Step 4: Navigate to equipment page and edit equipment details
            $browser
                ->visit('/admin/equipment')
                ->waitForText('Equipment Inventory')
                ->waitFor('tbody tr', 10)
                ->click('#add-equipment-button');

            // Step 7: Update some fields
            $name = 'input[wire\\:model="name"]';
            $category = 'input[wire\\:model="category"]';
            $tag_id = 'input[wire\\:model="tag_id"]';

            $browser
                ->clear($name)
                ->type($name, 'Dusk Edited Equipment')
                ->clear($category)
                ->type($category, 'Dusk Category')
                ->clear($tag_id)
                ->type($tag_id, 'LAB-0095');

            // Step 8: Save
            $browser
                ->click('#save-button')
                ->waitUntilMissing('[x-show="show"]'); // wait for modal to close

            // Step 9: Verify changes appear in the table
            // Search to find it regardless of pagination
            $browser
                ->waitFor('input[placeholder="Search by name or ID..."]')
                ->type('input[placeholder="Search by name or ID..."]', 'Dusk Edited Equipment')
                ->waitForText('Dusk Edited Equipment')
                ->assertSee('Dusk Edited Equipment')
                ->assertSee('Dusk Category');
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function login(Browser $browser, User $user): void
    {
        $browser->driver->manage()->deleteAllCookies();

        $browser
            ->visit('/login')
            ->waitFor('input[name="email"]')
            ->type('email', $user->email)
            ->type('password', $user->password)
            ->click('button[type="submit"]')
            ->waitForLocation('/admin/dashboard');
    }
}
