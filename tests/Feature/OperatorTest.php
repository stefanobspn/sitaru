<?php

use App\Enums\UserRole;
use App\Livewire\Operator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = User::query()->updateOrCreate(
        ['email' => 'admin@sitaru.test'],
        [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]
    );

    $this->operator = User::query()->updateOrCreate(
        ['nip' => '198501012010011001'],
        [
            'name' => 'Operator Sekolah',
            'email' => 'operator@sitaru.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Operator,
        ]
    );
});

it('shows the operator login form for unauthenticated users', function () {
    get('/operator')
        ->assertOk()
        ->assertSee('Login Operator')
        ->assertSee('NIP')
        ->assertSee('Password');
});

it('validates required fields on operator login', function () {
    Livewire::test(Operator::class)
        ->set('nip', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['nip' => 'required', 'password' => 'required']);
});

it('fails login with invalid password', function () {
    Livewire::test(Operator::class)
        ->set('nip', '198501012010011001')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['nip'])
        ->assertSee('NIP atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('fails login with non-existent nip', function () {
    Livewire::test(Operator::class)
        ->set('nip', '999999999999999999')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['nip'])
        ->assertSee('NIP atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('successfully logs in operator and displays the dashboard', function () {
    Livewire::test(Operator::class)
        ->set('nip', '198501012010011001')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertSee('Beranda')
        ->assertSee('Logout');

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->role)->toBe(UserRole::Operator);
});

it('shows dashboard directly when already authenticated as operator', function () {
    actingAs($this->operator);

    get('/operator')
        ->assertOk()
        ->assertSee('Beranda')
        ->assertSee('Logout');
});

it('allows operator to logout from the dashboard', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('logout')
        ->assertSee('Login Operator');

    expect(Auth::check())->toBeFalse();
});

it('prevents operator from accessing admin dashboard directly', function () {
    actingAs($this->operator);

    get('/admin')
        ->assertOk()
        ->assertSee('Login Admin')
        ->assertDontSee('Logout');
});

it('prevents admin from accessing operator dashboard directly', function () {
    actingAs($this->admin);

    get('/operator')
        ->assertOk()
        ->assertSee('Login Operator')
        ->assertDontSee('Logout');
});
