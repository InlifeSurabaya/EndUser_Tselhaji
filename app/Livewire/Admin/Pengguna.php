<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

#[Title('Pengguna')]
#[Layout('components.layouts.admin')]
class Pengguna extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';
    public $subtitle = "Manajemen Pengguna";
    public $view = 'index';

    // properti data user
    public $user_id, $email, $fullname, $gender, $birth_date, $phone, $phoneWa, $address, $avatar, $role;
    public ?string $existingAvatar = null;
    public $password, $password_confirmation;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return match ($this->view) {
            'create' => view('livewire.admin.pengguna.create'),
            'edit'   => view('livewire.admin.pengguna.edit'),
            'show'   => view('livewire.admin.pengguna.view'),
            default  => view('livewire.admin.pengguna.index', [
                'users' => User::with('userProfile')->paginate(10)
            ]),
        };
    }

    public function createPage()
    {
        $this->resetInput();
        $this->view = 'create';
    }

    public function store()
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'fullname' => 'required',
        ];

        if ($this->password) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $this->validate($rules, [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'fullname' => $this->fullname,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->resetInput();
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil ditambahkan.',
            'icon' => 'success'
        ]);
        $this->view = 'index';
    }

    public function editPage($id)
    {
        $user = User::with('userProfile')->findOrFail($id);

        $this->user_id = $user->id;
        $this->email = $user->email;
        $this->fullname = $user->userProfile->fullname;
        $this->gender = $user->userProfile->gender;
        $this->birth_date = $user->userProfile->birth_date;
        $this->phone = $user->userProfile->phone;
        $this->address = $user->userProfile->address;
        $this->existingAvatar = $user->userProfile->avatar;

        $this->view = 'edit';
    }

    public function update()
    {
        $rules = [
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'fullname' => 'required',
        ];

        if ($this->password) {
            $rules['password'] = 'min:6|confirmed';
        }

        $this->validate($rules, [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::findOrFail($this->user_id);
        $user->update(['email' => $this->email]);

        if ($this->password) {
            $user->update(['password' => Hash::make($this->password)]);
        }

        $user->userProfile->update([
            'fullname' => $this->fullname,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        // Penanganan unggahan avatar
        if ($this->avatar) {
            // Hapus avatar lama jika ada
            if ($this->existingAvatar && Storage::disk('public')->exists($this->existingAvatar)) {
                Storage::disk('public')->delete($this->existingAvatar);
            }

            // Simpan avatar baru ke folder public/avatars
            $path = $this->avatar->store('avatars', 'public');
            $user->userProfile->update(['avatar' => $path]);
            $this->existingAvatar = $path;
            $this->avatar = null;
        }

        $this->resetInput();
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data pengguna berhasil diperbarui.',
            'icon' => 'success'
        ]);
        $this->view = 'index';
    }

    public function showPage($id)
    {
        $user = User::with('userProfile')->findOrFail($id);

        $this->user_id = $user->id;
        $this->email = $user->email;
        $this->fullname = $user->userProfile->fullname;
        $this->gender = $user->userProfile->gender;
        $this->birth_date = $user->userProfile->birth_date;
        $this->phone = $user->userProfile->phone;
        $this->phoneWa = $user->userProfile->phone;
        $this->address = $user->userProfile->address;
        $this->existingAvatar = $user->userProfile->avatar;

        $this->view = 'show';
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->userProfile()->delete();
        $user->delete();

        $this->dispatch('swal', [
            'title' => 'Terhapus!',
            'text' => 'Data pengguna telah dihapus.',
            'icon' => 'success'
        ]);
    }

    private function resetInput()
    {
        $this->reset(['email', 'password', 'fullname', 'gender', 'birth_date', 'phone', 'address']);
    }
}
