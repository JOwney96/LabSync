<?php

namespace Tests\Browser;

use App\Models\CheckoutRequest;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReturnEquipmentTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_return_equipment(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $equipment = Equipment::create([
            'name' => 'Test Microscope',
            'tag_id' => 'TEST-' . uniqid(),
            'category' => 'Microscope',
            'status' => 'in_use',
        ]);

        CheckoutRequest::create([
            'user_id' => $student->id,
            'equipment_id' => $equipment->id,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'purpose' => 'Testing return',
            'status' => 'active', // ✅ MUST be active
        ]);

        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                ->visit('/student/borrowed') // 👈 THIS PAGE
                ->waitForText('Test Microscope', 10)
                ->assertSee('Test Microscope');
        });
    }
}
