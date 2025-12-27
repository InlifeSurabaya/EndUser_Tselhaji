<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\UserBan;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

#[Title('Pengguna')]
#[Layout('components.layouts.admin')]
class Pengguna extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public string $subtitle = 'Manajemen Pengguna';
    public string $view = 'index';

    // Properti utama untuk paginasi dan filter
    public int $perPage = 10;
    public ?string $nameItem = null;
    public ?string $roleFilter = null;
    public ?string $banFilter = null;

    // Properti data user
    public ?int $user_id = null;
    public string $email = '';
    public string $fullname = '';
    public string $gender = '';
    public ?string $birth_date = null;
    public string $phone = '';
    public string $waphone = '';
    public string $address = '';
    public ?string $role = null;
    public ?string $existingAvatar = null;
    public string $is_ban = '';
    public string $reason = '';

    public $avatar;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    /**
     * Render tampilan utama komponen pengguna.
     */
    public function render()
    {
        $query = User::with(['userProfile', 'roles', 'ban'])->latest();

        // Filter pencarian berdasarkan nama
        if ($this->nameItem) {
            $query->whereHas(
                'userProfile',
                fn($q) =>
                $q->where('fullname', 'like', '%' . $this->nameItem . '%')
            );
        }

        // Filter berdasarkan role
        if ($this->roleFilter) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', $this->roleFilter);
            });
        }

        // Filter status ban
        if ($this->banFilter === 'banned') {
            $query->whereHas('ban', function ($q) {
                $q->where('is_ban', 1);
            });
        }

        if ($this->banFilter === 'active') {
            $query->whereDoesntHave('ban')
                ->orWhereHas('ban', function ($q) {
                    $q->where('is_ban', 0);
                });
        }

        $users = $query->paginate($this->perPage);

        return match ($this->view) {
            'create' => view('livewire.admin.pengguna.create'),
            'edit'   => view('livewire.admin.pengguna.edit'),
            'show'   => view('livewire.admin.pengguna.view'),
            default  => view('livewire.admin.pengguna.index', compact('users')),
        };
    }

    /**
     * Buka halaman tambah pengguna baru.
     */
    public function createPage(): void
    {
        $this->clearForms();
        $this->view = 'create';
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(): void
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'fullname' => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:superadmin,user',
        ];

        $messages = [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ];

        try {
            $this->validate($rules, $messages);

            $user = User::create([
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->role);

            UserProfile::create([
                'user_id' => $user->id,
                'fullname' => $this->fullname,
                'gender' => $this->gender,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);

            LivewireAlert::title('Berhasil')
                ->text('Pengguna berhasil ditambahkan.')
                ->success()->toast()->position('top-end')->show();

            $this->clearForms();
            $this->view = 'index';
        } catch (ValidationException $e) {
            LivewireAlert::title('Data Tidak Valid')
                ->text('Periksa kembali data yang Anda masukkan.')
                ->error()->toast()->position('top-end')->show();
            throw $e;
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Menyimpan')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->error()->toast()->position('top-end')->show();
        }
    }


    /**
     * Buka halaman edit pengguna.
     */
    public function editPage(int $id): void
    {
        $user = User::with('userProfile', 'roles')->findOrFail($id);

        $this->user_id = $user->id;
        $this->email = $user->email;

        $profile = $user->userProfile;

        $this->fullname = $profile->fullname ?? '';
        $this->gender = $profile->gender ?? '';
        $this->birth_date = $profile->birth_date ?? '';
        $this->phone = $profile->phone ?? '';
        $this->waphone = $profile->waphone ?? '';
        $this->address = $profile->address ?? '';
        $this->existingAvatar = $profile->avatar ?? null;
        $this->role = $user->roles->first()?->name;

        $this->view = 'edit';
    }

    /**
     * Update data pengguna di database.
     */
    public function update(): void
    {
        $rules = [
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'fullname' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'role' => 'required|in:superadmin,user',
        ];

        if ($this->password) {
            $rules['password'] = 'min:6|confirmed';
        }

        $messages = [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ];

        try {
            $this->validate($rules, $messages);

            $user = User::findOrFail($this->user_id);
            $user->update(['email' => $this->email]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            $user->userProfile->update([
                'fullname' => $this->fullname,
                'gender' => $this->gender,
                'birth_date' => $this->birth_date ?: null,
                'phone' => $this->phone,
                'waphone' => $this->waphone,
                'address' => $this->address,
            ]);

            // Penanganan avatar
            if ($this->avatar) {
                if ($this->existingAvatar && Storage::disk('public')->exists($this->existingAvatar)) {
                    Storage::disk('public')->delete($this->existingAvatar);
                }

                $path = $this->avatar->store('avatars', 'public');
                $user->userProfile->update(['avatar' => $path]);
                $this->existingAvatar = $path;
                $this->avatar = null;
            }

            $user->syncRoles([$this->role]);

            LivewireAlert::title('Berhasil')
                ->text('Data pengguna berhasil diperbarui.')
                ->success()->toast()->position('top-end')->show();

            $this->clearForms();
            $this->view = 'index';
        } catch (ValidationException $e) {
            LivewireAlert::title('Data Tidak Valid')
                ->text('Periksa kembali data yang Anda masukkan.')
                ->error()->toast()->position('top-end')->show();
            throw $e;
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Update')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->error()->toast()->position('top-end')->show();
        }
    }

    public function confirmUpdate(string $password)
    {
        // Cek password admin yang sedang login
        if (!Hash::check($password, auth()->user()->password)) {
            LivewireAlert::title('Password Salah')
                ->text('Password yang Anda masukkan tidak sesuai.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();

            return;
        }

        // Jika password benar → lanjut update
        $this->update();
    }


    /**
     * Menampilkan detail pengguna.
     */
    public function showPage(int $id): void
    {
        $user = User::with('userProfile', 'roles', 'ban')->findOrFail($id);

        $this->user_id = $user->id;
        $this->email = $user->email;

        $profile = $user->userProfile;
        $ban = $user->ban;

        $this->fullname = $profile->fullname ?? '';
        $this->gender = $profile->gender ?? '';
        $this->birth_date = $profile->birth_date ?? '';
        $this->phone = $profile->phone ?? '';
        $this->waphone = $profile->waphone ?? '';
        $this->address = $profile->address ?? '';
        $this->existingAvatar = $profile->avatar ?? null;
        $this->role = $user->roles->first()?->name;
        $this->is_ban = $ban->is_ban ?? '';
        $this->reason = $ban->reason ?? '';

        $this->view = 'show';
    }

    /**
     * Menghapus data pengguna.
     */
    public function delete(int $id): void
    {
        try {
            $user = User::findOrFail($id);
            $user->userProfile()->delete();
            $user->delete();

            LivewireAlert::title('Terhapus')
                ->text('Data pengguna telah dihapus.')
                ->success()->toast()->position('top-end')->show();
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Hapus')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->error()->toast()->position('top-end')->show();
        }
    }

    /**
     * Ban User.
     */
    public function banUser(int $userId, string $reason): void
    {
        try {
            $user = User::findOrFail($userId);

            UserBan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'is_ban' => '1',
                    'reason' => $reason,
                ]
            );

            LivewireAlert::title('User Dibanned')
                ->text('User berhasil diblokir.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Ban User')
                ->text($e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Unban User.
     */
    public function unbanUser(int $userId, string $reason): void
    {
        try {
            $ban = UserBan::where('user_id', $userId)->first();

            if ($ban) {
                $ban->update([
                    'is_ban' => 0,
                    'reason' => $reason,
                ]);
            }

            LivewireAlert::title('User Unbanned')
                ->text('User berhasil diaktifkan kembali.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Unban User')
                ->text($e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Reset form dan error validasi.
     */
    public function clearForms(): void
    {
        $this->reset([
            'email',
            'password',
            'password_confirmation',
            'fullname',
            'gender',
            'birth_date',
            'phone',
            'waphone',
            'address',
            'avatar',
            'existingAvatar',
            'user_id'
        ]);

        $this->resetErrorBag();
    }
}
