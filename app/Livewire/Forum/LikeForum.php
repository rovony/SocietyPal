<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use App\Models\Forum;

class LikeForum extends Component
{
    public Forum $forum;
    public bool $liked = false;

    public function mount(Forum $forum)
    {
        $this->forum = $forum;
        $this->liked = $forum->isLikedBy(user());
    }

    public function toggleLike()
    {
        $user = user();

        if ($this->liked) {
            $this->forum->likes()->detach($user->id);
            $this->liked = false;
        } else {
            $this->forum->likes()->attach($user->id);
            $this->liked = true;
        }

        $this->forum->refresh(); 
    }

    public function render()
    {
        return view('livewire.forum.like-forum');
    }
}
