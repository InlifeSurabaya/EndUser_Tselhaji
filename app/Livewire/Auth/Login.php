<?php

namespace App\Livewire\Auth;

use App\Enum\RoleEnum;
use App\Traits\LogsDeveloper;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Login')]
#[Layout('components.layouts.auth')]
class Login extends Component
{
    use LogsDeveloper;

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

                $user = Auth::user();

                // is user admin
                if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
                    return $this->redirect(route('admin.dashboard'), navigate: true);
                }

                return $this->redirect(route('index.product'), navigate: true);
            }

            LivewireAlert::title('Waduh, Gagal!')
                ->text('Email atau password anda salah!')
                ->toast()
                ->error()
                ->position('top-end')
                ->timer(3000)
                ->show();

            $this->reset();

        } catch (\Throwable $e) {
            $this->logErrorForDeveloper($e, [
                'email' => $validatedData['email'],
            ]);

            LivewireAlert::title('Waduh, Gagal!')
                ->text('Email atau password anda salah!')
                ->toast()
                ->error()
                ->position('top-end')
                ->timer(3000)
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
