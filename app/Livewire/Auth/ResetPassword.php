<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Reset Password')]
class ResetPassword extends Component
{
    public string $token = '';

    #[Validate('required|email|string')]
    public string $email = '';

    #[Validate('required|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Mengambil token dari URL dan email dari query string saat komponen dimuat.
     */
    public function mount(string $token)
    {
        $this->token = $token;

        $this->email = request()->query('email', '');
    }

    /**
     * Menjalankan proses reset password.
     */
    public function resetPassword()
    {

        $status = Password::broker()->reset(
            $this->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            LivewireAlert::title('Success')
                ->text('Berhasil mengubah password')
                ->toast()
                ->position('top-end')
                ->success()
                ->show();
            $this->redirect(route('login'), navigate: true);
        } else {
            LivewireAlert::title('Error')
                ->text('Gagal mengubah password')
                ->toast()
                ->error()
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
