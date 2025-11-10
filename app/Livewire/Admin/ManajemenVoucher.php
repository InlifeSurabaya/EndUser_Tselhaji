<?php

namespace App\Livewire\Admin;

use App\Models\Voucher;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Voucher')]
#[Layout('components.layouts.admin')]
class ManajemenVoucher extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 10;

    // Properti untuk search
    public $searchCode;

    public ?Voucher $editingVoucher = null;
    public ?Voucher $voucherToDelete = null;

    // --- Properti untuk form Tambah ---
    #[Validate('required|string|max:255|unique:vouchers,code')]
    public string $code = '';

    #[Validate('required|numeric|min:0')]
    public $discount_value = 0;

    #[Validate('required|string|in:percentage,fixed')]
    public string $discount_type = 'percentage';

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('required|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|integer|min:1')]
    public $usage_limit = null;

    #[Validate('required|boolean')]
    public bool $is_active = true;
    // --- Akhir Properti form Tambah ---


    // --- Properti untuk form Edit ---
    public string $codeDetailVoucher = '';
    public $discount_valueDetailVoucher = 0;
    public string $discount_typeDetailVoucher = 'percentage';
    public $start_dateDetailVoucher = '';
    public $end_dateDetailVoucher = '';
    public $usage_limitDetailVoucher = null;
    public bool $is_activeDetailVoucher = true;
    // --- Akhir Properti form Edit ---

    /**
     * Menginisialisasi tanggal default
     */
    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(7)->format('Y-m-d');
    }

    /**
     * Mendapatkan detail voucher untuk ditampilkan pada modal edit.
     *
     * @param int $voucherId
     * @return void
     */
    public function getVoucher(int $voucherId)
    {
        if (empty($voucherId)) {
            LivewireAlert::title('Ooops')
                ->text('Terjadi kesalahan saat mengambil detail voucher.')
                ->toast()
                ->timer(3000)
                ->warning()
                ->position('top-end')
                ->show();
            return;
        }

        $detailVoucher = Voucher::findOrFail($voucherId);

        // Set properti ...DetailVoucher
        $this->codeDetailVoucher = $detailVoucher->code;
        $this->discount_valueDetailVoucher = $detailVoucher->discount_value;
        $this->discount_typeDetailVoucher = $detailVoucher->discount_type;
        $this->start_dateDetailVoucher = $detailVoucher->start_date ? (new \DateTime($detailVoucher->start_date))->format('Y-m-d') : '';
        $this->end_dateDetailVoucher = $detailVoucher->end_date ? (new \DateTime($detailVoucher->end_date))->format('Y-m-d') : '';
        $this->usage_limitDetailVoucher = $detailVoucher->usage_limit;
        $this->is_activeDetailVoucher = $detailVoucher->is_active;

        $this->editingVoucher = $detailVoucher;
    }

    /**
     * Mengupdate data voucher yang ada di database.
     *
     * @return void
     */
    public function updateVoucher()
    {
        if (!$this->editingVoucher) {
            LivewireAlert::title('Error')
                ->text('Tidak ada voucher yang dipilih untuk diupdate.')
                ->toast()
                ->timer(3000)
                ->warning()
                ->position('top-end')
                ->show();
            return;
        }

        $rules = [
            'codeDetailVoucher' => 'required|string|max:255|unique:vouchers,code,' . $this->editingVoucher->id,
            'discount_valueDetailVoucher' => 'required|numeric|min:0',
            'discount_typeDetailVoucher' => 'required|string|in:percentage,fixed',
            'start_dateDetailVoucher' => 'required|date',
            'end_dateDetailVoucher' => 'required|date|after_or_equal:start_dateDetailVoucher',
            'usage_limitDetailVoucher' => 'nullable|integer|min:1',
            'is_activeDetailVoucher' => 'required|boolean',
        ];

        try {
            // Validasi properti ...DetailVoucher
            $this->validate($rules, [
                // Atribut kustom
                'codeDetailVoucher' => 'kode',
                'discount_valueDetailVoucher' => 'nilai diskon',
                'discount_typeDetailVoucher' => 'tipe diskon',
                'start_dateDetailVoucher' => 'tanggal mulai',
                'end_dateDetailVoucher' => 'tanggal berakhir',
                'usage_limitDetailVoucher' => 'batas penggunaan',
                'is_activeDetailVoucher' => 'status aktif',
            ]);

            $this->editingVoucher->update([
                'code' => $this->codeDetailVoucher,
                'discount_value' => $this->discount_valueDetailVoucher,
                'discount_type' => $this->discount_typeDetailVoucher,
                'start_date' => $this->start_dateDetailVoucher,
                'end_date' => $this->end_dateDetailVoucher,
                'usage_limit' => $this->usage_limitDetailVoucher,
                'is_active' => $this->is_activeDetailVoucher,
            ]);

            LivewireAlert::title('Berhasil')
                ->text('Voucher berhasil diperbarui.')
                ->toast()
                ->timer(3000)
                ->success()
                ->position('top-end')
                ->show();

            $this->clearForms();
            $this->dispatch('close-modal');

        } catch (\Illuminate\Validation\ValidationException $e) {
            LivewireAlert::title('Data Tidak Valid')
                ->text('Silakan periksa kembali data yang Anda masukkan.')
                ->toast()
                ->timer(3000)
                ->error()
                ->position('top-end')
                ->show();

            throw $e;
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Update')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->toast()
                ->timer(3000)
                ->error()
                ->position('top-end')
                ->show();

        }
    }

    /**
     * Menyimpan voucher baru ke database.
     *
     * @return void
     */
    public function saveVoucher()
    {
        try {
            // Validasi akan menggunakan properti dengan atribut #[Validate]
            $validatedData = $this->validate(attributes: [
                'code' => 'kode',
                'discount_value' => 'nilai diskon',
                'discount_type' => 'tipe diskon',
                'start_date' => 'tanggal mulai',
                'end_date' => 'tanggal berakhir',
                'usage_limit' => 'batas penggunaan',
                'is_active' => 'status aktif',
            ]);

            // 'used_count' tidak diisi saat create, biarkan default database (jika ada)
            Voucher::create($validatedData);

            LivewireAlert::title('Berhasil')
                ->text('Voucher baru berhasil ditambahkan.')
                ->toast()
                ->timer(3000)
                ->success()
                ->position('top-end')
                ->show();


            $this->clearForms();
            $this->dispatch('close-modal');

        } catch (\Illuminate\Validation\ValidationException $e) {
            LivewireAlert::title('Data Tidak Valid')
                ->text('Silakan periksa kembali data yang Anda masukkan.')
                ->toast()
                ->timer(3000)
                ->error()
                ->position('top-end')
                ->show();
            throw $e;
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Menyimpan')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->toast()
                ->timer(3000)
                ->error()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Menampilkan konfirmasi penghapusan.
     *
     * @param int $voucherId
     * @return void
     */
    public function deleteVoucherAlert(int $voucherId)
    {
        LivewireAlert::title('Hapus Voucher')
            ->text('Apakah kamu yakin ingin menghapus voucher ini?')
            ->timer(50000)
            ->warning()
            ->withConfirmButton()
            ->onConfirm('deleteVoucher', ['voucherId' => $voucherId])
            ->withCancelButton()
            ->show();
    }

    /**
     * Listener untuk event onConfirmed dari alert.
     *
     * @var array
     */
    protected $listeners = [
        'deleteVoucher'
    ];

    /**
     * Menghapus voucher dari database (soft delete).
     *
     * @param array $data
     * @return void
     */
    public function deleteVoucher($data)
    {
        try {
            $voucher = Voucher::find($data['voucherId']);

            if (!$voucher) {
                LivewireAlert::title('Ooops')
                    ->text('Tidak ada voucher yang dipilih. ')
                    ->timer(3000)
                    ->error()
                    ->position('top-end')
                    ->toast()
                    ->show();
                return;
            }

            $voucher->delete();

            LivewireAlert::title('Berhasil')
                ->text('Voucher berhasil dihapus.')
                ->timer(3000)
                ->success()
                ->position('top-end')
                ->toast()
                ->show();
            return;
        } catch (\Throwable $e) {
            LivewireAlert::title('Gagal Menghapus')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->timer(3000)
                ->error()
                ->position('top-end')
                ->toast()
                ->show();
        }
    }

    /**
     * Membersihkan state form (tambah dan edit)
     * dan mereset error validasi serta state editing.
     *
     * @return void
     */
    public function clearForms()
    {
        // Reset properti form utama (tambah)
        $this->reset(
            'code', 'discount_value', 'usage_limit'
        );
        $this->discount_type = 'percentage';
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(7)->format('Y-m-d');
        $this->is_active = true;

        // Reset properti form edit
        $this->reset(
            'codeDetailVoucher', 'discount_valueDetailVoucher', 'discount_typeDetailVoucher',
            'start_dateDetailVoucher', 'end_dateDetailVoucher', 'usage_limitDetailVoucher',
            'is_activeDetailVoucher'
        );

        // Reset properti helper
        $this->editingVoucher = null;
        $this->voucherToDelete = null;

        // Bersihkan error validasi
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Voucher::latest();

        // Filter search by code
        if ($this->searchCode) {
            $query->where('code', 'like', '%' . $this->searchCode . '%');
        }

        $vouchers = $query->paginate($this->perPage);

        return view('livewire.admin.manajemen-voucher', [
            'vouchers' => $vouchers
        ]);
    }
}
