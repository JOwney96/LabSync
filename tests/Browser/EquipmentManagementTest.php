<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EquipmentManagementTest extends DuskTestCase
{
    // =========================================================
    // CONFIGURE THESE FOR YOUR MACHINE
    // =========================================================
    private string $appUrl        = 'http://127.0.0.1:8000';
    private string $adminEmail    = 'admin@email.com';
    private string $adminPassword = 'password';
    // =========================================================

    private function login(Browser $browser): void
    {
        $browser->driver->manage()->deleteAllCookies();

        $browser
            ->visit($this->appUrl . '/login')
            ->waitFor('input[name="email"]')
            ->type('email', $this->adminEmail)
            ->type('password', $this->adminPassword)
            ->click('button[type="submit"]')
            ->waitForLocation('/admin/dashboard');
    }

    public function test_admin_can_add_new_equipment(): void
    {
        $this->browse(function (Browser $browser) {

            // Step 1-3: Open browser, go to site, log in
            $this->login($browser);

            // Step 4: Navigate to equipment page and click Add Equipment
            $browser
                ->visit($this->appUrl . '/admin/equipment')
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

    public function test_admin_can_edit_equipment_details(): void
    {
        $this->browse(function (Browser $browser) {

            // Step 1-3: Open browser, go to site, log in
            $this->login($browser);

            // Step 4: Navigate to equipment page and edit equipment details
            $browser
                ->visit($this->appUrl . '/admin/equipment')
                ->waitForText('Equipment Inventory')
                ->waitFor('tbody tr', 10)
                ->clickAtXPath('//tbody/tr[1]//button[@dusk]')
                ->waitFor('[dusk="edit-details"]', 10)
                ->click('[dusk="edit-details"]')
                ->waitForText('Edit Equipment');

            // Step 7: Update some fields
            $browser
                ->clear('input[wire\\:model="name"]')
                ->type('input[wire\\:model="name"]', 'Dusk Edited Equipment')
                ->clear('input[wire\\:model="category"]')
                ->type('input[wire\\:model="category"]', 'Dusk Category');

            // Step 8: Save
            $browser
                ->press('Save Changes')
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
}
