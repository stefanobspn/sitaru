<?php

use App\Enums\UserRole;
use App\Livewire\Admin;
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

it('shows the admin login form for unauthenticated users', function () {
    get('/admin')
        ->assertOk()
        ->assertSee('Login Admin')
        ->assertSee('Email')
        ->assertSee('Password');
});

it('validates required fields on admin login', function () {
    Livewire::test(Admin::class)
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email' => 'required', 'password' => 'required']);
});

it('fails login with invalid password', function () {
    Livewire::test(Admin::class)
        ->set('email', 'admin@sitaru.test')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertSee('Email atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('prevents operator user from logging in via admin login', function () {
    Livewire::test(Admin::class)
        ->set('email', 'operator@sitaru.test')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    expect(Auth::check())->toBeFalse();
});

it('successfully logs in admin and displays the dashboard', function () {
    Livewire::test(Admin::class)
        ->set('email', 'admin@sitaru.test')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertSee('Beranda')
        ->assertSee('Logout');

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->role)->toBe(UserRole::Admin);
});

it('shows dashboard directly when already authenticated as admin', function () {
    actingAs($this->admin);

    get('/admin')
        ->assertOk()
        ->assertSee('Beranda')
        ->assertSee('Logout');
});

it('allows admin to logout from the dashboard', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('logout')
        ->assertSee('Login Admin');

    expect(Auth::check())->toBeFalse();
});
