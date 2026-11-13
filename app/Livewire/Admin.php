<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Admin extends Component
{
    #[Url(as: 'menu')]
    public string $activeMenu = 'beranda';

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function setMenu(string $menu): void
    {
        $this->activeMenu = $menu;
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'role' => UserRole::Admin], $this->remember)) {
            session()->regenerate();
            $this->reset(['password']);

            return;
        }

        $this->addError('email', 'Email atau password salah, atau Anda bukan Admin.');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->reset(['email', 'password', 'remember']);
    }

    public function render(): View
    {
        if (Auth::check() && Auth::user()?->role === UserRole::Admin) {
            return view('livewire.admin.dashboard');
        }

        return view('livewire.admin.login');
    }
}
