<?php

namespace App\Livewire\CommonAreaBill;

use Livewire\Component;
use Livewire\Attributes\On;

class CommonAreaBillList extends Component
{

    public $search;
    public $showAddCommonAreaBillModal = false;
    public $showFilterButton = true;

    #[On('hideAddCommonAreaBill')]
    public function hideAddCommonAreaBill()
    {
        $this->showAddCommonAreaBillModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearCommonAreaBillFilter')]
    public function clearCommonAreaBillFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('CommonAreaBill')]
    public function CommonAreaBillBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.common-area-bill.common-area-bill-list');
    }
}
