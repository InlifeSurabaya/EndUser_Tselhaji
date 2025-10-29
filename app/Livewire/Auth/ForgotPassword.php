<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Forgot Password')]
class ForgotPassword extends Component
{
    #[Validate('required|email|string')]
    public string $email = '';

    /**
     * Mengirim link reset password.
     */
    public function sendResetLink()
    {

        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            LivewireAlert::title('Link Reset Terkirim!')
                ->text('Kami telah mengirimkan email berisi tautan untuk mengatur ulang password Anda. Silakan cek inbox atau folder spam Anda.')
                ->success()
                ->show();

            $this->reset();
        } else {
            LivewireAlert::title('Gagal Mengirim Link')
                ->text('Terjadi masalah. Pastikan email yang Anda masukkan benar dan terdaftar.')
                ->error()
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
