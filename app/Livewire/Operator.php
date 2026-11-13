<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Operator extends Component
{
    public string $nip = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'nip' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['nip' => $this->nip, 'password' => $this->password, 'role' => UserRole::Operator], $this->remember)) {
            session()->regenerate();
            $this->reset(['password']);

            return;
        }

        $this->addError('nip', 'NIP atau password salah, atau Anda bukan Operator.');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->reset(['nip', 'password', 'remember']);
    }

    public function render(): View
    {
        if (Auth::check() && Auth::user()?->role === UserRole::Operator) {
            return view('livewire.operator.dashboard');
        }

        return view('livewire.operator.login');
    }
}
