<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Login')]
class Login extends Component
{
    #[Validate('required|string|exists:users,email')]
    public $email;
    #[Validate('required|string')]
    public $password;
    #[Validate('boolean')]
    public $remember = false;

    /**
     * Login method
     * @return void|null
     */
    public function login()
    {
        // Validasi data
        $validatedData = $this->validate();

        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ];

        $remember = $validatedData['remember'];

        try {
            if (Auth::attempt($credentials, $remember)) {
                request()->session()->regenerate();

                return $this->redirect('/dashboard', navigate: true);
            }

            $this->addError('email', 'Email atau password yang Anda masukkan salah.');

        } catch (\Throwable $e) {
            Log::error('Login attempt failed unexpectedly: ' . $e->getMessage());

            $this->dispatch('login-failed', message: 'Terjadi kesalahan pada server. Silakan coba lagi nanti.');
        }
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
