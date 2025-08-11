<?php

namespace App\Livewire\Dashboard;

use App\Models\CommonAreaBills as ModelsCommonAreaBills;
use Livewire\Attributes\On;
use Livewire\Component;

class CommonAreaBills extends Component
{
    public $commonAreaBills;
    public $selectedCommonAreaBills;
    public $showCommonAreaBillModal;

    public function showCommonAreaBill($id)
    {
        $this->selectedCommonAreaBills = ModelsCommonAreaBills::findOrFail($id);
        $this->showCommonAreaBillModal = true;
    }

    #[On('hideCommonAreaBillModal')]
    public function hideCommonAreaBillModal()
    {
        $this->showCommonAreaBillModal = false;
    }

    public function mount()
    {        
        $this->commonAreaBills = ModelsCommonAreaBills::with('billType')->where('status','unpaid')->get();
    }
    
    public function render()
    {
        return view('livewire.dashboard.common-area-bills');
    }
}
