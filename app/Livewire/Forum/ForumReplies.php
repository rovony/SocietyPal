<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use App\Models\ForumReply;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ForumReplies extends Component
{
    use LivewireAlert;

    public $forum;
    public $reply = '';
    public $parentReplyId = null;
    public $showReplies = false;

    protected $rules = [
        'reply' => 'required|string|max:2000',
    ];

    public function submit()
    {
        $this->validate();

        ForumReply::create([
            'forum_id' => $this->forum->id,
            'user_id' => auth()->id(),
            'reply' => $this->reply,
            'parent_reply_id' => $this->parentReplyId,
        ]);

        // Reset input fields
        $this->reset(['reply', 'parentReplyId']);

        $this->alert('success', __('messages.addedSuccessfully'));
    }

    public function setReplyingTo($replyId)
    {
        $this->parentReplyId = $replyId;
        $this->reply = '';
    }

    public function deleteReply($replyId)
    {
        $reply = ForumReply::findOrFail($replyId);

        $this->deleteReplyRecursively($reply);

        $this->alert('success', __('messages.deletedSuccessfully'));
    }

    protected function deleteReplyRecursively($reply)
    {
        foreach ($reply->children as $child) {
            $this->deleteReplyRecursively($child);
        }

        $reply->delete();
    }

    public function cancelReply()
    {
        $this->reset(['parentReplyId', 'reply']);
    }

    public function toggleReplies()
    {
        $this->showReplies = !$this->showReplies;
    }

    public function render()
    {
        $replies = ForumReply::where('forum_id', $this->forum->id)->whereNull('parent_reply_id')->with(['user', 'children.user','children.children'])->latest()->get();

        return view('livewire.forum.forum-replies', [
            'replies' => $replies
        ]);
    }
    
}
