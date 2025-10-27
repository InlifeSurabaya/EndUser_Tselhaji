<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('History Transaction')]
class HistoryTransaction extends Component
{
    use WithPagination;


    public function render()
    {
        $user = Auth::user();

        $data = Transaction::with(['order'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.transaction.history-transaction', [
            'data' => $data,
        ]);
    }
}
