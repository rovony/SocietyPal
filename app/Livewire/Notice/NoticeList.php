<?php

namespace App\Livewire\Notice;

use Livewire\Attributes\On;
use Livewire\Component;

class NoticeList extends Component
{
    public $search;
    public $showAddNotice = false;
    public $showFilterButton = true;

    #[On('hideAddNotice')]
    public function hideAddNotice()
    {
        $this->showAddNotice = false;
        $this->js('window.location.reload()');
    }

    #[On('clearNoticeFilter')]
    public function clearNoticeFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideNoticeFilters')]
    public function hideNoticeFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.notice.notice-list');
    }
}
