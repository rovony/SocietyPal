<?php

namespace App\Livewire\VisitorsManagement;

use Livewire\Component;
use Livewire\Attributes\On;

class VisitorsManagementList extends Component
{
    public $search;
    public $showAddVisitorModal = false;
    public $showFilterButton = true;

    #[On('hideAddVisitor')]
    public function hideAddVisitor()
    {
        $this->showAddVisitorModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearVisitorFilter')]
    public function clearVisitorFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }
    #[On('Visitor')]
    public function VisitorBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.visitors-management.visitors-management-list');
    }
}
