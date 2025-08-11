<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use App\Models\Forum;

class ForumDetail extends Component
{
    public $forum;
    public $forumId;

    public function mount($forumId)
    {
        $this->forumId = $forumId;
        $this->forum = Forum::with('files')->findOrFail($this->forumId);
    }
    public function render()
    {
        return view('livewire.forum.forum-detail');
    }
}
