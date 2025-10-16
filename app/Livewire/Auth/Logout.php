<?php

namespace App\Livewire\Auth;

use App\Traits\LogsDeveloper;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    use LogsDeveloper;
    /**
     * Mengeluarkan pengguna dari aplikasi.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function logout()
    {
        try {
            Auth::logout();
            session()->invalidate();

            session()->regenerateToken();

            return $this->redirect(route('login'), navigate: true);


        } catch (\Throwable $e) {
            $this->logErrorForDeveloper($e);
        }
    }
    public function render()
    {
        return view('livewire.auth.logout');
    }
}
