<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use App\Models\SocietyForumCategory;
use Livewire\Attributes\On;

class SocietyForumList extends Component
{
    public $search;
    public $showAddForum = false;
    public $categories;
    public $selectedCategoryId = null; 

    public function mount()
    {
        $this->categories = SocietyForumCategory::all();
    }

    #[On('hideAddForum')]
    public function hideAddForum()
    {
        $this->showAddForum = false;
        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.forum.society-forum-list');
    }
}
