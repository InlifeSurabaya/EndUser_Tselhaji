<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Traits\LogsDeveloper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Register')]
#[Layout('components.layouts.auth')]
class Register extends Component
{
    use LogsDeveloper;

    #[Validate('required|email|unique:users,email')]
    public $email;
    #[Validate('required|confirmed|min:8|string')]
    public $password;
    #[Validate('required|string|min:8')]
    public $password_confirmation;


    public function register()
    {
        $validatedData = $this->validate();

        try {
            User::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            LivewireAlert::title('Hore! Pendaftaran berhasil!')
                ->toast()
                ->success()
                ->position('top-end')
                ->timer(3000)
                ->show();


            return $this->redirect(route('login'), navigate: true);

        } catch (\Throwable $e) {
            $this->logErrorForDeveloper($e, [
                'email' => $validatedData['email'],
            ]);

            Log::error($e->getMessage());

            LivewireAlert::title('Waduh, Gagal!')
                ->text('Gagal membuat akun baru.')
                ->toast()
                ->error()
                ->position('top-end')
                ->timer(3000)
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
