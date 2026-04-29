<?php

namespace Tests\Browser;

use App\Models\Equipment;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchInventoryTest extends DuskTestCase
{
    public function test_inventory_search_filters_results(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $unique = time();

        $matchingTag = "DUSK-SEARCH-$unique";
        $hiddenTag = "DUSK-HIDDEN-$unique";

        Equipment::create([
            'name' => 'Dusk Test Microscope',
            'tag_id' => $matchingTag,
            'category' => 'Microscope',
            'status' => 'available',
        ]);

        Equipment::create([
            'name' => 'Dusk Hidden Laptop',
            'tag_id' => $hiddenTag,
            'category' => 'Laptop',
            'status' => 'available',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $matchingTag) {
            $browser->loginAs($admin)
                ->visit('/admin/equipment')
                ->waitForText('Equipment Inventory', 10)

                ->click('input[placeholder="Search by name or ID..."]')
                ->keys('input[placeholder="Search by name or ID..."]', ['{control}', 'a'])
                ->keys('input[placeholder="Search by name or ID..."]', ['{backspace}'])
                ->keys('input[placeholder="Search by name or ID..."]', $matchingTag)

                ->pause(1500)

                ->assertSee('Dusk Test Microscope')
                ->assertSee($matchingTag)
                ->assertDontSee('Dusk Hidden Laptop');
        });
    }
}
