<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Pesanan')]
#[Layout('components.layouts.admin')]
class ManajemenPesanan extends Component
{
    use WithPagination;

    // Properti untuk filter dan pencarian
    public string $search = '';
    public string $statusFilter = '';

    // Properti untuk menyimpan data modal detail
    public ?Order $selectedOrder;
    public ?Transaction $selectedTransaction;

    // Opsi untuk dropdown filter status
    public array $statusOptions = [
        'pending' => 'Pending',
        'success' => 'Success',
        'failed' => 'Failed',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Reset halaman pagination setiap kali filter atau search berubah.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Mengambil data order untuk ditampilkan di modal detail.
     */
    public function getOrderDetails(int $orderId)
    {
        $this->selectedOrder = Order::with(['product', 'user', 'voucher', 'transaction'])
            ->find($orderId);

        $this->selectedTransaction = $this->selectedOrder?->transaction;
    }

    /**
     * Membersihkan properti modal saat ditutup.
     */
    public function closeModal()
    {
        $this->reset('selectedOrder', 'selectedTransaction');
    }

    /**
     * Render komponen.
     */
    public function render()
    {
        $query = Order::query()
            ->with(['product', 'user'])
            ->latest();

        // Terapkan filter pencarian
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('product', function ($prodQuery) {
                        $prodQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Terapkan filter status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->paginate(10);

        return view('livewire.admin.manajemen-pesanan', [
            'orders' => $orders,
        ]);
    }
}
