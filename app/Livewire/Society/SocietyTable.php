<?php

namespace App\Livewire\Society;

use App\Models\Society;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class SocietyTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $search;
    public $society;
    public $filterStatus;
    public $showEditSocietyModal = false;
    public $confirmDeleteSocietyModal = false;
    public $showChangePackageModal = false;
    public $showRejectionReasonModal = false;
    public $rejectionReason;

    public function showEditSociety($id)
    {
        $this->society = Society::findOrFail($id);
        $this->showEditSocietyModal = true;
    }

    #[On('hideEditSociety')]
    public function hideEditSociety()
    {
        $this->showEditSocietyModal = false;
    }

    public function showChangePackage($id)
    {
        $this->society = Society::findOrFail($id);

        $this->showChangePackageModal = true;
    }

    #[On('hideChangePackage')]
    public function hideChangePackage()
    {
        $this->showChangePackageModal = false;
        $this->reset('society');
    }

    public function showDeleteSociety($id)
    {
        $this->society = Society::findOrFail($id);
        $this->confirmDeleteSocietyModal = true;
    }

    public function deleteSociety($id)
    {
        Society::destroy($id);

        $this->confirmDeleteSocietyModal = false;
        $this->reset('society');
        $this->alert('success', __('messages.societyDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

    }

    public function confirmApprovalStatus($id, $status)
    {
        $this->society = Society::findOrFail($id);

        if ($this->society->approval_status !== 'Pending') {
            $this->alert('error', __('saas.noSocietyFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }
        
        $this->resetValidation();
        $this->reset('rejectionReason');
        
        if ($status == 'Rejected') {
            $this->showRejectionReasonModal = true;
        } else {
            $this->updateApprovalStatus($status);
        }
    }

    public function saveRejectionReason()
    {
        $this->validate([
            'rejectionReason' => 'required|string|max:255',
        ]);

        $this->society->approval_status = 'Rejected';
        $this->society->rejection_reason = $this->rejectionReason;
        $this->society->save();

        $this->showRejectionReasonModal = false;
        $this->reset('rejectionReason', 'society');
        $this->dispatch('updatedCount');
        $this->alert('success', __('messages.statusUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    private function updateApprovalStatus($status)
    {
        $this->society->approval_status = $status;
        $this->society->save();
        $this->reset('rejectionReason', 'society');
        $this->dispatch('updatedCount');
        $this->alert('success', __('messages.statusUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $query = Society::with(['package'])
        ->where(function ($q) {
            return $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('id', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        })
            ->orderByDesc('id');

            if ($this->filterStatus != 'all') {
            $query->where('approval_status', $this->filterStatus);
        }

        $query = $query->paginate(20);

        return view('livewire.society.society-table', [
           'societies' => $query
        ]);
    }
}
