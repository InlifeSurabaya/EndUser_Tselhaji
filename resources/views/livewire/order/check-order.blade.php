<div class="max-w-2xl mx-auto my-7 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
        <div class="p-6 sm:p-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-[var(--color-neutral-900)]">
                    Cek Status Pembayaran
                </h1>
                <p class="mt-2 text-sm text-[var(--color-neutral-600)]">
                    Masukkan payment reference dari Laravel B
                </p>
            </div>

            <!-- Form Pencarian -->
            <form class="mt-8 space-y-4" onsubmit="checkPaymentStatus(event)">
                <div>
                    <label for="paymentReference" class="block text-sm font-medium mb-2">
                        Payment Reference
                    </label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <i class="fas fa-receipt text-gray-400"></i>
                        </div>
                        <input type="text" id="paymentReference"
                               class="py-3 ps-11 pe-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Contoh: PAY-20251231-001"
                               required>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                    <span id="submitText">Cek Status</span>
                    <span id="loadingText" class="hidden">
                        <span class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-white rounded-full"></span>
                        Mencari...
                    </span>
                </button>
            </form>

            <!-- Hasil Pencarian -->
            <div class="mt-8">
                <div id="resultContainer" class="hidden">
                    <!-- Result akan diisi oleh JavaScript -->
                </div>

                <div id="noResult" class="text-center border-2 border-dashed border-gray-300 rounded-lg p-8">
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <p class="text-sm text-gray-500">
                        Masukkan payment reference untuk mengecek status
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkPaymentStatus(event) {
    event.preventDefault();

    const paymentRef = document.getElementById('paymentReference').value.trim();
    if (!paymentRef) return;

    // Show loading
    document.getElementById('submitText').classList.add('hidden');
    document.getElementById('loadingText').classList.remove('hidden');

    // Fetch from Laravel B
    fetch(`http://localhost:8001/api/transactions/${paymentRef}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('submitText').classList.remove('hidden');
            document.getElementById('loadingText').classList.add('hidden');

            if (data.success) {
                showPaymentResult(data.data);
            } else {
                showNotFound();
            }
        })
        .catch(error => {
            document.getElementById('submitText').classList.remove('hidden');
            document.getElementById('loadingText').classList.add('hidden');
            showError();
        });
}

function showPaymentResult(transaction) {
    const statusMap = {
        'pending': { text: 'Menunggu Pembayaran', class: 'bg-yellow-100 text-yellow-800' },
        'settlement': { text: 'Pembayaran Berhasil', class: 'bg-green-100 text-green-800' },
        'failed': { text: 'Gagal', class: 'bg-red-100 text-red-800' }
    };

    const statusInfo = statusMap[transaction.status] || { text: transaction.status, class: 'bg-gray-100 text-gray-800' };

    let html = `
        <div class="border-2 border-dashed border-blue-200 rounded-lg p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-800">Payment Reference</p>
                    <h3 class="text-lg font-bold text-gray-900 mt-1">${transaction.transaction_number}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Created: ${new Date(transaction.created_at).toLocaleString()}
                    </p>
                </div>
                <div class="mt-3 sm:mt-0">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium ${statusInfo.class}">
                        ${statusInfo.text}
                    </span>
                </div>
            </div>

            <hr class="my-4 border-dashed border-gray-300">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Amount</p>
                    <p class="text-lg font-bold text-gray-800">Rp ${transaction.gross_amount.toLocaleString()}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Payment Type</p>
                    <p class="text-lg font-bold text-gray-800">${transaction.payment_type}</p>
                </div>
            </div>

            ${transaction.settlement_time ? `
            <div class="mt-4">
                <p class="text-xs text-gray-500">Settlement Time</p>
                <p class="text-sm font-medium text-gray-700">${new Date(transaction.settlement_time).toLocaleString()}</p>
            </div>
            ` : ''}

            ${transaction.approval_data ? `
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500">Approval Info</p>
                <p class="text-sm font-medium text-gray-700">
                    Approved by: ${transaction.approval_data.approved_by || 'N/A'}
                </p>
            </div>
            ` : ''}
        </div>
    `;

    document.getElementById('noResult').classList.add('hidden');
    document.getElementById('resultContainer').innerHTML = html;
    document.getElementById('resultContainer').classList.remove('hidden');
}

function showNotFound() {
    const html = `
        <div class="text-center border-2 border-dashed border-gray-300 rounded-lg p-8">
            <i class="fas fa-times-circle text-red-400 text-4xl mb-4"></i>
            <h3 class="mt-4 text-lg font-semibold text-gray-800">Payment Reference Tidak Ditemukan</h3>
            <p class="mt-1 text-sm text-gray-600">
                Pastikan payment reference yang dimasukkan sudah benar.
            </p>
        </div>
    `;

    document.getElementById('noResult').classList.add('hidden');
    document.getElementById('resultContainer').innerHTML = html;
    document.getElementById('resultContainer').classList.remove('hidden');
}

function showError() {
    const html = `
        <div class="text-center border-2 border-dashed border-gray-300 rounded-lg p-8">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-4"></i>
            <h3 class="mt-4 text-lg font-semibold text-gray-800">Gagal Terhubung ke Server</h3>
            <p class="mt-1 text-sm text-gray-600">
                Silakan coba lagi beberapa saat.
            </p>
        </div>
    `;

    document.getElementById('noResult').classList.add('hidden');
    document.getElementById('resultContainer').innerHTML = html;
    document.getElementById('resultContainer').classList.remove('hidden');
}
</script>
