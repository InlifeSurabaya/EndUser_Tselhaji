<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\UserProfile as ProfileModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithFileUploads;

    // Properti untuk data profil
    public ?string $fullname = '';

    public ?string $gender = '';

    public ?string $birth_date = '';

    public ?string $phone = '';

    public ?string $address = '';

    public $avatar; // Untuk unggahan baru

    public ?string $existingAvatar = null; // Untuk menampilkan avatar saat ini

    // Properti untuk ganti kata sandi
    public ?string $current_password = '';

    public ?string $new_password = '';

    public ?string $new_password_confirmation = '';

    protected function profileRules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'gender' => ['nullable'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Maks 2MB
        ];
    }

    protected function passwordRules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Muat data profil pengguna saat komponen dimuat.
     */
    public function mount(): void
    {
        $user = $this->getUser();
        if ($user->userProfile) {
            $this->fullname = $user->userProfile->fullname;
            $this->gender = $user->userProfile->gender;
            $this->birth_date = $user->userProfile->birth_date;
            $this->phone = $user->userProfile->phone;
            $this->address = $user->userProfile->address;
            $this->existingAvatar = $user->userProfile->avatar;
        }
    }

    /**
     * Dapatkan instance pengguna yang sedang login.
     */
    private function getUser(): User
    {
        return User::with('userProfile')->find(Auth::id());
    }

    /**
     * Simpan atau perbarui informasi profil.
     */
    public function updateProfile(): void
    {
        $this->validate($this->profileRules());

        $user = $this->getUser();
        $profileData = [
            'fullname' => $this->fullname,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'phone' => $this->phone,
            'address' => $this->address,
        ];

        // Penanganan unggahan avatar
        if ($this->avatar) {
            // Hapus avatar lama jika ada
            if ($this->existingAvatar && Storage::disk('public')->exists($this->existingAvatar)) {
                Storage::disk('public')->delete($this->existingAvatar);
            }
            // Simpan avatar baru
            $profileData['avatar'] = $this->avatar->store('avatars', 'public');
            $this->existingAvatar = $profileData['avatar'];
            $this->avatar = null;
        }

        try {
            ProfileModel::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            LivewireAlert::title('Success')
                ->timer(5000)
                ->title('Profile berhasil diperbarui!')
                ->success()
                ->show();
            //            $this->dispatch('profileUpdated');
        } catch (\Exception $e) {
            LivewireAlert::title('Error')
                ->timer(5000)
                ->title('Terjadi kesalahan saat memperbarui profil.')
                ->error()
                ->show();
        }
    }

    /**
     * Perbarui kata sandi pengguna.
     */
    public function updatePassword(): void
    {
        $this->validate($this->passwordRules());

        try {
            $user = $this->getUser();
            $user->update([
                'password' => Hash::make($this->new_password),
            ]);

            LivewireAlert::title('Success')
                ->timer(5000)
                ->title('Kata sandi berhasil diperbarui!')
                ->success()
                ->show();
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        } catch (\Exception $e) {
            LivewireAlert::title('Success')
                ->timer(5000)
                ->title('Terjadi kesalahan saat memperbarui kata sandi.')
                ->error()
                ->show();
        }
    }

    public function sendVerificationEmail()
    {
        if (Auth::user() && ! Auth::user()->hasVerifiedEmail()) {
            Auth::user()->sendEmailVerificationNotification();

            LivewireAlert::title('Success')
                ->timer(5000)
                ->title('Email verifikasi telah dikirim. Periksa inbox Anda.')
                ->success()
                ->show();
        }
    }

    public function render()
    {
        $user = $this->getUser();

        return view('livewire.user.user-profile', ['user' => $user]);
    }
}
