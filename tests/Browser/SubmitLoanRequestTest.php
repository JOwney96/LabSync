<?php

namespace Tests\Browser;

use App\Models\Equipment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SubmitLoanRequestTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_submit_loan_request(): void
    {
        // Create equipment
        Equipment::create([
            'name' => 'Test Microscope',
            'tag_id' => 'TEST-' . uniqid(),
            'category' => 'Microscope',
            'status' => 'available',
        ]);

        $this->browse(function (Browser $browser) {

            $browser->logout()
                ->visit('/register')

                // Wait for form to load
                ->waitFor('form', 10)

                // Fill registration
                ->type('name', 'Test User')
                ->type('email', 'test' . uniqid() . '@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->select('role', 'student')

                // Submit registration
                ->press('REGISTER')

                // Wait for dashboard
                ->waitForText('Lab Equipment Directory', 10)
                ->pause(2000)

                // Click request button
                ->press('Request')

                // Fill modal
                ->waitFor('@checkout-modal', 10)
                ->type('@start-date', '2026-04-30')
                ->type('@end-date', '2026-05-02')
                ->type('@purpose', 'Testing loan request')

                // Submit request
                ->press('@submit-request')

                // Verify result
                ->waitForText('Pending Approval', 10);
        });
    }
}
