<?php

namespace App\Livewire\UtilityBills;

use Livewire\Component;
use Livewire\Attributes\On;

class UtilityBillsList extends Component
{
    public $search;
    public $showAddUtilityBillModal = false;
    public $showFilterButton = true;


    #[On('hideAddUtilityBill')]
    public function hideAddUtilityBill()
    {
        $this->showAddUtilityBillModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearUtilityBillFilter')]
    public function clearUtilityBillFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('UtilityBill')]
    public function UtilityBillBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.utility-bills.utility-bills-list');
    }
}
