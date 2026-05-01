<?php

namespace Tests\Browser;

use App\Models\Equipment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ApproveLoanRequestTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_approves_request(): void
    {
        Equipment::create([
            'name' => 'Test Microscope',
            'tag_id' => 'TEST-' . uniqid(),
            'category' => 'Microscope',
            'status' => 'available',
        ]);

        $this->browse(function (Browser $browser) {

            // Student creates request
            $browser->logout()
                ->visit('/register')
                ->waitFor('form', 10)
                ->type('name', 'Student User')
                ->type('email', 'student' . uniqid() . '@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->select('role', 'student')
                ->press('REGISTER')
                ->waitForText('Lab Equipment Directory', 10)
                ->pause(2000)
                ->press('Request')
                ->waitFor('@checkout-modal', 10)
                ->type('@start-date', '2026-04-30')
                ->type('@end-date', '2026-05-02')
                ->type('@purpose', 'Testing approval request')
                ->press('@submit-request')
                ->waitForText('Pending Approval', 10);

            // Admin approves request
            $browser->logout()
                ->visit('/register')
                ->waitFor('form', 10)
                ->type('name', 'Admin User')
                ->type('email', 'admin' . uniqid() . '@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->type('admin_code', 'TAMUT-ADMIN-2026')
                ->select('role', 'admin')
                ->press('REGISTER')
                ->visit('/admin/requests')
                ->pause(3000)
                ->screenshot('admin-requests-debug');
        });
    }
}
