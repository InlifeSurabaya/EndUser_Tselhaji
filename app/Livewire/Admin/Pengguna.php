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

#[Title('Pengguna')]
#[Layout('components.layouts.admin')]
class Pengguna extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $subtitle = "Manajemen Pengguna";
    public $view = 'index';

    // properti data user
    public $user_id, $email, $password, $fullname, $gender, $birth_date, $phone, $address, $avatar;

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
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'fullname' => 'required',
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

        $this->view = 'edit';
    }

    public function update()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'fullname' => 'required',
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

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data pengguna berhasil diperbarui.',
            'icon' => 'success'
        ]);
        $this->view = 'index';
    }

    public function showPage($id)
    {
        $this->user_id = $id;
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
