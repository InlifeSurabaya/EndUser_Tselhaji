<div x-data="{ openDetailModal: false }"
     @keydown.escape.window="openDetailModal = false"
     class="max-w-7xl mx-auto my-7 px-4 sm:px-6 lg:px-8">

    <!-- Header Halaman -->
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-neutral-900">
            History Transaksi
        </h1>
        <p class="mt-1 text-sm text-neutral-600">
            Lihat riwayat semua transaksi yang telah Anda lakukan.
        </p>
    </header>

    <!-- Card untuk membungkus tabel -->
    <div class="flex flex-col bg-white border border-neutral-200 shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <!-- Header Tabel -->
                        <thead class="bg-neutral-100">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                Order ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                Jumlah
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                        </thead>

                        <!-- Body Tabel -->
                        <tbody class="divide-y divide-neutral-200">
                        {{-- Loop data transaksi dari Livewire --}}
                        @forelse ($data as $transaction)
                            <tr class="hover:bg-neutral-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                                    {{-- Asumsi Anda memiliki field 'invoice_number' atau 'id' --}}
                                    {{ $transaction->order->order_number ?? $transaction->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                                    {{-- Asumsi Anda memiliki field 'created_at' --}}
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                                    {{-- Asumsi Anda memiliki field 'description' --}}
                                    {{ Str::limit($transaction->order->notes ?? 'N/A', 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-800">
                                    {{-- Asumsi Anda memiliki field 'amount' --}}
                                    {{ Number::currency($transaction->net_amount, 'IDR', 'id', 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{--
                                      Styling Status Transaksi
                                      Menggunakan warna dari palette Anda:
                                      - 'success'/'paid' -> accent (biru)
                                      - 'failed'/'cancelled' -> error/primary (merah)
                                      - 'pending' -> warning (kuning - fallback ke tailwind default)
                                      - Lainnya -> neutral (abu-abu)
                                    --}}
                                    @php
                                        $status = strtolower($transaction->status ?? 'unknown');
                                        $statusClass = '';
                                        $statusText = ucfirst($status);

                                        switch ($status) {
                                            case 'success':
                                            case 'paid':
                                                $statusClass = 'bg-accent-100 text-accent-800';
                                                $statusText = 'Berhasil';
                                                break;
                                            case 'pending':
                                            case 'waiting':
                                                // --color-warning tidak punya shades, jadi kita pakai default yellow
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                $statusText = 'Menunggu';
                                                break;
                                            case 'failed':
                                            case 'cancelled':
                                            case 'expired':
                                                // --color-error adalah --color-primary
                                                $statusClass = 'bg-primary-100 text-primary-800';
                                                $statusText = 'Gagal';
                                                break;
                                            default:
                                                $statusClass = 'bg-neutral-100 text-neutral-800';
                                                break;
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        wire:click="showTransactionDetail({{ $transaction->id }})"
                                        @click="openDetailModal = true"
                                        class="text-accent-600 hover:text-accent-800"
                                    >
                                        Detail
                                    </button>

                                </td>

                            </tr>
                        @empty
                            <!-- Tampilan Jika Data Kosong (Empty State) -->
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-12 w-12 text-neutral-400"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-semibold text-neutral-800">
                                            Belum Ada Transaksi
                                        </h3>
                                        <p class="mt-1 text-sm text-neutral-500">
                                            Anda belum memiliki riwayat transaksi apapun.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($data->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200">
                {{ $data->links() }}
            </div>
        @endif
    </div>
    @include('livewire.transaction.partials.modal-detail-transaction')
</div>

