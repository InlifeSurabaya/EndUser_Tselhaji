<?php

namespace App\Livewire\UserReference;

use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Title('Bantu kami mengenali anda')]
#[Layout('components.layouts.app')]
class CreateUserReference extends Component
{
    // Preefrence user
    public $plannedBudget;
    public $plannedDuration;
    public $plannedQuota;

    public function createUserPreference()
    {
        $userPreference = UserPreference::create([
            'user_id' => Auth::id(),
            'planned_budget' => $this->plannedBudget,
            'planned_duration' => $this->plannedDuration,
            'planned_quota' => $this->plannedQuota,
        ]);

        $userPreference->user->update([
            'is_new' => 0
        ]);

        LivewireAlert::title('Berhasil')
            ->text('Terimakasih dengan ini kami lebih mengenali anda.')
            ->success()
            ->timer(2500)
            ->withConfirmButton()
            ->onConfirm('goToIndexProduct')
            ->show();
    }

    public function goToIndexProduct()
    {
        return $this->redirect(route('index.product'), navigate: true);
    }

    public function render()
    {
        return view('livewire.user-reference.create-user-reference');
    }
}
