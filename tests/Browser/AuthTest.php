<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LabSyncAuthTest extends DuskTestCase
{
    use DatabaseTruncation;

    private const PASSWORD = 'test1234';

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function testValidLoginAdmin(): void
    {
        $this->createUser('Admin QA', 'adminqa@email.com', 'admin');

        $this->browse(function (Browser $browser) {
            $this->logIn($browser, 'adminqa@email.com')
                ->waitForLocation('/admin/dashboard', 10)
                ->assertPathIs('/admin/dashboard');
        });
    }

    public function testValidLoginStudent(): void
    {
        $this->createUser('Student QA', 'studentqa@email.com', 'student');

        $this->browse(function (Browser $browser) {
            $this->logIn($browser, 'studentqa@email.com')
                ->waitForLocation('/dashboard', 10)
                ->assertPathIs('/dashboard');
        });
    }

    public function testInvalidLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                ->waitFor('#email')
                ->type('#email', 'wrong@example.edu')
                ->type('#password', 'badpassword')
                ->pause(300)
                ->click('@login-button')
                ->waitForText('These credentials do not match our records.', 10)
                ->assertSee('These credentials do not match our records.');
        });
    }

    public function testLogoutAdmin(): void
    {
        $this->createUser('Admin QA', 'adminqa@email.com', 'admin');

        $this->browse(function (Browser $browser) {
            $this->logIn($browser, 'adminqa@email.com')
                ->waitForLocation('/admin/dashboard');

            $this->submitLogout($browser);

            $browser->waitForLocation('/register', 10)
                ->assertPathIs('/register');
        });
    }

    public function testLogoutStudent(): void
    {
        $this->createUser('Student QA', 'studentqa@email.com', 'student');

        $this->browse(function (Browser $browser) {
            $this->logIn($browser, 'studentqa@email.com')
                ->waitForLocation('/dashboard');

            $this->submitLogout($browser);

            $browser->waitForLocation('/register', 10)
                ->assertPathIs('/register');
        });
    }

    private function createUser(string $name, string $email, string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'role' => $roleName,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([$role]);

        return $user;
    }

    private function logIn(Browser $browser, string $email): Browser
    {
        $browser->driver->manage()->deleteAllCookies();

        return $browser->visit('/login')
            ->waitFor('#email')
            ->click('#email')
            ->type('#email', $email)
            ->click('#password')
            ->type('#password', self::PASSWORD)
            ->pause(500)
            ->click('@login-button');
    }

    private function submitLogout(Browser $browser): void
    {
        $browser->script(<<<'JS'
const form = document.createElement('form');
form.method = 'POST';
form.action = '/logout';

const token = document.querySelector('meta[name="csrf-token"]').content;
const input = document.createElement('input');
input.type = 'hidden';
input.name = '_token';
input.value = token;

form.appendChild(input);
document.body.appendChild(form);
form.submit();
JS);
    }
}
