<?php

namespace App\Livewire\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Verify Email Anda')]
#[Layout('components.layouts.auth')]
class VerifyEmail extends Component
{
    public function sendVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        LivewireAlert::title('Email Verifikasi Terkirim')
            ->text('Tautan verifikasi baru telah dikirimkan ke alamat email Anda. Silakan periksa kotak masuk Anda.')
            ->success()
            ->timer(4000)
            ->show();
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
