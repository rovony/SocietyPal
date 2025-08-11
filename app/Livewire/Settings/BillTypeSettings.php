<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\BillType;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class BillTypeSettings extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshBillTypes' => 'mount'];

    public $billTypes;
    public $billType;
    public $showEditBillTypeModal = false;
    public $showAddBillTypeModal = false;
    public $confirmDeleteBillTypeModal = false;

    public function mount()
    {
        $this->billTypes = BillType::get();
    }

    public function showAddBillType()
    {
        $this->showAddBillTypeModal = true;
    }

    public function showEditBillType($id)
    {
        $this->billType = BillType::findOrFail($id);
        $this->showEditBillTypeModal = true;
    }

    public function showDeleteBillType($id)
    {
        $this->billType = BillType::findOrFail($id);
        $this->confirmDeleteBillTypeModal = true;
    }

    public function deleteBillType($id)
    {
        BillType::destroy($id);

        $this->confirmDeleteBillTypeModal = false;

        $this->billType= '';
        $this->dispatch('refreshBillTypes');
        $this->alert('success', __('messages.billTypeDeleted'));
    }

    #[On('hideEditBillType')]
    public function hideEditBillType()
    {
        $this->showEditBillTypeModal = false;
        $this->dispatch('refreshBillTypes');
    }

    #[On('hideAddBillType')]
    public function hideAddBillType()
    {
        $this->showAddBillTypeModal = false;
        $this->dispatch('refreshBillTypes');
    }

    public function render()
    {
        return view('livewire.settings.bill-type-settings');
    }
}
