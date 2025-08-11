<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use App\Models\Forum;
use App\Exports\ForumExport;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SocietyForumTable extends Component
{
    use LivewireAlert;

    public $search;
    public $categoryId;
    public $showEditForumModal = false;
    public $editForum;
    public $confirmDeleteForumModal = false;
    public $deleteForum;

    public function showEditForum($id)
    {
        $this->editForum = Forum::findOrFail($id);
        $this->showEditForumModal = true;
    }

    #[On('hideEditForum')]
    public function hideEditForum()
    {
        $this->showEditForumModal = false;
    }

    public function showDeleteForum($id)
    {
        $this->deleteForum = Forum::findOrFail($id);
        $this->confirmDeleteForumModal = true;
    }

    public function deleteSocietyForum($id)
    {
        Forum::destroy($id);
        $this->confirmDeleteForumModal = false;
        $this->deleteForum = '';
        $this->alert('success', __('messages.deletedSuccessfully'));
    }

    #[On('exportForum')]
    public function exportForum()
    {
        return (new ForumExport($this->search, $this->categoryId))->download('forum-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $query = Forum::query();
        if (!user_can('Show Forum')) {
            $query->where('discussion_type', 'public');

            $query->orWhereHas('users', function ($query) {
                $query->where('user_id', user()->id);
            });
        }
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }
    
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
    
        $forums = $query->withCount('replies')->withCount('likes')->latest()->paginate(10);
    
        return view('livewire.forum.society-forum-table', [
            'forums' => $forums,
        ]);
    }
}
