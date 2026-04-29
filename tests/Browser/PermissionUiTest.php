<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PermissionUiTest extends DuskTestCase
{
    public function test_student_cannot_see_admin_controls(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                ->visit('/dashboard') // ✅ FIXED ROUTE
                ->waitForText('Equipment Portal', 10) // from your blade

                // Admin-only UI should NOT be visible
                ->assertDontSee('+ Add Equipment')
                ->assertDontSee('Manage all lab assets')
                ->assertDontSee('Next Calibration');
        });
    }
}
